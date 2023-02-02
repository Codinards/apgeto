<?php

namespace App\Controller;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\AssistanceType;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtInterest;
use App\Entity\Main\Funds\DebtRenewal;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use App\Entity\Main\Users\UserAction;
use App\Entity\Tontines\CotisationDay;
use App\Entity\Tontines\CotisationFailure;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontinetype;
use App\Entity\Tontines\Tontineur;
use App\Entity\Tontines\TontineurData;
use App\Entity\Tontines\Unity;
use App\Repository\Main\Users\RoleRepository;
use App\Repository\Main\Users\UserRepository;
use App\Repository\Main\Users\UserActionRepository;
use App\EventSubscriber\Exceptions\RedirectRequestException;
use App\Repository\Assistances\AssistanceRepository;
use App\Repository\Assistances\AssistanceTypeRepository;
use App\Repository\Main\Funds\AccountRepository;
use App\Repository\Main\Funds\DebtInterestRepository;
use App\Repository\Main\Funds\DebtRenewalRepository;
use App\Repository\Main\Funds\DebtRepository;
use App\Repository\Main\Funds\FundRepository;
use App\Repository\Main\Operations\OperationRepository;
use App\Repository\Main\Operations\TypeRepository;
use App\Repository\Tontines\CotisationDayRepository;
use App\Repository\Tontines\CotisationFailureRepository;
use App\Repository\Tontines\TontineRepository;
use App\Repository\Tontines\TontinetypeRepository;
use App\Repository\Tontines\TontineurDataRepository;
use App\Repository\Tontines\TontineurRepository;
use App\Repository\Tontines\UnityRepository;
use App\Tools\AppConstants;
use App\Tools\Languages\LangResolver;
use App\Tools\Request\UrlSessionManager;
use App\Tools\Twig\LocalLanguages;
use DateTime;
use App\Tools\Files\PdfProvider;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;

abstract class BaseController extends AbstractController
{
    /**
     * @var DataCollectorTranslator
     */
    protected $translator;

    protected $localLanguages;

    protected $urlsessionManager;

    /** 
     * @var Request
     */
    protected $request;

    /** PdfProvider */
    protected $pdfProvider;

    protected EventDispatcherInterface $dispatcher;

    protected $menu_routes = [
        'users' => [
            'members' => ['app_backend_user_index', 'fa fa-user'],
            'accounts' => ['app_backend_account_index', 'fa fa-folder'],
            'authorizations' => ['app_backend_user_action_index', 'fa fa-mountain'],
        ],
        'tontines' => [
            'types' => ['app_backend_tontinetype_index', 'fa fa-link'],
            'tontines' => ['app_backend_tontine_index', 'fa fa-money-check'],
            'cotisations' => 'app_backend_tontine_cotisation_index'
        ],
        'assistances' => [
            'types' => 'app_backend_assistance_type_index',
            'assistances' => 'app_backend_assistance_index'
        ],
        'administration' => [
            'dashboard' => "app_backend_admin_index",
            'operations' => 'app_backend_operation_type_index'
        ]
    ];


    public function __construct(
        TranslatorInterface $translator,
        LocalLanguages $localLanguages,
        UrlSessionManager $urlsessionManager,
        PdfProvider $pdfProvider,
        EventDispatcherInterface $dispatcher
    ) {
        $this->translator = $translator;
        $this->localLanguages = $localLanguages;
        $this->urlsessionManager = $urlsessionManager;
        $this->request = $this->localLanguages->getRequest();
        $this->pdfProvider = $pdfProvider;
        $this->dispatcher = $dispatcher;
    }

    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $asPdf = $this->request->query->get('as_pdf');
        $parameters['as_pdf'] =  $asPdf ? true : false;
        $parameters['translation_catalogue'] = json_encode(
            $this->catalogueData(),
            JSON_UNESCAPED_UNICODE
        );
        $parameters['appConstants'] = AppConstants::getInstance();
        $parameters = $this->getUser() ? array_merge($parameters, [
            'menu_routes' => $this->menu_routes
        ]) : $parameters;

        return parent::render($view, $parameters, $response);
    }

    private function catalogueData(): array
    {
        $catalogues = [];
        foreach (array_keys(LangResolver::$languages_name) as $key) {
            $catalogues[$key] = [];
            $all = $this->translator->getCatalogue(AppConstants::$DEFAULT_LANGUAGE_KEY)->all();
            foreach ($all as $elt) {
                $catalogues[$key] = array_merge($catalogues[$key], $elt);
            }
        }
        return $catalogues;
    }


    public function throwRedirectRequest(bool $condition, $route = 'home', ?string $message = null, bool $is_path = false): void
    {
        if ($condition) {
            throw new RedirectRequestException($route, $message, $is_path);
        }
    }

    public function throwRedirectRequestToHome(bool $condition, ?string $message = null): void
    {
        $this->throwRedirectRequest($condition, 'home', $message);
    }

    public function getManager(?string $connection = 'default'): EntityManager
    {
        return $this->getDoctrine()->getManager($connection);
    }

    public function getTontineManager(): EntityManager
    {
        return $this->getManager();
    }

    public function getAssistanceManager(): EntityManager
    {
        return $this->getManager();
    }

    public function getRepository($entityName, ?string $connection = null)
    {
        return $this->getManager($connection)->getRepository($entityName);
    }

    public function getRoleRepository(): RoleRepository
    {
        return $this->getRepository(Role::class);
    }

    public function getUserRepository(): UserRepository
    {
        return $this->getRepository(User::class);
    }

    public function getUserActionRepository(): UserActionRepository
    {
        return $this->getRepository(UserAction::class);
    }

    public function getAccountRepository(): AccountRepository
    {
        return $this->getRepository(Account::class);
    }

    public function getFundRepository(): FundRepository
    {
        return $this->getRepository(Fund::class);
    }

    public function getDebtRepository(): DebtRepository
    {
        return $this->getRepository(Debt::class);
    }

    public  function getInterestRepository(): DebtInterestRepository
    {
        return $this->getRepository(DebtInterest::class);
    }

    public  function getRenewalRepository(): DebtRenewalRepository
    {
        return $this->getRepository(DebtRenewal::class);
    }

    public function getOperationTypeRepository(): TypeRepository
    {
        return $this->getRepository(Type::class);
    }

    public function getOperationRepository(): OperationRepository
    {
        return $this->getRepository(Operation::class);
    }

    public function getAssistanceTypeRepository(): AssistanceTypeRepository
    {
        return $this->getAssistanceManager()->getRepository(AssistanceType::class);
    }

    public function getAssistanceRepository(): AssistanceRepository
    {
        return $this->getAssistanceManager()->getRepository(Assistance::class);
    }

    public function getTontinetypeRepository(): TontinetypeRepository
    {
        return $this->getTontineManager()->getRepository(Tontinetype::class);
    }

    public function getTontineRepository(): TontineRepository
    {
        return $this->getTontineManager()->getRepository(Tontine::class);
    }

    public function getTontineurRepository(): TontineurRepository
    {
        return $this->getTontineManager()->getRepository(Tontineur::class);
    }

    public function getUnityRepository(): UnityRepository
    {
        return $this->getTontineManager()->getRepository(Unity::class);
    }

    public function getTontineurDataRepository(): TontineurDataRepository
    {
        return $this->getTontineManager()->getRepository(TontineurData::class);
    }

    public function getCotisationFailureRepository(): CotisationFailureRepository
    {
        return $this->getTontineManager()->getRepository(CotisationFailure::class);
    }

    public function getCotisationDayRepository(): CotisationDayRepository
    {
        return $this->getTontineManager()->getRepository(CotisationDay::class);
    }


    public function getRequestLocale(): string
    {
        return $this->request->getLocale();
    }

    public function trans($id, array $parameters = []): string
    {
        return $this->translator->trans($id, $parameters, null, $this->localLanguages->getLocale());
    }

    public function flashMessage(string $type, $message, array $parameters = [], bool $translate = true)
    {
        if ($translate) {
            $message = $this->trans($message, $parameters);
        }
        return parent::addFlash($type, $message);
    }

    public function successFlash($message, array $parameters = [], bool $translate = true)
    {
        return $this->flashMessage('success', $message, $parameters, $translate);
    }

    public function errorFlash($message, array $parameters = [], bool $translate = true)
    {
        return $this->flashMessage('error', $message, $parameters, $translate);
    }

    public function collection(array $data = []): Collection
    {
        return new Collection($data);
    }

    public function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        if (!isset($parameters['_locale'])) {
            $parameters['_locale'] = $this->request->getLocale();
        }
        return parent::redirectToRoute($route, $parameters, $status);
    }

    public function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if (!isset($parameters['_locale'])) {
            $parameters['_locale'] = $this->request->getLocale();
        }
        return parent::generateUrl($route, $parameters, $referenceType);
    }

    public function getYear(?int $year = null): int
    {
        return $year ?? (int) (new DateTime())->format('Y');
    }

    public function dataOrNull(array $data): ?array
    {
        return empty($data) ? null : $data;
    }
}
