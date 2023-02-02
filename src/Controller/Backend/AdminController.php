<?php

namespace App\Controller\Backend;

use App\Entity\Assistances\Assistance;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontine;
use App\Tools\AppConstants;
use App\Tools\Files\PdfProvider;
use App\Tools\Request\UrlSessionManager;
use App\Tools\Twig\LocalLanguages;
use DateTime;
use Illuminate\Support\Collection;
use Njeaner\FrontTranslator\Manager\TranslationManager;
use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use stdClass;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/backend/admin")
 * @Module(name="base", is_activated=true)
 */
class AdminController extends AdminBaseController
{
    protected $viewPath = 'main/admin/';

    private null|array $data = null;

    /**
     * @Route("/{_locale}/{year}", name="app_backend_admin_index", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "year":"[0-9]{4}"})
     * @RouteAction(name="app_backend_admin_index", title="admin.index.see.action", targets={"admins"})
     */
    public function index(?int $year = null): Response
    {

        $currentYear =  (new DateTime())->format('Y');
        $year = $year ?? $currentYear;
        $years = [];
        $years = array_map(fn (array $array) => $array['year'], $this->getFundRepository()->findYears());

        return $this->render('index.html.twig', [
            'data' => [
                'totals.member' => $this->getAll($year, $this->generateUrl('app_backend_user_index')),
                'locked.member' => $this->lockedUsers($this->generateUrl('app_backend_locked_users')),

                'low_cash_balance' => $this->getElement('negative_fund', $year, $this->generateUrl(
                    'app_backend_account_filter',
                    ['field' => 'fund', 'operator' => 'infeq', 'value' => 0]
                ), title: 'low_cash_balance', base: 0),
                'base_cash_balance' => $this->getElement('base_fund', $year, $this->generateUrl(
                    'app_backend_account_filter',
                    ['field' => 'fund', 'operator' => 'infeq', 'value' => AppConstants::$ACCOUNT_BASE_FUND]
                ), title: 'low_cash_balance', base: AppConstants::$ACCOUNT_BASE_FUND),
                'over_base_fund_balance' => $this->getElement('over_base_fund', $year, $this->generateUrl(
                    'app_backend_account_filter',
                    ['field' => 'fund', 'operator' => 'supeq', 'value' => AppConstants::$ACCOUNT_BASE_FUND]
                ), title: 'upper_cash_balance', base: AppConstants::$ACCOUNT_BASE_FUND),
                'over_base_loan_balance' => $this->getElement('over_base_loan', $year, $this->generateUrl(
                    'app_backend_account_filter',
                    ['field' => 'fund', 'operator' => 'supeq', 'value' => AppConstants::$LOAN_BASE_FUND]
                ), title: 'upper_cash_balance', base: AppConstants::$LOAN_BASE_FUND),
                'has_debt' => $this->getElement('has_loan', $year, $this->generateUrl('app_backend_account_has_loan')),
            ],
            'states' => $this->getElement('data', $year),
            'tontines' => $this->getTontineInfos(),
            'assistances' => $this->assistancesOrderByYear($year, path: $this->generateUrl('app_backend_assistance_index')),
            'operations' => $this->getOperationsInfos($year),
            'years' => $years,
            'year' => $year
        ]);
    }

    private function fundMap(Int|string $year)
    {
        $data = [
            'negative_fund' => ['count' => 0, 'items' => []],
            'positive_fund' => ['count' => 0, 'items' => []],
            'base_fund' => ['count' => 0, 'items' => []],
            'over_base_fund' => ['count' => 0, 'items' => []],
            'base_loan' => ['count' => 0, 'items' => []],
            'over_base_loan' => ['count' => 0, 'items' => []],
            'has_loan' => ['count' => 0, 'items' => []],
            'data' => [
                'previous' => 0,
                'inflows' => 0,
                'outflows' => 0,
                'balances' => 0,
                'loan_previous' => 0,
                'loan_inflows' => 0,
                'loan_outflows' => 0,
                'loan_balances' => 0
            ]
        ];

        $this->collection($this->getFundRepository()->findUsersBalance($year, 'user'))->map(
            function (array $account) use (&$data) {
                $balance = $account['previous'] + $account['inflows'] - $account['outflows'];

                $data['data']['previous'] += $account['previous'];
                $data['data']['inflows'] += $account['inflows'];
                $data['data']['outflows'] += $account['outflows'];
                $data['data']['balances'] += $account['inflows'] + $account['previous'] - $account['outflows'];

                if ($balance <= 0) {
                    $data['negative_fund']['count']++;
                    $data['negative_fund']['items'][] = $account;
                } else {
                    $data['positive_fund']['count']++;
                    $data['positive_fund']['items'][] = $account;
                }

                if ($balance < AppConstants::$ACCOUNT_BASE_FUND) {
                    $data['base_fund']['count']++;
                    $data['base_fund']['items'][] = $account;
                } else {
                    $data['over_base_fund']['count']++;
                    $data['over_base_fund']['items'][] = $account;
                }

                if ($balance < AppConstants::$LOAN_BASE_FUND) {
                    $data['base_loan']['count']++;
                    $data['base_loan']['items'][] = $account;
                } else {
                    $data['over_base_loan']['count']++;
                    $data['over_base_loan']['items'][] = $account;
                }
            }
        );

        $this->collection($this->getDebtRepository()->findUsersBalance($year, 'user'))->map(
            function (array $account) use (&$data) {
                $LoanBalance = $account['previous'] + $account['inflows'] - $account['outflows'];
                $data['data']['loan_previous'] += $account['previous'];
                $data['data']['loan_inflows'] += $account['inflows'];
                $data['data']['loan_outflows'] += $account['outflows'];
                $data['data']['loan_balances'] += $account['inflows'] + $account['previous'] - $account['outflows'];


                if ($LoanBalance > 0) {
                    $data['has_loan']['count']++;
                    $data['has_loan']['items'][] = $account;
                }
            }
        );
        return $this->data = $data;
    }


    private function getData(int|string $year): array
    {
        if ($this->data === null) {
            $this->fundMap($year);
        }
        return $this->data;
    }

    private function assistancesOrderByYear(null|int|string $year = null, ?string $path = null)
    {
        $assistances = $this->collection(
            $this->getAssistanceRepository()->findBy($year ? ['year' => $year] : null)
        )
            ->sortBy(fn (Assistance $assistance) => $assistance->getUser())
            ->groupBy(fn (Assistance $assistance) => $assistance->getType()->getId())
            ->map(fn (Collection $items) => ['count' => $items->count(), 'items' => $items, 'type' => $items[0]->getType()]);
        return ['data' => $assistances, 'path' => $path];
    }

    public function getAll(int|string $year, ?string $path = null): array
    {
        $users = $this->getUserRepository()->findAllForYear(
            new DateTime($year . '-12-31 23:59:59')
        );
        return array_merge(['count' => count($users), 'items' => $users, 'path' => $path], []);
    }

    private function lockedUsers(?string $path = null)
    {
        $locked = $this->getUserRepository()->findBy(['locked' => true]);
        return ['count' => count($locked), 'items' => $locked, 'path' => $path];
    }

    public function getElement(string $name, int|string $year, ?string $path = null, ?string $title = null, ?int $base = null)
    {
        return array_merge($this->getData($year)[$name], ['path' => $path, 'title' => $title, 'base' => $base]);
    }


    public function getOperationsInfos(int|string $year)
    {
        $infos = $this->getOperationRepository()->sum(year: $year);
        $types = $this->getOperationRepository()->findOperationsTypes(year: $year);
        $otherTypes = $this->getOperationTypeRepository()->findExcept($types);
        $operationsData = [];
        foreach ($infos as $key => $info) {
            $std = new stdClass();
            $std->type = $types[$key]->getType();
            $std->year = $year;
            $std->previousBalance = (int) $this->getOperationRepository()->findOneBy(['type' => $std->type, 'year' => $year - 1], ['id' => 'DESC'])?->getBalance();
            foreach ($info as $prop => $count) {
                $std->$prop = $count;
            }
            $std->balances = $std->inflows - $std->outflows;
            $operationsData[] = $std;
        }
        foreach ($otherTypes as $type) {
            $std = new stdClass();
            $std->type = $type;
            $std->year = $year;
            $std->previousBalance = 0;
            $std->inflows = 0;
            $std->outflows = 0;
            $std->balances = 0;
            $operationsData[] = $std;
        }
        return $operationsData;
    }

    public function getTontineInfos()
    {
        $tontines = $this->getTontineRepository()->findBy(['isCurrent' => true]);
        if (!empty($tontines)) {
            $tontines = $this->collection($tontines)->groupBy(fn (Tontine $tontine) => $tontine->getType()->getId());
        }
        return $tontines;
    }
}
