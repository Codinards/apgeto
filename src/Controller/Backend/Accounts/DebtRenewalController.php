<?php

namespace App\Controller\Backend\Accounts;

use App\Controller\Backend\AdminBaseController;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtRenewal;
use App\Entity\Main\Funds\Fund;
use App\Events\Debt\CreateDebtEvent;
use App\Events\DebtRenewal\CreateDebtRenewalEvent;
use App\Events\Fund\CreateFundEvent;
use App\Form\Main\Funds\DebtRenewalType;
use App\Form\Main\Funds\DeleteDebtRenewalType;
use App\Tools\AppConstants;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/account/debt-renewal", name="app_backend_debt_renewal_")
 * @Module(name="account", is_activated=true)
 */
class DebtRenewalController extends AdminBaseController
{
    protected $viewPath = 'account/debt_renewal/';


    /**
     * @Route("/{_locale}/{id}-new", name="new", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_renewal_new", title="debt_renewal.new.action", targets={"admins"})
     */
    public function new(Request $request, Debt $debt): Response
    {
        $renewal = (new DebtRenewal())
            ->setDebt($debt)
            ->setAccount($account = $debt->getAccount())
            ->setAdmin($this->getUser())
            ->setAmount($account->getLoanBalances() * AppConstants::$INTEREST_RATE)
            ->setWording("Reconduction prêt du " . ($debt->getRenewalDate())->format("d/m/Y"))
            ->setDebtRate((float) AppConstants::$INTEREST_RATE);

        $form = $this->createForm(DebtRenewalType::class, $renewal);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            if ($renewal->renewalOutflow === DebtRenewal::FUND_SUBSTRACT) {
                $fund = (new Fund())
                    ->setWording($renewal->getWording())
                    ->setCashBalances($account->getCashBalances())
                    ->setPreviousBalances($account->getCashBalances())
                    ->setPreviousTotalInflows($account->getCashInFlows())
                    ->setPreviousTotalOutflows($account->getCashOutFlows())
                    ->setObservations($renewal->getObservation())
                    ->setCreatedAt($renewal->getCreatedAt())
                    ->setAccount($account)
                    ->setUser($account->getUser())
                    ->setCashOutFlows($renewal->getAmount())
                    ->setAdmin($renewal->getAdmin())
                    ->setYear($renewal->getYear());
                $account->setCashOutFlows($renewal->getAmount());
                $this->getManager()->persist($fund);
                $renewal->setLinkedFund($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            } else if ($renewal->renewalOutflow === DebtRenewal::DEBT_ADD) {
                $linkedDebt = (new Debt)
                    ->setLoanBalances($account->getLoanBalances())
                    ->setPreviousTotalinflows($account->getLoanInFlows())
                    ->setPreviousTotaloutflows($account->getLoanOutFlows())
                    ->setPreviousBalances($account->getLoanBalances())
                    ->setUser($account->getUser())
                    ->setAdmin($this->getUser())
                    ->setWording($renewal->getWording())
                    ->setCreatedAt($renewal->getCreatedAt())
                    ->setAccount($account)
                    ->setYear((int)$renewal->getCreatedAt()->format("Y"))
                    ->setObservations($renewal->getObservation() ?? $renewal->getWording())
                    ->setLoanInFlows($renewal->getAmount())
                    ->setInterests(0)
                    ->setType(Account::INFLOW);
                $linkedDebt->origin = Debt::FROM_RENEWAL;
                $linkedDebt->resetLoanInFlows($linkedDebt->getLoanInFlows() - $linkedDebt->getPreviousBalances());
                foreach ($debt->getAvalistes() as $avaliste) {
                    $linkedDebt->addAvaliste($avaliste);
                }
                $linkedDebt->setParent($debt);
                // $account->setLoanInFlows($renewal->getAmount());
                $this->getManager()->persist($linkedDebt);
                $renewal->setLinkedDebt($linkedDebt);
                $this->dispatcher->dispatch(new CreateDebtEvent($linkedDebt));
            }
            $this->getManager()->persist($renewal);
            $debt->setRenewalAt($renewal->getCreatedAt());
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute($renewal->renewalOutflow === DebtRenewal::DEBT_ADD ? 'app_backend_debt_show' : 'app_backend_fund_show', ['id' => $account->getId()]);
        }

        return $this->render('new.html.twig', [
            "form" => $form->createView(),
            "renewal" => $renewal,
            "debt" => $debt
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-update", name="update", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_renewal_update", title="debt_renewal.update.action", targets={"admins"})
     */
    public function update(Request $request, DebtRenewal $renewal)
    {
        $form = $this->createForm(DebtRenewalType::class, $renewal);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            dd($renewal);
        }
        return $this->render('new.html.twig', [
            "form" => $form->createView(),
            "renewal" => $renewal,
            "debt" => $renewal->getDebt()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-delete", name="delete", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_debt_renewal_delete", title="debt_renewal.delete.action", targets={"admins"})
     */
    public function delete(Request $request, DebtRenewal $renewal)
    {
        $form = $this->createForm(DeleteDebtRenewalType::class, $renewal);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
        }
        return $this->render('delete.html.twig', [
            "form" => $form->createView(),
            "renewal" => $renewal,
            "debt" => $renewal->getDebt()
        ]);
    }
}
