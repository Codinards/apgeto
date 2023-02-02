<?php

namespace App\Controller\Backend\Tontines;

use App\Controller\Backend\AdminBaseController;
use App\Entity\Tontines\Tontinetype;
use App\Form\Tontines\TontinetypeType;
use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;

/**
 * @Route("/backend/tontine/type")
 * @Module(name="tontine", is_activated=false)
 */
class TontinetypeController extends AdminBaseController
{
    protected $viewPath = 'tontines/tontine_type/';

    /**
     * @Route("/{_locale}", name="app_backend_tontinetype_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontinetype_index", title="tontinetype.index.see.action", targets={"admins"}, is_index=true)
     */
    public function index(Request $request): Response
    {
        $currents = $this->collection($this->getTontineRepository()->findBy(['isCurrent' => true]))
            ->map(function ($item) {
                return $item->getType()->getId();
            })->toArray();
        return $this->render('index.html.twig', [
            'types' => $this->getTontinetypeRepository()->findAll(),
            'currents' => $currents
        ]);
    }

    /**
     * @Route("/{_locale}/new", name="app_backend_tontinetype_new", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_tontinetype_new", title="tontinetype.new.action", targets={"admins"})
     */
    public function new(Request $request): Response
    {
        $tontineType = new Tontinetype();
        $form = $this->createForm(TontinetypeType::class, $tontineType);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $manager = $this->getTontineManager();
            $manager->persist($tontineType);
            $manager->flush();
            $this->flashMessage('success', 'operation.success');
            return $this->redirectToRoute("app_backend_tontinetype_index", ['_locale' => $request->getLocale()]);
        }
        return $this->render('new.html.twig', [
            'type' => $tontineType,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-update", name="app_backend_tontinetype_update", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_tontinetype_update", title="tontinetype.update.action", targets={"admins"})
     */
    public function update(Request $request, Tontinetype $type): Response
    {
        if (!$type->getTontines()->isEmpty()) {
            $this->errorFlash('tontinetype.already.has.tontine');
            return $this->redirectToRoute("app_backend_tontinetype_index", ['_locale' => $request->getLocale()]);
        }
        $form = $this->createForm(TontinetypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getTontineManager();
            $manager->flush();
            $this->flashMessage('success', 'operation.success');
            return $this->redirectToRoute("app_backend_tontinetype_index", ['_locale' => $request->getLocale()]);
        }
        return $this->render('new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'update' => true,
            'title' => 'tontinetype.update',
            'button_label' => 'update'
        ]);
    }
}
