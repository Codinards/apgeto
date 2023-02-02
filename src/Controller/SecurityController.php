<?php

namespace App\Controller;

use App\Tools\AppConstants;
use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Module(name="base", is_activated=true)
 */
class SecurityController extends BaseController
{
    /**
     * @Route("/{_locale}/login", name="app_login", methods={"GET", "POST"}, requirements={"_locale":"en|fr|es|it|pt"},
     * options={"action_name":"user.home.login", "updatable":"1"})
     * @RouteAction(name="app_login", title="user.loggedin.action", targets={"all"}, is_updatable=false, has_auth=false)
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        $action = $request->getPathInfo();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($this->getUser()) {
            return $this->throwRedirectRequestToHome(true, $this->trans('you are already connected as', [
                '%name%' => $this->getUser()->getName()
            ]));
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'action' => $action]);
    }

    /**
     * @Route("/login", name="login", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     */
    public function index(): Response
    {
        return $this->redirectToRoute('app_login', ['_locale' => AppConstants::$DEFAULT_LANGUAGE_KEY]);
    }

    /**
     * @Route("/logout", name="logout")
     * @RouteAction(name="app_logout", title="user.loggedout.action", targets={"all"}, is_updatable=false)
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
