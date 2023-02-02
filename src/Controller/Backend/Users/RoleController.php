<?php

namespace App\Controller\Backend\Users;

use App\Entity\Main\Users\Role;
use App\Form\Main\Users\RoleType;
use App\Entity\Main\Users\UserAction;
use App\Form\Main\Users\RoleUpdateType;
use App\Form\Main\Users\UserActionType;
use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Backend\AdminBaseController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\Users\UserActionRepository;
use Illuminate\Support\Collection;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/users/role")
 * @Module(name="base", is_activated=true)
 */
class RoleController extends AdminBaseController
{
    protected $viewPath = 'main/role/';
    /**
     * @Route("/{_locale}", name="app_backend_user_action_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_action_index", title="user.all.action.see.action", targets={"admins"}, is_index=true)
     */
    public function index(): Response
    {
        $roles = $this->collection($this->getRoleRepository()->findAll())
            ->sortBy(fn (Role $item) => count($item->getUserActions()), SORT_REGULAR, true);
        return $this->render('index.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/{_locale}/new", name="app_backend_user_action_new", methods={"GET","POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_action_new", title="user.action.add.action", targets={"ROLE_SUPERADMIN"})
     */
    public function new(Request $request): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $withoutAuth = $this->getUserActionRepository()->findBy(['hasAuth' => false]);
            foreach ($withoutAuth as $action) {
                $role->addUserAction($action);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('app_backend_user_action_index', ['_locale' => $request->getLocale()]);
        }

        return $this->render('new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}", name="app_backend_user_action_show", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_action_show", title="user.action.see.action", targets={"admins"})
     */
    public function show(Role $role): Response
    {
        return $this->render('show.html.twig', [
            'role' => $role,
        ]);
    }

    /**
     * @Route("/{id}/{_locale}/edit", name="app_backend_user_action_edit", methods={"GET","POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_action_edit", title="user.action.edit.action", targets={"admins"})
     */
    public function edit(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleUpdateType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($role);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_backend_user_action_index', ['_locale' => $request->getLocale()]);
        }
        //dd($form->getErrors());
        return $this->render('edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}", name="app_backend_user_action_delete", methods={"POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_action_delete", title="user.action.delete.action", targets={"ROLE_SUPERADMIN"})
     */
    public function delete(Request $request, Role $role): Response
    {
        if (
            $method = ($request->request->get('_method')) and  $method === 'DELETE' and
            $this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token')) and
            $role->getIsDeletable()
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($role);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_user_action_index', ['_locale' => $request->getLocale()]);
    }
}
