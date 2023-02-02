<?php

namespace App\Controller\Backend\Accounts;

use Njeaner\UserRoleBundle\Annotations\Module;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Main\Funds\Account;
use App\Tools\Entity\TotalResolver;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("backend/account")
 * @Module(name="account", is_activated=true)
 */
class AccountController extends AdminBaseController
{
    protected $viewPath = 'account/account/';

    /**
     * @Route("/{_locale}", name="app_backend_account_index", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_account_index", title="account.index.action", targets={"admins"})
     */
    public function index(): Response
    {
        $accounts = $this->collection($this->getAccountRepository()->findNotLocked())
            ->sortBy(fn (Account $account) => $account->getUser()->getName());
        // $totals = (new TotalResolver)->resolve($accounts, 'account');
        // // dd($totals);
        return $this->render('index.html.twig', [
            'accounts' => $accounts,
            // 'totals' => $totals,
            'title' => null
        ]);
    }

    /**
     * @Route(
     * "/{_locale}/filter-{field}-{operator}-{value}",
     * name="app_backend_account_filter",
     * methods={"GET"}, 
     * requirements={
     *  "_locale":"en|fr|es|it|pt",
     *  "field":"fund|loan",
     *  "operator":"inf|eq|sup|infeq|supeq",
     *  "value":"\d+"
     * }
     * )
     * @RouteAction(name="app_backend_account_filter", title="account.filter.action", targets={"admins"})
     */
    public function filter(string $field, string $operator, int $value)
    {
        $accounts = $this->getAccountRepository()->findWhere('fund', $operator, $value);

        $totals = (new TotalResolver)->resolve($accounts, 'account');
        return $this->render('index.html.twig', [
            'accounts' => $accounts,
            'totals' => $totals,
            'title' => $this->trans(($operator === 'inf' || $operator === 'infeq') ? 'low_cash_balance' : 'upper_cash_balance', ['%balance%' => $value])
        ]);
    }

    /**
     * @Route("/{_locale}/has-loan", name="app_backend_account_has_loan", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_account_has_loan", title="account.has_loan.action", targets={"admins"})
     */
    public function hasLoan(): Response
    {
        $accounts = $this->getAccountRepository()->findWhere('loan', 'sup', 0);
        $totals = (new TotalResolver)->resolve($accounts, 'account');

        return $this->render('index.html.twig', [
            'accounts' => $accounts,
            'totals' => $totals,
            'title' => $this->trans('has_debts.index')
        ]);
    }
}
