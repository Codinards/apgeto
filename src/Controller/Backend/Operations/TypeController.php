<?php

namespace App\Controller\Backend\Operations;

use App\Controller\Backend\AdminBaseController;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Form\Operations\BaseOperationType;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/operations/types")
 * @Module(name="base", is_activated=true)
 */
class TypeController extends AdminBaseController
{

    protected $viewPath = 'operations/type/';

    /**
     * @Route("/{_locale}", name="app_backend_operation_type_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_type_index", title="operation_type.index.action", targets={"admins"}, is_index=true)
     */
    public function index(): Response
    {
        return $this->render('/index.html.twig', [
            'types' => $this->getOperationTypeRepository()->findAll()
        ]);
    }

    /**
     * @Route("{_locale}/{id}", name="app_backend_operation_type_show", methods={"GET"}, requirements={"id":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_type_show", title="operation_type.see.action", targets={"admins"})
     */
    public function show(Type $type): Response
    {
        return $this->render('/show.html.twig', [
            'type' => $type,
            'operations' => $this->collection($type->getOperations()->toArray())->sortBy(fn (Operation $item) => $item->getCreatedAt())
        ]);
    }

    /**
     * @Route("/{_locale}/new", name="app_backend_operation_type_new", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_type_new", title="operation_type.add.action", targets={"admins"})
     */
    public function create(Request $request): Response
    {
        $type = new Type();
        $type->setAdmin($this->getUser());
        $form = $this->createForm(BaseOperationType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager()->persist($type);
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_index');
        }
        return $this->render('/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'title' => $this->trans('operation.type.creation')
        ]);
    }

    /**
     * @Route("/{_locale}/update-{id}", name="app_backend_operation_type_update", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_operation_type_update", title="operation_type.update.action", targets={"admins"})
     */
    public function update(Request $request, Type $type): Response
    {
        if ($type->getIsUpdatable() === false) {
            $this->errorFlash('operation.unauthorize');
            return $this->redirectToRoute('app_backend_operation_type_index');
        }
        $form = $this->createForm(BaseOperationType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_index');
        }
        return $this->render('/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'title' => $this->trans('operation.type.edition')
        ]);
    }
}
