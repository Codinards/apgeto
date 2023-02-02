<?php

declare(strict_types=1);

namespace App\Controller\Backend\Users;

use App\Entity\Main\Users\User;
use App\Form\Main\Users\UserType;
use App\Form\Main\Users\UserRoleType;
use App\Form\Main\Users\UserUpdateType;
use App\Form\Main\Users\UserPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Backend\AdminBaseController;
use App\Tools\Entity\BaseRoleProperties;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/backend/users/user")
 * @Module(name="base", is_activated=true)
 */
class UserController extends AdminBaseController
{
    protected $viewPath = 'main/user/';
    /**
     * @Route("/{_locale}", name="app_backend_user_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_index", title="user.all.see.action", targets={"admins"}, is_index=true)
     */
    public function index(): Response
    {
        return $this->render("index.html.twig", [
            'title' => 'user.index',
            'users' => $this->collection($this->getUserRepository()->findAll())
                ->sortBy(fn (User $user) => $user->getUsername()),
        ]);
    }

    /**
     * @Route("/{_locale}/locked-users", name="app_backend_locked_users", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_locked_users", title="locked.users.action", targets={"admins"})
     */
    public function lockedUsers(): Response
    {
        return $this->render("locked.html.twig", [
            'title' => 'locked.users',
            'users' => $this->getUserRepository()->findBy(['locked' => true]),
        ]);
    }

    /**
     * @Route("/{_locale}/new-user", name="app_backend_user_new", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_user_new", title="user.new.action", targets={"admins"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getManager();
            $user->setAdmin($this->getUser());
            $manager->persist($user);
            $manager->flush();
            $this->flashMessage('success', 'operation.success');
            if ($request->query->get('redirect') !== null) {
                return $this->redirectToRoute("app_backend_account_index", ['_locale' => $request->getLocale()]);
            }
            return $this->redirectToRoute("app_backend_user_index", ['_locale' => $request->getLocale()]);
        }
        return $this->render('new.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/user-{id}", name="app_backend_user_show", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt","id":"\d+"})
     * @RouteAction(name="app_backend_user_show", title="user.see.action", targets={"admins"})
     */
    public function show(User $user): Response
    {
        return $this->render("show.html.twig", [
            'user' => $user,
            'assistances_types' => $this->getAssistanceTypeRepository()->findAll()
        ]);
    }

    /**
     * @Route("/{_locale}/user-{id}&edit-0", name="app_backend_user_edit", methods={"GET","POST"}, requirements={"_locale":"en|fr|es|it|pt","id":"\d+"})
     * @RouteAction(name="app_backend_user_edit", title="user.edit.action", targets={"admins"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->flashMessage(
                'success',
                "member name informations have been successfully modified",
                ['%name%' => (string) $user]

            );
            return $this->redirectToRoute('app_backend_user_show', ['_locale' => $request->getLocale(), 'id' => $user->getId()]);
        }

        return $this->render("edit.html.twig", [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/user-{id}&edit-role", name="app_backend_user_role_edit", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt","id":"\d+"})
     * @RouteAction(name="app_backend_user_role_edit", title="user.role.edit.action", targets={"ROLE_SUPERADMIN"}, is_updatable=false)
     */
    public function role(Request $request, User $user): Response
    {
        $this->throwRedirectRequest(
            $user->getId() === $this->getUser()->getId(),
            $this->urlsessionManager->getOldUrl(),
            $this->trans("an admin can not edit his own role", [
                '%name%' => $this->trans($user->getRole()->getTitle())
            ]),
            true
        );
        if (!$user->getPassword()) {

            $this->flashMessage(
                'error',
                "member name need to get an password before changing role",
                ['%name%' => (string) $user]
            );
            return $this->redirectToRoute(
                'app_backend_user_show',
                [
                    'id' => $user->getId(),
                    '_locale' => $request->getLocale()
                ]
            );
        }
        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getManager();
            $manager->flush();
            $this->flashMessage(
                'success',
                "member name role has been successfuly edited",
                ['%name%' => (string) $user]
            );
            return $this->redirectToRoute('app_backend_user_show', [
                'id' => $user->getId(),
                '_locale' => $request->getLocale()
            ]);
        }
        return $this->render('role.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'password' => null
        ]);
    }


    /**
     * @Route("/{_locale}/user-{id}&edit-password", name="app_backend_user_password_edit", methods={"GET","POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt","id":"\d+"})
     * @RouteAction(name="app_backend_user_password_edit", title="user.password.edit.action", targets={"admins"})
     */
    public function password(Request $request, User $user, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $user->setPassword($userPasswordEncoder->encodePassword($user, $user->getPassword()));
            $manager = $this->getManager();
            $manager->flush();
            $this->flashMessage(
                'success',
                "member name password has been successfully edited",
                ['%name%' => (string) $user]
            );
            return $this->redirectToRoute('app_backend_user_show', ['id' => $user->getId()]);
        }
        return $this->render('password.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/user-{id}&action-{action}", name="app_backend_user_lock", methods={"POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt","id":"\d+", "action":"(un)?lock"})
     * @RouteAction(name="app_backend_user_lock", title="user.lock.action", targets={"admins"})
     */
    public function lock(Request $request, User $user, string $action, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $request->request->get('_csrf_token');
        $token = new CsrfToken('user_' . $action, $token);
        if (!$csrfTokenManager->isTokenValid($token)) {
            $this->throwRedirectRequest(
                true,
                $this->urlsessionManager->getOldUrl(),
                $this->trans('invalid action detected'),
                true
            );
        }
        /** @var User $auth */
        $auth = $this->getUser();
        $this->throwRedirectRequest(
            $user->getRole()->getName() === BaseRoleProperties::ROLE_SUPERADMIN  &&
                $auth->getRole()->getName() !== BaseRoleProperties::ROLE_SUPERADMIN,
            $this->urlsessionManager->getOldUrl(),
            $this->trans(
                "an user with name role can not lock or unlock an user with %root% role",
                [
                    '%name%' => $this->trans($auth->getRole()->getTitle()),
                    '%root%' => $this->trans($user->getRole()->getTitle())
                ]
            ),
            true
        );
        $this->throwRedirectRequest(
            $user->getId() === $this->getUser()->getId(),
            $this->urlsessionManager->getOldUrl(),
            $this->trans('an super admin can not lock or unlock himself', [
                '%name%' => $this->trans($user->getRole()->getTitle())
            ]),
            true
        );

        $manager = $this->getManager();
        if ($action === 'lock') {
            if (!$user->getLocked()) {
                $user->setLocked(true);
                $user->setLockedAt(new DateTime());
                $user->setAdmin($this->getUser());
                $manager->flush();
                $this->flashMessage(
                    'success',
                    "member %name% has been successfully locked",
                    ['%name%' => (string) $user]
                );
            } else {
                $this->flashMessage(
                    'error',
                    "member %name% is already locked",
                    ['%name%' => (string) $user]
                );
            }
        } else {
            if ($user->getLocked()) {
                $user->setLocked(false);
                $user->setLockedAt(null);
                $user->setAdmin($this->getUser());
                $manager->flush();
                $this->flashMessage(
                    'success',
                    "member %name% has been successfully unlocked",
                    ['%name%' => (string) $user]
                );
            } else {
                $this->flashMessage(
                    'error',
                    "member %name% is not locked",
                    ['%name%' => (string) $user]
                );
            }
        }

        return $this->redirectToRoute('app_backend_locked_users', [
            //'id' => $user->getId(),
            '_locale' => $request->getLocale()
        ]);
    }
}
