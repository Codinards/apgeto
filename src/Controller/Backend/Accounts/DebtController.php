<?php

namespace App\Controller\Backend\Accounts;

use App\Form\Main\Funds\UpdateDebtType;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Exceptions\AccountManagerException;
use App\Entity\Interests\UserInterest;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtInterest;;

use App\Entity\Utils\LoanDataUpdate;
use App\Entity\Utils\LoanInFlows;
use App\Entity\Utils\LoanOutFlows;
use App\Events\Debt\CreateDebtEvent;
use App\Events\Debt\DeleteDebtEvent;
use App\Events\Debt\DispatchCreatedDebtInterest;
use App\Events\Debt\UpdateDebtEvent;
use App\Form\Utils\LoanDataUpdateType;
use App\Form\Utils\LoanInFlowsType;
use App\Form\Utils\LoanOutFlowsType;
use App\Tools\AppConstants;
use App\Tools\Entity\AccountManager;
use App\Tools\Entity\TotalResolver;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/account/debt")
 * @Module(name="account", is_activated=true)
 */
class DebtController extends AdminBaseController
{
    protected $viewPath = 'account/debt/';

    /**
     * @Route("/{_locale}/{id}-show", name="app_backend_debt_show", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_show", title="debt.show.action", targets={"admins"})
     */
    public function show(Request $request, Account $account, bool $called = false): Response
    {
        $debts = [];
        if ($debt = $account->getCurrentDebt()) {
            $debts = $debt->getChildren()->toArray();
            array_unshift($debts, $debt);
        }

        $debts = $this->collection($debts)->sortBy(fn (Debt $debt) => $debt->getCreatedAt());
        $totals = (new TotalResolver)->resolve($debts, 'debt');
        $previous = $this->getDebtRepository()
            ->findBy(['isCurrent' => false, 'account' => $account, 'parent' => 'IS NULL']);
        return $this->render('show.html.twig', [
            'account' => $account,
            'totals' => $totals,
            'user' => $account->getUser(),
            'debts' => $debts,
            'count' => count($debts),
            'previous' => $this->dataOrNull($previous),
            'called' => $called,
            "currentDebt" => $this->getCurrentDebt($account)
        ]);
    }

    private function getCurrentDebt(Account $account): false|Debt
    {
        $currentDebt = $account->getCurrentDebt();

        if (!$currentDebt) {
            return false;
        }

        $renewalAt = $currentDebt->getRenewalAt();
        $resolvePeriod = function ($format = "Y-m-d") use ($currentDebt, $renewalAt): \DateTime {
            return $renewalAt
                ? (new \DateTime($renewalAt->format($format)))->add($currentDebt->getRenewalPeriod())
                : ($currentDebt->getPaybackAt() ??  (new \DateTime($currentDebt->getCreatedAt()->format($format)))
                    ->add($currentDebt->getRenewalPeriod()));
        };

        $time = ((new \DateTime()))->getTimestamp();
        return $time > $resolvePeriod()->getTimestamp() ? $currentDebt : false;
    }


    /**
     * @Route("/{_locale}/{id}-show/parent-{parent}", name="app_backend_debt_show_previous", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "parent":"\d+"})
     * @RouteAction(name="app_backend_debt_show_previous", title="debt.show.previous.action", targets={"admins"})
     */
    public function showPrevious(Request $request, Account $account, Debt $parent, bool $called = false): Response
    {
        $debts = $this->getDebtRepository()->findBy(['parent' => $parent, 'account' => $account]);
        array_unshift($debts, $parent);
        //$oldBalance = $this->getDebtRepository()->findLast(['account' => $account, 'isCurrent' => true, 'year' => ($year - 1)]);
        $totals = (new TotalResolver)->resolve($debts, 'debt');
        /*$years = $this->collection($this->getFundRepository()->findYears())
            ->reverse()
            ->map(function ($item) {
                return $item['year'];
            });*/
        return $this->render('show_previous.html.twig', [
            'account' => $account,
            'totals' => $totals,
            'user' => $account->getUser(),
            'debts' => $debts,
            'count' => count($debts),
            'parent' => $parent,
            'called' => $called
        ]);
    }


    /**
     * @Route("/{_locale}/{id}-inflow", name="app_backend_debt_loaninflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_loaninflows", title="debt.loaninflows.action", targets={"admins"})
     */
    public function loanInFlows(Request $request, Account $account, AccountManager $accountManager): Response
    {
        $this->throwRedirectRequest(
            !$account->canLoan(false),
            $this->generateUrl('app_backend_debt_show', ['id' => $account->getId(), '_local' => $request->getLocale()]),
            $this->trans('this.member.cannot.loan'),
            true
        );
        $loanInFlows = (new LoanInFlows())
            ->setAccount($account)
            ->setUser($account->getUser())
            ->setAdmin($this->getUser());
        $form = $this->createForm(LoanInFlowsType::class, $loanInFlows);
        $form->handleRequest($request);
        try {
            $debt = $accountManager->ManageLoanInFlows($loanInFlows, $request->isMethod('POST'))
                ->setYear($this->getYear());
        } catch (AccountManagerException $e) {
            $this->throwRedirectRequest(
                true,
                $this->generateUrl('app_backend_debt_show', ['id' => $account->getId()]),
                $this->trans($e->getMessage(), $e->getParams()),
                true
            );
        }

        if ($form->isSubmitted() and $form->isValid()) {

            $this->getManager()->persist($debt);

            /** Interest Registration */
            $this->getManager()->persist(
                $debtInterest = (new DebtInterest)
                    ->setWording($debt->getWording())
                    ->setInterest($debt->getInterests())
                    ->setCreatedAt($debt->getCreatedAt())
                    ->setDebt($debt)
            );
            $account->setCurrentDebt($debt);

            $this->dispatcher->dispatch(
                new DispatchCreatedDebtInterest($debt, $debtInterest)
            );

            /** Database commit */
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_debt_show', ['id' => $account->getId()]);
        }

        $avalisations = $this->getDebtRepository()
            ->findByConditions(
                [
                    'isCurrent' => true, 'parent' => 'IS NULL', 'memberOf' => ['avalistes' => $account->getUser()]
                ]
            );
        $tontineur = $this->getTontineurRepository()->findOneBy(['user' => $account->getId()]) ?? null;

        return $this->render('loan_in_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $loanInFlows,
            'user' => $account->getUser(),
            'account' => $account,
            'avalisations' => $this->dataOrNull($avalisations),
            'tontineurData' => $tontineur ? $tontineur->getCurrentData() : null,
            'isInflow' => true
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-outflow", name="app_backend_debt_loanoutflows", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_loanoutflows", title="debt.loanoutflows.action", targets={"admins"})
     */
    public function loanOutFlows(Request $request, Account $account, AccountManager $accountManager): Response
    {
        $this->throwRedirectRequest(
            !$account->hasLoan(),
            $this->generateUrl('app_backend_debt_show', ['id' => $account->getId()]),
            $this->trans(AccountManagerException::NO_EXISTING_DEBT),
            true
        );

        $loanOutFlows = (new LoanOutFlows($account->getCurrentDebt()))
            ->setAccount($account)
            ->setUser($account->getUser())
            ->setAdmin($this->getUser());
        $form = $this->createForm(LoanOutFlowsType::class, $loanOutFlows);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getManager();

            /** Operation validation */
            try {
                $debt = $accountManager->ManageLoanOutFlows($loanOutFlows, $manager);
            } catch (AccountManagerException $e) {
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_debt_loanOutFlows', ['id' => $account->getId()]),
                    $this->trans($e->getMessage(), $e->getParams()),
                    true
                );
            }

            $manager->persist($debt);
            $this->dispatcher->dispatch(new CreateDebtEvent($debt));

            /** Database commit */
            $manager->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_debt_show', ['id' => $account->getId()]);
        }
        return $this->render('loan_out_flow.html.twig', [
            'form' => $form->createView(),
            'data' => $loanOutFlows,
            'user' => $account->getUser(),
            'account' => $account,
            'isInflow' => false,
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-update", name="app_backend_debt_update", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_update", title="debt.update.action", targets={"ROLE_SUPERADMIN"})
     */
    public function loanUpdate(Request $request, debt $debt, AccountManager $accountManager): Response
    {
        if ($debt->isUpdatable()) {

            $previous = clone $debt;
            $loanUpdate = (new LoanDataUpdate)
                ->setIsInflow($debt->isInflow());
            $loanUpdate->hydrate($debt);

            $form = $this->createForm(LoanDataUpdateType::class, $loanUpdate);
            $form->handleRequest($request);

            if ($form->isSubmitted() and $form->isValid()) {

                /** Remove all avalistes that were removed by user  */
                foreach ($debt->getAvalistes() as $avaliste) {
                    if ($loanUpdate->getAvalistes()->contains($avaliste) === false) {
                        $debt->removeAvaliste($avaliste);
                        $this->getManager()->remove($avaliste);
                    }
                }

                /** update debt information, associated interest information and flush */
                $debt = $accountManager->manageLoanUpdate($loanUpdate, $debt);

                $interest = $this->getInterestRepository()->findOneBy(['debt' => $debt]);
                if ($interest) {
                    $interest->setInterest($debt->getInterests());
                }

                $this->dispatcher->dispatch(new UpdateDebtEvent($debt, $previous));
                if ($debt->isDebtRenewalLinked()) {
                    $debt->getLinkedDebtRenewal()->setAmount($loanUpdate->getLoanInFlows());
                }

                $this->getManager()->flush();
                $this->successFlash('operation.success');
                return $this->redirectToRoute('app_backend_debt_show', ['id' => $debt->getAccount()->getId()]);
            }
            return $this->render('update.html.twig', [
                'form' => $form->createView(),
                'debt' => $debt,
                'user' => $debt->getUser(),
                'account' => $debt->getAccount(),
                'isInflow' => $loanUpdate->getIsInflow()
            ]);
        }
        $this->successFlash('operation.unauthorize');
        return $this->redirectToRoute('app_backend_debt_show', ['id' => $debt->getAccount()->getId()]);
    }

    /**
     * @Route("/{_locale}/{id}-delete", name="app_backend_debt_delete", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_delete", title="debt.delete.action", targets={"ROLE_SUPERADMIN"})
     */
    public function loanDelete(Request $request, debt $debt): Response
    {
        if ($debt->isDeletable()) {
            if ($request->isMethod('POST')) {
                if ($debt->isInflow()) {
                    $debt->getAccount()->reinitializeDebt();
                    foreach ($debt->getChildren() as $child) {
                        $this->getManager()->remove($child);
                    }
                    $debt->delete($this->getManager());
                } else {
                    $this->dispatcher->dispatch(new DeleteDebtEvent($debt));
                    if ($debt->isDebtRenewalLinked()) {
                        $this->getManager()->remove($debt->getLinkedDebtRenewal());
                    }
                    $this->getManager()->remove($debt);
                }
                $this->getManager()->flush();
                $this->successFlash('operation.success');
                return $this->redirectToRoute('app_backend_debt_show', ['id' => $debt->getAccount()->getId()]);
            }
            return $this->render('delete.html.twig', [
                'debt' => $debt,
                'user' => $debt->getUser(),
                'account' => $debt->getAccount()
            ]);
        }
        $this->successFlash('operation.unauthorize');
        return $this->redirectToRoute('app_backend_debt_show', ['id' => $debt->getAccount()->getId()]);
    }
}
