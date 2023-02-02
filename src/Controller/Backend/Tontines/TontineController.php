<?php

namespace App\Controller\Backend\Tontines;

use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Exceptions\TontineOperationException;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\TontineTrait;
use App\Entity\Tontines\Tontinetype;
use App\Entity\Tontines\Tontineur;
use App\Entity\Tontines\TontineurData;
use App\Entity\Tontines\Unity;
use App\Form\Tontines\TontineType as TontinesTontineType;
use App\Form\Tontines\TontineUpdateType;
use App\Form\Tontines\TontineurDataType;
use App\Form\Tontines\TontineurType;
use App\Tools\AppConstants;
use App\Tools\Entity\DataProvider;
use App\Tools\Entity\TontineInfoResolver;
use App\Tools\Tontines\TontineTool;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;

/**
 * @Route("/backend/tontine/")
 * @Module(name="tontine", is_activated=false)
 */
class TontineController extends AdminBaseController
{
    use TontinesControllerTrait;

    protected $viewPath = 'tontines/tontine/';

    /**
     * @Route("{_locale}", name="app_backend_tontine_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontine_index", title="tontine.index.see.action", targets={"admins"}, is_index=true)
     */
    public function index(): Response
    {


        $tontineTypes = $this->collection(
            $this->getTontineRepository()->findBy(['isCurrent' => true])
        )->map(fn ($tontine) => $tontine->getType())->toArray();

        $types = $this->collection(
            $this->getTontinetypeRepository()
                ->findAll()
        )->filter(
            fn (Tontinetype $type) => $type->getHasMultipleTontine() ? true : !in_array($type, $tontineTypes)
        );
        //dd($types);
        return $this->render('index.html.twig', [
            'tontines' => $this->collection($this->getTontineRepository()->findAll())
                ->sortByDesc(fn (Tontine $tontine) => $tontine->getCreatedAt()),
            'types' => $types->isEmpty() ? null : $types,
        ]);
    }

    /**
     * @Route("{_locale}/{id}-show", name="app_backend_tontine_show", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontine_show", title="tontine.see.action", targets={"admins"})
     */
    public function show(Tontine $tontine): Response
    {
        $unities = $this->collection(
            $tontine->getUnities()->toArray()
        )->sortBy(
            fn (Unity $item) => $item->getTontineur()->getName()
        );
        return $this->render('show.html.twig', [
            'unities' => $unities,
            'data' => $tontine->getTontineurData(),
            'tontine' => $tontine,
            'is_valid' => $tontine->getIsCurrent(),
        ]);
    }


    /**
     * @Route("{_locale}/{id}-delete", name="app_backend_tontine_delete", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontine_delete", title="Supprimer une tontine en cours", targets={"ROLE_SUPERADMIN"})
     */
    public function delete(Tontine $tontine): Response
    {
        return $this->render('show.html.twig', [
            'unities' => $unities,
            'data' => $tontine->getTontineurData(),
            'tontine' => $tontine,
            'is_valid' => $tontine->getIsCurrent(),
        ]);
    }

    /**
     * @Route("{_locale}/{id}-update-data", name="app_backend_tontine_update_data", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontine_update_data", title="tontine.update.data.action", targets={"admins"})
     */
    public function updateData(Request $request, Tontine $tontine): Response
    {
        if (!$tontine->getIsCurrent()) {
            $this->successFlash("Cette tontine n'est plus en cours. Vous ne pouvez plus modifier ses paramètres");
            return $this->redirectToRoute('app_backend_tontine_index', ['_locale' => $request->getLocale()]);
        }

        $form = $this->createForm(TontineUpdateType::class, $tontine);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $this->getTontineManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_tontine_show', ['id' => $tontine->getId(), '_locale' => $request->getLocale()]);
        }
        return $this->render('update_data.html.twig', [
            'tontine' => $tontine,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/{id}-winners", name="app_backend_tontine_details", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontine_details", title="tontine.winners.action", targets={"admins"})
     */
    public function details(Tontine $tontine): Response
    {
        $tontineData = $this->collection($tontine->getTontineurData()->toArray())
            ->sortBy(fn (TontineurData $tontineurData) => $tontineurData->getTontineur()->getName())
            ->keyBy(fn ($item) => $item->getId());
        $unities = $this->collection($tontine->getUnities()->toArray())
            ->sortBy(fn (Unity $unity) => $unity->getTontineur()->getName())
            ->groupBy(fn ($item) => $item->getTontineurData()->getId());
        return $this->render('details.html.twig', [
            'unities' => $unities,
            'data' => $tontineData,
            'tontine' => $tontine,
            'is_valid' => $tontine->getIsCurrent(),
        ]);
    }

    /**
     * @Route("{_locale}/{id}-info", name="app_backend_tontine_info", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontine_info", title="tontine.info.action", targets={"admins"})
     */
    public function info(Tontine $tontine, TontineInfoResolver $tontineInfoResolver): Response
    {
        /*$tontineData = $this->collection($tontine->getTontineurData()->toArray())
            ->keyBy(fn ($item) => $item->getId());
        $unities = $this->collection($tontine->getUnities()->toArray())
            ->groupBy(fn ($item) => $item->getTontineurData()->getId());*/
        $unities = $this->collection(
            $this->collection($tontine->getWonUnities()->toArray())
                ->sortBy(fn ($item) => $item->getBenefitAt())->toArray()
        )
            ->groupBy(fn (Unity $unity) => $unity->getBenefitAt()->format('Y-m-d'));
        return $this->render('info.html.twig', [
            'unities' => $unities,
            'tontine' => $tontine,
            'resolver' => $tontineInfoResolver
        ]);
    }

    /**
     * @Route("{_locale}/{type}-new", name="app_backend_tontine_new", methods={"GET", "POST"}, requirements={"type":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontine_new", title="tontine.new.action", targets={"admins"})
     */
    public function newFromType(Request $request, TontineTool $requestTool, Tontinetype $type): Response
    {
        $manager = $this->getTontineManager();

        $current = $manager->getRepository(Tontine::class)->findOneBy(['type' => $type, 'isCurrent' => true]);
        if ($current and !$type->getHasMultipleTontine()) {
            $this->flashMessage('error', 'tontinetype.tontine.current.exists', ['%type%' => $type]);
            return $this->redirectToRoute(
                'app_backend_tontine_show',
                ['id' => $current->getId(), '_locale' => $request->getLocale()]
            );
        }
        $tontineurs = $this->getTontineurRepository()->findAll();

        $tontine = (new Tontine())
            ->setAdmin($this->getUser())
            ->hydrateDataFromTontineur($tontineurs)
            ->setType($type);
        if ($request->isMethod('GET')) {
            /** introduction of data in the provider by retrieve its in the form */
            DataProvider::getInstance()->setData($tontine->getTontineurData()->toArray());
        }

        $form = $this->createForm(TontinesTontineType::class, $tontine);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $tontine = $requestTool
                ->resolveCreateNewTontineMember($request, $tontine, $this->getUser());
        }

        if ($form->isSubmitted() and $form->isValid()) {

            $manager->persist($tontine);
            $manager->flush();
            $this->addFlash('success', $this->trans('operation.success'));
            return $this->redirectToRoute('app_backend_tontine_show', ['id' => $tontine->getId(), '_locale' => $request->getLocale()]);
        }
        $usersData = [];
        /** @var Tontineur $tontineur */
        foreach ($tontine->getTontineurs() as $tontineur) {

            $usersData[] = [
                'id' =>  $tontineur->getUser()->getId(),
                'name' => $tontineur->getName(),
                'checked' => false,
                'count' => 0,
                'half' => 0,
            ];
        }

        $attributesData = [
            'data' => [
                'cotisation_label' => $this->trans('cotisation'),
                'half_label' => $this->trans('half.name'),
                'and_half_label' => $this->trans('half.and.name'),
                'none' => $this->trans('nona'),
                'count_label' => $this->trans('part.numbers'),
                'half_check_label' => $this->trans('half.part'),
                'unityMaxCount' => AppConstants::$TONTINEUR_MAX_COUNT,
                'unity' => $this->trans('unity'),
                'unities' => $this->trans('unities'),
                //'won' => 0
            ],

            'tontineurs' => $usersData,
        ];

        return $this->render("new.html.twig", [
            'tontine' => $tontine,
            'form' => $form->createView(),
            'type' => $tontine->getType(),
            'add' => false,
            'attributes_data' => json_encode($attributesData),
            'total_data' => '{' .
                '"half_label":"' . $this->trans('half.name') .
                '", "and_half_label":"' . $this->trans('half.and.name') .
                '", "unity":"' . $this->trans('unity') . '"}'
        ]);
    }

    /**
     * @Route("{_locale}/{id}-member-add-{tontineur}/{data}", name="app_backend_tontineur_unity_add", methods={"GET", "POST"},
     * requirements={"id":"\d+", "tontineur":"\d+", "data":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontineur_unity_add", title="tontineur.unity.add.action", targets={"admins"})
     */
    public function addTontineurUnities(Request $request, Tontine $tontine, Tontineur $tontineur, TontineurData $data): Response
    {

        $this->throwRedirectRequest(
            !$tontine->getAddMember(),
            ['app_backend_unity_show', ['id' => $tontine->getId()]],
            'Impossible d\'ajouter de nouvelles unités à cette tontine'
        );

        $this->throwRedirectRequest(
            (($data->getTontine()->getId() !== $tontine->getId()) || ($data->getTontineur()->getId() !== $tontineur->getId())),
            ['app_backend_tontine_details', ['id' => $tontine->getId()]],
            $this->trans('add_unity.invalid.data')
        );

        $tontineurData = (new TontineurData);
        $form = $this->createForm(TontineurDataType::class, $tontineurData, ['attr' => ['value' => $tontineur]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $unities = [];
            $count = $tontineurData->getCount();
            $halfUnity = $tontineurData->getDemiNom();
            if ($count === 0 and $halfUnity === false) {
                $this->errorFlash('any unity specify for this tontineur');
                return $this->redirectToRoute(
                    'app_backend_tontineur_unity_add',
                    ['id' => $tontine->getId(), 'tontineur' => $tontineur->getId(), 'data' => $data->getId()]
                );
            } else {
                for ($i = 1; $i <= $count; $i++) {

                    $unities[] = (new Unity)
                        ->setTontine($tontine)
                        ->setTontineur($tontineur)
                        ->setTontineurData($data)
                        ->setAmount($tontine->getType()->getCotisation())
                        ->setAdmin($this->getUser());
                    $tontine->incrementCount();
                    $tontine->incrementCotisation($tontine->getType()->getCotisation());
                    $data->incrementCount();
                }

                if ($halfUnity) {

                    if ($data->getCountDemiNom() !== 0) {

                        /** @var Unity $odlHalf */
                        $odlHalf = $data->getUnities()->filter(
                            fn (Unity $unity) => $unity->getIsDemiNom()
                        )->first();

                        /** Throw error */
                        $this->throwRedirectRequest(
                            $odlHalf->getIsWon(),
                            ['app_backend_tontine_details', ['id' => $tontine->getId()]],
                            $this->trans('tontineur.already.have.won.half_unity')
                        );
                        /** End throw error */

                        $odlHalf
                            ->setAmount($tontine->getType()->getCotisation())
                            ->setIsDemiNom(false)
                            ->setAdmin($this->getUser());
                        $tontine->decrementCountDemiNom();
                        $tontine->incrementCotisation($tontine->getType()->getCotisation() / 2);
                        $data->decrementCountDemiNom();
                        $data->setDemiNom(false);
                    } else {
                        $unities[] = (new Unity)
                            ->setTontine($tontine)
                            ->setTontineur($tontineur)
                            ->setTontineurData($data)
                            ->setAmount($tontine->getType()->getCotisation() / 2)
                            ->setAdmin($this->getUser());
                        $tontine->incrementCount();
                        $tontine->incrementCountDemiNom();
                        $tontine->incrementCotisation($tontine->getType()->getCotisation() / 2);
                        $data->incrementCount();
                        $data->incrementCountDemiNom();
                        $data->setDemiNom(true);
                    }
                }
            }
            if ($tontine->getMaxCount() < $maxCount = $data->getCount()) $tontine->setMaxCount($maxCount);

            $manager = $this->getTontineManager();
            foreach ($unities as $unity) {
                $manager->persist($unity);
            }

            $manager->flush();

            $this->successFlash($this->trans('operation.success'));

            return $this->redirectToRoute('app_backend_tontine_details', ['id' => $tontine->getId()]);
        }

        return $this->render("add_unity.html.twig", [
            'tontine' => $tontine,
            'tontineur' => $tontineur,
            'data' => $data,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/{id}-member-add", name="app_backend_tontine_member_add", methods={"GET", "POST"}, requirements={"id":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontine_member_add", title="tontine.member.add.action", targets={"admins"})
     */
    public function addMember(Request $request, TontineTool $requestTool, Tontine $basetontine): Response
    {

        $this->throwRedirectRequest(
            !$basetontine->getAddMember(),
            ['app_backend_tontine_show', ['id' => $basetontine->getId()]],
            'Impossible d\'ajouter de nouveaux membres à cette tontine'
        );
        $manager = $this->getTontineManager();
        $oldTontineurs = $this->collection($basetontine->getTontineurData()->toArray())
            ->keyBy(fn (TontineurData $data) => $data->getTontineur()->getUser()->getId())
            ->toArray();
        $tontineurs = $this->collection($this->getTontineurRepository()->findAll())
            ->filter(fn (Tontineur $tontiner) => !in_array($tontiner->getId(), array_keys($oldTontineurs)))
            ->toArray();
        if (empty($tontineurs)) {
            $this->errorFlash('Aucun membre à ajouter à cette tontine');
            return $this->redirectToRoute('app_backend_tontine_show', ['id' => $basetontine->getId()]);
        }
        $tontine = (new Tontine)
            ->setAdmin($this->getUser())
            ->hydrateDataFromTontineur($tontineurs)
            ->setType($basetontine->getType())
            ->setName($basetontine->getName())
            ->setCreatedAt($basetontine->getCreatedAt())
            ->setAmount($basetontine->getAmount())
            ->setWon($basetontine->getWon());

        if ($request->isMethod('GET')) {
            /** introduction of data in the provider by retrieve its in the form */
            DataProvider::getInstance()->setData($tontine->getTontineurData()->toArray());
        }

        $form = $this->createForm(TontinesTontineType::class, $tontine, ['attr' => ['add' => true]]);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            $tontine = $requestTool
                ->resolveCreateNewTontineMember($request, $tontine, $this->getUser(), $basetontine);
        }

        if ($form->isSubmitted() and $form->isValid()) {
            foreach ($tontine->getTontineurData() as $data) {
                $manager->persist($data);
            }
            $manager->flush();
            $this->addFlash('success', $this->trans('operation.success'));
            return $this->redirectToRoute('app_backend_tontine_show', ['id' => $basetontine->getId(), '_locale' => $request->getLocale()]);
        }

        $usersData = [];
        /** @var Tontineur $tontineur */
        foreach ($tontine->getTontineurs() as $tontineur) {
            $usersData[] = [
                'id' =>  $tontineur->getUser()->getId(),
                'name' => $tontineur->getName(),
                'checked' => false,
                //'oldChecked' => false,
                'count' => 0,
                'half' => 0,
                //'oldCount' => 0,
                //'oldHalf' => 0,
                //'won' => 0
            ];
        }

        $attributesData = [
            'data' => [
                'cotisation_label' => $this->trans('cotisation'),
                'half_label' => $this->trans('half.name'),
                'and_half_label' => $this->trans('half.and.name'),
                'none' => $this->trans('nona'),
                'count_label' => $this->trans('part.numbers'),
                'half_check_label' => $this->trans('half.part'),
                'unityMaxCount' => AppConstants::$TONTINEUR_MAX_COUNT,
                'unity' => $this->trans('unity'),
                'unities' => $this->trans('unities')
            ],

            'tontineurs' => $usersData,
        ];

        return $this->render("new.html.twig", [
            'tontine' => $tontine,
            'basetontine' => $basetontine,
            'form' => $form->createView(),
            'type' => $tontine->getType(),
            'add' => true,
            'attributes_data' => json_encode($attributesData),
            'total_data' => '{' .
                '"half_label":"' . $this->trans('half.name') .
                '", "and_half_label":"' . $this->trans('half.and.name') .
                '", "unity":"' . $this->trans('unity') . '"}'
        ]);
    }

    /**
     * @Route("{_locale}/{id}-stop-add-member", name="app_backend_tontine_stop_add_member", methods={"POST"}, requirements={"id":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontine_stop_add_member", title="tontine.stop.add.member.action", targets={"admins"})
     */
    public function stopAddingMember(Request $request, TontineTool $requestTool, Tontine $tontine): Response
    {
        $manager = $this->getTontineManager();
        $tontine->setAddMember(false);
        $manager->flush();
        $this->flashMessage('success', 'operation.success');
        return $this->redirectToRoute('app_backend_tontine_show', ['id' => $tontine->getId()]);
    }
}
