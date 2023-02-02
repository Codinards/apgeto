<?php

namespace App\Controller\Backend\Assistances;

use App\Controller\Backend\Accounts\HandleAccountFlow;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\AssistanceType;
use App\Entity\Assistances\Contributor;
use App\Entity\Exceptions\AssistanceManagerException;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Entity\Utils\AssistanceFilter;
use App\Entity\Utils\ContributorFilter;
use App\Events\Fund\CreateFundEvent;
use App\Events\Fund\DeleteFundEvent;
use App\Form\Assistances\AddAssistanceContributorType;
use App\Form\Assistances\AssistanceType as AssistanceFormType;
use App\Entity\Assistances\AddContributorAssistance;
use App\Form\Assistances\Utils\AddContributorAssistanceType;
use App\Form\Assistances\Utils\AssistanceDeleteType;
use App\Form\Assistances\Utils\AssistanceFilterType;
use App\Form\Utils\ContributorFilterType;
use App\Tools\AppConstants;
use App\Tools\Entity\AccountManager;
use App\Tools\Entity\AssistanceManager;
use App\Tools\Entity\ContributorData;
use Exception;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/assistance")
 * @Module(name="assistance", is_activated=true)
 */
class AssistanceController extends AdminBaseController
{
    use HandleAccountFlow;

    protected $viewPath = 'assistances/assistance/';

    /**
     * @Route("/{_locale}/{type}", name="app_backend_assistance_index", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "type":"\d+"})
     * @RouteAction(name="app_backend_assistance_index", title="assistance.index.action", targets={"admins"}, is_index=true)
     */
    public function index(Request $request, AssistanceType $type = null): Response
    {
        $filter = new AssistanceFilter();
        $form = $this->createForm(
            AssistanceFilterType::class,
            $filter,
            [
                'action' => $this->generateUrl(
                    'app_backend_assistance_index',
                    ['_locale' => $request->getLocale()]
                )
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $assistances = $this->getAssistanceRepository()->findWithFilter($filter, $type ? ['type' => $type] : []);
        } else {
            $assistances = $type ? $this->getAssistanceRepository()->findBy(['type' => $type])
                : $this->getAssistanceRepository()->findAll();
        }


        $base_assistances = $this->getAssistanceTypeRepository()->findAll();
        return $this->render('index.html.twig', [
            'assistances' => $this->collection($assistances)->sortBy(fn (Assistance $assistance) => $assistance->getCreatedAt(), descending: true),
            'base_assistances' => $base_assistances,
            'type' => $type,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{_locale}/{id}/new-{type}", name="app_backend_assistance_new", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+", "type":"\d+"})
     * @RouteAction(name="app_backend_assistance_new", title="assistance.new.action", targets={"admins"})
     */
    public function new(
        Request $request,
        User $user,
        AssistanceType $type,
        AssistanceManager $assManager,
        AccountManager $accountManager
    ): Response {
        /** filtration de la requete */
        $filter = new ContributorFilter;
        $filterForm = $this->createForm(ContributorFilterType::class, $filter);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() and $filterForm->isValid()) {
            $users = $this->getUserRepository()->findWithFilter($filter);
        } else {
            $users = $this->getUserRepository()->findBy([]);
        }

        if (!AppConstants::$FUND_CAN_BE_NEGATIVE) {
            $users = $this->collection($users)->filter(fn ($user) => $user->getAccount()->getCashBalances() > 0);
            $users = array_merge([], $users->toArray());
        }
        $users = [...$this->collection($users)
            ->sortBy(fn (User $user) => $user->getUsername())
            ->toArray()];
        /** creation de l'assistance */
        $assistance = new Assistance();
        $assistance
            ->setAdmin($this->getUser())
            ->setType($type)
            ->setUser($user);
        $assistance->hydrateContributorsFromUsers($users);
        $count = count($users);
        $amount = $type->getIsAmount()
            ? $type->getAmount()
            : (
                (is_null($type->getIsAmount()) or empty($count))
                ? null
                : $type->getAmount() / $count
            );
        $assistance->setAmount($amount);
        ContributorData::getInstance()->setContributors($users);

        $form = $this->createForm(AssistanceFormType::class, $assistance);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            // Insertion du montant pour les types d'aide non volontaire
            if ($assistance->getAmount()) {
                if ($request->query->get('amount')) {
                    $assistance->updateAmount($request->query->get('amount'));
                } else {
                    $contributor1 = $assistance->getContributors()[0];
                    $assistance->setAmount($contributor1->getAmount());
                }
            }

            // filtration des contributeurs non selectionner
            $assistance = $assistance->filterContributors();

            if ($assistance->getContributors()->count() === 0) {
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_assistance_new', [
                        'id' => $user->getId(),
                        'type' => $type->getId()
                    ]),
                    $this->trans('assistance.not.get.contributors', ['%type%' => $type]),
                    true
                );
            }

            // construction des sorties de fonds pour les contributeurs
            try {
                $assistance = $assManager->manageCreateAssistance($assistance, $this->getUser(), $accountManager);
            } catch (AssistanceManagerException $e) {
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_assistance_new', [
                        'id' => $user->getId(),
                        'type' => $type->getId()
                    ]),
                    $this->trans($e->getMessage(), $e->getParams()),
                    true
                );
            }

            // Sauvegarder des donnees
            $manager = $this->getManager();
            $manager->getConnection()->beginTransaction();
            try {
                $manager->persist($assistance);
                /** @var Contributor $contributor */
                foreach ($assistance->getContributors() as $contributor) {
                    /** @var Fund $fund */
                    $fund = $contributor->getFund();
                    $manager->persist($fund);
                    $this->dispatcher->dispatch(new CreateFundEvent($fund));
                }

                $manager->flush();
                $manager->getConnection()->commit();
                //$manager->getConnection()->commit();
            } catch (Exception $e) {
                $manager->getConnection()->rollBack();
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_assistance_new', ['id' => $user->getId(), 'type' => $type->getId()]),
                    $this->trans('operation.fail'),
                    //dd($e->getMessage()),
                    true
                );
            }

            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_assistance_index', ['type' => $type->getId()]);
        }
        $data = array_map(function (User $user, $index) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'cashBalances' => $user->getAccount()->getCashBalances(),
                'index' => $index
            ];
        }, $users, array_keys($users));

        return $this->render('new.html.twig', [
            'assistance' => $assistance,
            'form' => $form->createView(),
            'member' => $user,
            'type' => $type,
            'filterForm' => $filterForm->createView(),
            'data' => json_encode($data),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-add-contributor", name="app_backend_assistance_add_contributor", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_assistance_add_contributor", title="assistance.add_contributor.action", targets={"admins"})
     */
    public function add(Request $request, Assistance $assistance): Response
    {
        $addContributorAssistance = new AddContributorAssistance;
        $cloned = clone $assistance;
        $cloned->reinitializeContributor();
        $addContributorAssistance->setContributors($this->getUserRepository()->findAll());
        $form = $this->createForm(AddContributorAssistanceType::class, $addContributorAssistance);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
        }
        return $this->render("select_contributors.html.twig", [
            'assistances' => $assistance,
            'form' => $form->createView()
        ]);
    }

    /**
     * Show an assistance informations
     * 
     * @Route("/{_locale}/{id}-show", name="app_backend_assistance_show", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_assistance_show", title="assistance.show.action", targets={"admins"})
     *
     *
     * @param Assistance $assistance
     * @return Response
     */
    public function show(Assistance $assistance): Response
    {
        // dd($this->collection(
        //     $assistance->getContributors()->toArray()
        // )->map(fn (Contributor $cont) => $cont->getUser()->getId()));
        return $this->render('show.html.twig', [
            'assistance' => $assistance,
            'addables' => json_encode($this->collection($this->getUserRepository()
                ->findByConditions([
                    "notin" => [
                        "field" => "id",
                        "values" => $this->collection(
                            $assistance->getContributors()->toArray()
                        )
                            ->map(fn (Contributor $cont) => $cont->getUser()->getId())
                            ->toArray()
                    ]
                ]))
                ->map(fn (User $user) => [
                    "id" => $user->getId(),
                    "name" => $user->getName(),
                    "user" => serialize($user)
                ]))
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-delete", name="app_backend_assistance_delete", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_assistance_delete", title="assistance.delete.action", targets={"ROLE_SUPERADMIN"})
     *
     *
     * @param Request $request
     * @param Assistance $user
     * @return Response
     */
    public function delete(
        Request $request,
        Assistance $assistance,
        AssistanceManager $assistanceManager,
        AccountManager $accountManager
    ): Response {
        $form = $this->createForm(AssistanceDeleteType::class, $assistance);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            // $assistanceManager->manageDelete($assistance, $this->getUser(), $accountManager);

            // Sauvegarder des donnees
            $manager = $this->getManager();
            $manager->getConnection()->beginTransaction();
            try {
                // /** @var Contributor $contributor */
                foreach ($assistance->getContributors() as $key => $contributor) {
                    //     /** @var Fund $fund */
                    //     $fund = $contributor->getFund();
                    //     $manager->persist($fund);
                    $this->dispatcher->dispatch(new DeleteFundEvent($contributor->getFund()));
                    //     $manager->remove($fund);
                    $manager->remove($contributor);
                }
                $manager->remove($assistance);
                $manager->flush();
                $manager->getConnection()->commit();
            } catch (Exception $e) {
                $manager->getConnection()->rollBack();
                $this->throwRedirectRequest(
                    true,
                    $this->generateUrl('app_backend_assistance_show', ['id' => $assistance->getId()]),
                    $this->trans('operation.fail'),
                    true
                );
            }

            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_assistance_index');
        }

        return $this->render('delete.html.twig', [
            'assistance' => $assistance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * aides beneficiees par un membres
     * @Route("/{id}-contribute-assistance", name="user_contribute_assistance", requirements={"id":"\d+"})
     *
     * @param Request $request
     * @param User $member
     * @return Response
     */
    public function memberAssistanceContributed(Request $request, User $member): Response
    {
        $aides = $this->getAssistanceRepository()->findAll();

        return $this->render('contribute.html.twig', [
            'aides' => $aides,
            'member' => $member
        ]);
    }
}
