<?php

namespace App\Controller\Backend\Tontines;

use App\Controller\Backend\AdminBaseController;
use App\Entity\Tontines\CotisationDay;
use App\Entity\Tontines\CotisationFailure;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\TontineurData;
use App\Entity\Utils\BaseCotisation;
use App\Form\Utils\BaseCotisationType;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/tontine/cotisation/", name="app_backend_tontine_cotisation_")
 * @Module(name="tontine", is_activated=false)
 */
class CotisationController extends AdminBaseController
{
    protected $viewPath = 'tontines/cotisation/';
    /**
     * @Route("{_locale}/{type}", name="index", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "type":"day|tontiner"})
     * @RouteAction(name="app_backend_tontine_cotisation_index", title="tontine.cotisation.see.action", targets={"admins"}, is_index=true)
     */
    public function index(string $type = 'day'): Response
    {
        if ($type === 'day') {
            $data = $this->getCotisationDayRepository()->findBy([], ['createdAt' => 'DESC']);
        } else {
            $data = $this->getCotisationFailureRepository()->findByConditions([], ['createdAt' => 'DESC'], 1000);
            $data = array_merge(($this->collection($data))
                    ->sortBy(fn (CotisationFailure $item) => $item->getCotisationDay()->getCreatedAt())
                    ->map(fn ($item) => $item->getTontiner())
                    ->keyBy(fn ($item) => $item->getTontineur()->getId())
                    ->toArray()
            );
        }

        return $this->render('index.html.twig', [
            'cotisationData' => $data,
            'title' => $type === 'day' ? 'Gestions des cotisations et échecs' : 'Gestions des cotisations et échecs | Membres ',
            'type' => $type
        ]);
    }

    /**
     * @Route("{_locale}/create", name="create_failure", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(
     *  name="app_backend_tontine_cotisation_create_failure",
     *  title="tontine.cotisation.create.failure.action",
     *  targets={"admins"}
     * )
     */
    public function cotisationFailure(Request $request)
    {
        $currentTontines = $this->getTontineRepository()->findBy(['isCurrent' => true]);
        $cotisationmembres = $this->collection();
        /** @var Tontine $tontine */
        foreach ($currentTontines as $tontine) {
            $cotisationmembres = $cotisationmembres->merge($tontine->getTontineurData()->toArray());
        }
        $grouped = $cotisationmembres
            ->sortBy(fn (TontineurData $data) => $data->getTontineur()->getName())
            ->groupBy(fn (TontineurData $data) => $data->getTontineur()->getName());

        $baseCotisation = (new BaseCotisation($grouped->toArray()))
            ->setDate(new CotisationDay());

        $form = $this->createForm(BaseCotisationType::class, $baseCotisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $baseCotisation->handleFailure($this->getManager());
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_tontine_cotisation_index');
        }

        return $this->render('create_failure.html.twig', [
            'form' => $form->createView(),
            'currentCount' => count($currentTontines),
            'members' => $grouped->keys()
        ]);
    }

    /**
     * @Route("{_locale}/day-{day}", name="day_show", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "day":"\d+"})
     * @RouteAction(name="app_backend_tontine_cotisation_day_show", title="tontine.cotisation.day.show.action", targets={"admins"})
     */
    public function showDayFailure(CotisationDay $day)
    {
        return $this->render('day_failure.html.twig', [
            'cotisationDay' => $day,
            'failures' => $day->getCotisationFailures()
        ]);
    }

    /**
     * @Route("{_locale}/tontiner-{tontiner}", name="tontiner_show", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "member":"\d+"})
     * @RouteAction(name="app_backend_tontine_cotisation_tontiner_show", title="tontine.cotisation.tontiner.show.action", targets={"admins"})
     */
    public function showMemberFailure(TontineurData $tontiner)
    {
        $failures = $this->getCotisationFailureRepository()->findBy(['tontiner' => $tontiner], ['createdAt' => 'DESC']);
        return $this->render('tontiner_failure.html.twig', [
            'tontiner' => $tontiner,
            'failures' => $failures
        ]);
    }
}
