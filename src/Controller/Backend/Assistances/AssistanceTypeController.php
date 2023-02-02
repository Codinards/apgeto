<?php

namespace App\Controller\Backend\Assistances;

use App\Controller\Backend\AdminBaseController;
use App\Entity\Assistances\AssistanceType;
use App\Form\Assistances\BaseAssistanceType;
use App\Repository\Assistances\AssistanceTypeRepository;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/base/assistance")
 * @Module(name="assistance", is_activated=true)
 */
class AssistanceTypeController extends AdminBaseController
{
    protected $viewPath = 'assistances/assistance_type/';

    /**
     * @Route("/{_locale}", name="app_backend_assistance_type_index", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_assistance_type_index", title="assistance.type.index.action", targets={"admins"}, is_index=true)
     */
    public function index(AssistanceTypeRepository $assistanceTypeRepository): Response
    {
        return $this->render("index.html.twig", [
            'types' => $assistanceTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{_locale}/new", name="app_backend_assistance_type_new", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_assistance_type_new", title="assistance.type.new.action", targets={"admins"})
     */
    public function new(Request $request): Response
    {
        $assistanceType = new AssistanceType();
        $form = $this->createForm(BaseAssistanceType::class, $assistanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getAssistanceManager();
            $entityManager->persist($assistanceType);
            $entityManager->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_assistance_type_index');
        }

        return $this->render("new.html.twig", [
            'base_assistance' => $assistanceType,
            'form' => $form->createView(),
        ]);
    }

    /*
     * @Route("/{id}", name="base_assistance_show", methods={"GET"})
     
    public function show(AssistanceType $assistanceType): Response
    {
        return $this->render("{$this->viewPath}/show.html.twig", [
            'base_assistance' => $assistanceType,
        ]);
    }*/

    /**
     * @Route("/{_locale}/{id}-edit", name="app_backend_assistance_type_edit", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_assistance_type_edit", title="assistance.type.edit.action", targets={"admins"})
     */
    public function edit(Request $request, AssistanceType $assistanceType): Response
    {
        $oldType = clone $assistanceType;
        $form = $this->createForm(BaseAssistanceType::class, $assistanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getAssistanceManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_assistance_type_index');
        }

        $response = $this->render("edit.html.twig", [
            'base_assistance' => $assistanceType,
            'form' => $form->createView(),
        ]);

        /*preg_match("!<html([^>]+)?>(.*)</html>!Ui", $response->getContent(), $matches);class="navbar fixed-top bg-dark" style="font-size: 0.8rem;"*/
        /*$content = $response->getContent();
        $data = [];
        while (preg_match('/<nav(.+)>(.+)<\/nav>/isU', $content, $matches)) {
            $data[] = $matches[0];
            $content = str_replace($matches[0], '', $content);
        }
        dd($data);*/
        return $response;
    }

    /**
     * @Route("/{id}", name="base_assistance_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AssistanceType $assistanceType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $assistanceType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($assistanceType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('base_assistance_index');
    }
}
