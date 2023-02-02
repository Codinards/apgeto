<?php

namespace App\Controller\Backend\Accounts;

use App\Form\Main\Funds\UpdateFundType;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Exceptions\AccountManagerException;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Users\User;
use App\Entity\Utils\CashInFlows;
use App\Entity\Utils\CashOutFlows;
use App\Entity\Utils\SeveralCashInFlows;
use App\Entity\Utils\SeveralCashOutFlows;
use App\Entity\Utils\SeveralFundsOperations\MultiCashInFlows;
use App\Entity\Utils\SeveralFundsOperations\MultiCashOutFlows;
use App\Events\Assistance\DeleteContributorFundEvent;
use App\Events\Assistance\UpdateContributorFundEvent;
use App\Events\DebtRenewal\UpdateDebtRenewalEvent;
use App\Events\DebtRenewal\DeleteDebtRenewalEvent;
use App\Events\Fund\CreateFundEvent;
use App\Events\Fund\DeleteFundEvent;
use App\Events\Fund\DeleteLinkedDebtRenewalEvent;
use App\Events\Fund\UpdateFundEvent;
use App\Events\Fund\UpdateLinkedDebtRenewalEvent;
use App\Events\Operation\DeleteOperationMemberFundEvent;
use App\Events\Operation\UpdateOperationMemberFundEvent;
use App\Form\Main\Funds\DeleteFundType;
use App\Form\Utils\CashInFlowsType;
use App\Form\Utils\CashOutFlowsType;
use App\Form\Utils\SeveralCashInFlowsType;
use App\Form\Utils\SeveralCashOutFlowsType;
use App\Form\Utils\SeveralFundOperations\MultiCashInFlowsType;
use App\Form\Utils\SeveralFundOperations\MultiCashOutFlowsType;
use App\Tools\Entity\AccountManager;
use App\Tools\Entity\TotalResolver;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/account/fund")
 * @Module(name="account", is_activated=true)
 */
class FundController extends AdminBaseController
{
    use HandleAccountFlow;

    protected $viewPath = 'account/fund/';


    /**
     * @Route("/{_locale}/{id}-show-{year}", name="app_backend_fund_show", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+", "year":"\d{4}"})
     * @RouteAction(name="app_backend_fund_show", title="fund.show.action", targets={"admins"})
     */
    public function show(Request $request, Account $account, bool $called = false, ?int $year = null): Response
    {
        $year = $this->getYear($year);
        $fundData = $this->getFundRepository()->findBy(['year' => $year, 'account' => $account]);
        $oldBalance = $this->getFundRepository()->findLast(['account' => $account, 'year' => ($year - 1)]);
        $totals = (new TotalResolver)->resolve($fundData, 'fund', $oldBalance);
        $years = $this->collection($this->getFundRepository()->findYears())
            ->reverse()
            ->map(function ($item) {
                return $item['year'];
            })->add($this->getYear())->unique();
        return $this->render('show.html.twig', [
            'account' => $account,
            'totals' => $totals,
            'user' => $account->getUser(),
            'funds' => $year !== 2021 ? $this->collection($fundData)->sortBy(fn (Fund $fund) => $fund->getCreatedAt()) : $fundData,
            'count' => count($fundData),
            'oldBalance' => $oldBalance,
            'years' => $years,
            'year' => $year,
            'called' => $called
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-inflow", name="app_backend_fund_cashinflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_fund_cashinflows", title="fund.cashinflows.action", targets={"admins"})
     */
    public function cashInFlows(Request $request, Account $account, AccountManager $accountManager): Response
    {
        $createdAt = new \DateTime();
        $cashInFlows = (new CashInFlows())
            ->setAccount($account)
            ->setUser($account->getUser())
            ->setAdmin($this->getUser())
            ->setCreatedAt($createdAt);
        $form = $this->createForm(CashInFlowsType::class, $cashInFlows);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $fund = $accountManager->ManageCashInFlows($cashInFlows);
            $this->getManager()->persist($fund);
            $this->dispatcher->dispatch(new CreateFundEvent($fund));
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_fund_show', ['id' => $account->getId()]);
        }
        return $this->render('cash_in_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $cashInFlows,
            'user' => $account->getUser(),
            'account' => $account
        ]);
    }


    /**
     * @Route("/{_locale}/several-inflow", name="app_backend_fund_several_cashinflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_fund_several_cashinflows", title="Faire un entrée de fond multiple le même montant", targets={"admins"})
     */
    public function severalCashInFlows(Request $request, AccountManager $accountManager): Response
    {
        $severalCashInFlows = (new SeveralCashInFlows())
            ->setAdmin($this->getUser());
        $form = $this->createForm(SeveralCashInFlowsType::class, $severalCashInFlows);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            /** @var User $target */
            foreach ($severalCashInFlows->getTargets() as $target) {

                $fund = $accountManager->ManageCashInFlows(
                    (new CashInFlows)
                        ->setUser($target)
                        ->setAccount($target->getAccount())
                        ->setAdmin($this->getUser())
                        ->setWording($severalCashInFlows->getWording())
                        ->setCreatedAt($severalCashInFlows->getCreatedAt())
                        ->setCashInFlows($severalCashInFlows->getCashInFlows())
                        ->setObservations($severalCashInFlows->getObservations())
                );

                $this->getManager()->persist($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_account_index');
        }
        return $this->render('several_cash_in_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $severalCashInFlows,
        ]);
    }

    /**
     * @Route("/{_locale}/several-outflow", name="app_backend_fund_several_cashoutflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_fund_several_cashoutflows", title="Faire un sortie de fond multiple avec le même montant", targets={"admins"})
     */
    public function severalCashOutFlows(Request $request, AccountManager $accountManager): Response
    {
        $severalCashOutFlows = (new SeveralCashOutFlows())
            ->setAdmin($this->getUser());
        $form = $this->createForm(SeveralCashOutFlowsType::class, $severalCashOutFlows);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $funds = [];
            /** @var User $target */
            foreach ($severalCashOutFlows->getTargets() as $target) {

                $funds[] = $accountManager->ManageCashOutFlows(
                    (new CashOutFlows)
                        ->setUser($target)
                        ->setAccount($target->getAccount())
                        ->setAdmin($this->getUser())
                        ->setWording($severalCashOutFlows->getWording())
                        ->setCreatedAt($severalCashOutFlows->getCreatedAt())
                        ->setCashOutFlows($severalCashOutFlows->getCashOutFlows())
                        ->setObservations($severalCashOutFlows->getObservations())
                );
            }
            foreach ($funds as $fund) {
                $this->getManager()->persist($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_account_index');
        }
        return $this->render('several_cash_out_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $severalCashOutFlows,
        ]);
    }

    /**
     * @Route("/{_locale}/multi-{count}-inflow", name="app_backend_fund_multi_cashinflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "count":"\d+"})
     * @RouteAction(name="app_backend_fund_multi_cashinflows", title="Faire une entrée de fond multiple", targets={"admins"})
     */
    public function multiCashInflow(Request $request, AccountManager $accountManager, int $count = 2): Response
    {
        $count = $count < 2 ? 2 : ($count > 50 ? 50 : $count);
        $multiInflow = new MultiCashInFlows($this->getUser(), $count);
        $form = $this->createForm(MultiCashInFlowsType::class, $multiInflow);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $funds = [];
            /** @var CashInFlows $target */
            foreach ($multiInflow->getTargets() as $target) {
                if ($target->getCashInFlows() > 0) {
                    $fund = $accountManager->ManageCashInFlows(
                        $target
                            ->setAccount($target->getUser()->getAccount())
                    );

                    $funds[] = $fund;
                }
            }

            foreach ($funds as $fund) {
                $this->getManager()->persist($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_account_index');
        }

        return $this->render('multi_inflow.html.twig', [
            'form' => $form->createView(),
            'title' => "Entrée de Fond Multiple",
            'type' => "inflow"
        ]);
    }



    /**
     * @Route("/{_locale}/multi-{count}-outflow", name="app_backend_fund_multi_cashoutflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "count":"\d+"})
     * @RouteAction(name="app_backend_fund_multi_cashoutflows", title="Faire une sortie de fond multiple", targets={"admins"})
     */
    public function multiCashOutflow(Request $request, AccountManager $accountManager, int $count = 2): Response
    {
        $count = $count < 2 ? 2 : ($count > 50 ? 50 : $count);
        $multiOutflow = new MultiCashOutFlows($this->getUser(), $count);
        $form = $this->createForm(MultiCashOutFlowsType::class, $multiOutflow);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $funds = [];
            /** @var CashOutFlows $target */
            foreach ($multiOutflow->getTargets() as $target) {
                if ($target->getCashOutFlows() > 0) {
                    $fund = $accountManager->ManageCashOutFlows(
                        $target
                            ->setAccount($target->getUser()->getAccount())
                    );

                    $funds[] = $fund;
                }
            }
            // dd($funds);
            foreach ($funds as $fund) {
                $this->getManager()->persist($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_account_index');
        }

        return $this->render('multi_inflow.html.twig', [
            'form' => $form->createView(),
            'title' => "Entrée de Fond Multiple",
            'type' => "outflow"
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-outflow", name="app_backend_fund_cashoutflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_fund_cashoutflows", title="fund.cashoutflows.action", targets={"admins"})
     */
    public function cashOutFlows(Request $request, Account $account, AccountManager $accountManager): Response
    {
        $cashOutFlows = (new CashOutFlows())
            ->setAccount($account)
            ->setUser($account->getUser())
            ->setAdmin($this->getUser());
        $form = $this->createForm(CashOutFlowsType::class, $cashOutFlows);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            try {
                $fund = $accountManager->ManageCashOutFlows($cashOutFlows);
            } catch (AccountManagerException $e) {
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_fund_cashoutflows', ['id' => $account->getId()]),
                    $this->trans($e->getMessage(), $e->getParams()),
                    true
                );
            }

            $this->getManager()->persist($fund);
            $this->dispatcher->dispatch(new CreateFundEvent($fund));
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_fund_show', ['id' => $account->getId()]);
        }
        return $this->render('cash_out_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $cashOutFlows,
            'user' => $account->getUser(),
            'account' => $account
        ]);
    }


    /**
     * @Route("/{_locale}/{id}-update-data", name="app_backend_fund_update_data", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_fund_update_data", title="fund.update_data.action", targets={"admins"})
     */
    public function update(Request $request, Fund $fund): Response
    {

        $initialFund = clone $fund;
        $form = $this->createForm(UpdateFundType::class, $fund);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            /** @var UpdateFundEvent */
            $event = $this->dispatcher->dispatch(
                new UpdateFundEvent($fund, $initialFund)
            );
            if ($event->isAssistanceLinked()) {
                $this->dispatcher->dispatch(
                    new UpdateContributorFundEvent($fund)
                );
            }
            if ($event->isOperationLinked()) {
                $this->dispatcher->dispatch(
                    new UpdateOperationMemberFundEvent($fund)
                );
            }
            if ($fund->isDebtRenewalLinked()) {
                $this->dispatcher->dispatch(
                    new UpdateLinkedDebtRenewalEvent($fund)
                );
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_fund_show', ['id' => $fund->getAccount()->getId()]);
        }
        return $this->render('edit.html.twig', [
            'form' => $form->createView(),
            "fund" => $fund
        ]);
    }



    /**
     * @Route("/{_locale}/{id}-delete", name="app_backend_fund_delete", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_fund_delete", title="fund.delete.action", targets={"admins"})
     */
    public function delete(Request $request, Fund $fund): Response
    {
        // $initialFund = clone $fund;
        if ($fund->isDebtRenewalLinked()) {
            return $this->redirectToRoute('app_backend_debt_renewal_delete', [
                'id' => $fund->getLinkedDebtRenewal()->getId()
            ]);
        }

        $form = $this->createForm(DeleteFundType::class, $fund);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $this->getManager()->remove($fund);
            /** @var DeleteFundEvent */
            $event = $this->dispatcher->dispatch(new DeleteFundEvent($fund));
            if ($event->isAssistanceLinked()) {
                $this->dispatcher->dispatch(new DeleteContributorFundEvent($fund));
            }
            if ($event->isOperationLinked()) {
                $this->dispatcher->dispatch(new DeleteOperationMemberFundEvent($fund));
            }
            // if ($event->isDebtRenewalLinked()) {
            //     $this->dispatcher->dispatch(
            //         new DeleteLinkedDebtRenewalEvent($fund, $this->getManager())
            //     );
            // }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_fund_show', ['id' => $fund->getAccount()->getId()]);
        }
        return $this->render('delete.html.twig', [
            'form' => $form->createView(),
            "fund" => $fund
        ]);
    }

    private function updateCreatedAfter(Fund $fund, ?Fund $initial)
    {
        //on modifie un fond d'une date anterieure a une date récente
        if ($initial && $initial->getCreatedAt() < $fund->getcreatedAt()) {
            $anterior = $this->getFundRepository()
                ->findWhereCreatedAt(
                    $initial->getCreatedAt(),
                    operator: "<",
                    conditions: ["user" => $initial->getUser()],
                    getOneResult: true
                ) ?? $initial;


            /** @var Fund[] $funds */
            $funds = $this->getFundRepository()
                ->findWhereCreatedAt(
                    $initial->getCreatedAt(),
                    conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
                    orderDesc: false
                );
        } else {
            // soit on supprimer un fond, soit on le modifie d'une
            // date récente a une date anterieure

            $anterior = $this->getFundRepository()
                ->findWhereCreatedAt(
                    $fund->getCreatedAt(),
                    operator: "<",
                    conditions: ["user" => $fund->getUser()],
                    getOneResult: true
                ) ?? $fund;


            /** @var Fund[] $funds */
            $funds = $this->getFundRepository()
                ->findWhereCreatedAt(
                    $fund->getCreatedAt(),
                    conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
                    orderDesc: false
                );
        }

        if ($initial) {
            array_unshift($funds, $fund);
        } else {
            $this->getManager()->remove($fund);
        }

        $funds = $this->collection($funds)
            ->sortBy(fn (Fund $fund) => $fund->getCreatedAt());
        $fakeAccount = (new Account())
            ->setUser($fund->getUser())
            ->setCashBalances((int)$anterior->getPreviousBalances())
            ->resetCashInFlows((int)$anterior->getPreviousTotalInflows())
            ->resetCashOutFlows((int)$anterior->getPreviousTotalOutflows())
            ->setCashInFlows((int)$anterior->getCashInFlows())
            ->setCashOutFlows((int)$anterior->getCashOutFlows());
        foreach ($funds as $after) {
            $after
                ->setPreviousBalances((int)$fakeAccount->getCashBalances())
                ->setPreviousTotalInflows((int)$fakeAccount->getCashInflows())
                ->setPreviousTotalOutflows((int)$fakeAccount->getCashOutFlows())
                ->setCashBalances((int)$fakeAccount->getCashBalances())
                ->setCashInFlows((int)$after->getCashInFlows())
                ->setCashOutFlows((int)$after->getCashOutFlows());
            $fakeAccount->setCashInFlows(
                $after->getCashInflows()
            )
                ->setCashOutFlows(
                    $after->getCashOutFlows()
                );
        }
        unset($fakeAccount);
        return $funds;
    }
}
