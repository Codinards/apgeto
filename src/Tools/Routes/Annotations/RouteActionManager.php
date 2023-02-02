<?php

namespace App\Tools\Routes\Annotations;

use App\Entity\Main\Users\User;
use App\Entity\Main\Users\UserAction;
use App\Tools\Entity\GlobalData;
use Doctrine\ORM\EntityManagerInterface;
use Njeaner\UserRoleBundle\Annotations\RouteAction as NjeanerRouteAction;
use Njeaner\UserRoleBundle\Annotations\RouteActionAnnotationReader;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;

class RouteActionManager
{

    protected $security;

    protected $actionRoutes = [];

    protected $manager;

    protected $kernel;

    protected $routeAnnotations;

    protected $globalData;

    protected ?User $user = null;

    public function __construct(Security $security, EntityManagerInterface $manager, ContainerInterface $container, RouteActionAnnotationReader $routeAnnotations, GlobalData $globalData)
    {
        $this->security = $security;
        $this->manager = $container->get('doctrine')->getManager();
        $this->kernel = $container->get('kernel');
        $this->routeAnnotations = $routeAnnotations;
        $this->globalData = $globalData;
    }

    public function getCurrentRouteName(Request $request)
    {
        return $request->attributes->get('_route');
    }

    public function isAuthorize(?string $routeName)
    {

        // dd($routeName);
        // if ($routeName === 'first_admin_registration') {
        //     return 1;
        // }
        /** @var UserAction $action */
        $action = $this->globalData->get($routeName, 'actions');
        if ($action) {
            if (!$action->getIsActivated()) {
                return 0;
            }
            if ($action->getHasAuth()) {

                return ((bool) $this->getUser()?->hasRole($action)) ? 1 : 0;
                /** @var User $user */
                /*$user = $this->security->getUser();
                if (!$user) {
                    return 0;
                }
                $role = $user->getRole();

                return $role->hasUserAction($action) ? 1 : 0;*/
            }
            return 1;
        }
        return 2;
    }

    public function actionIsAutorize(Request $request)
    {
        if (in_array($this->kernel->getEnvironment(), ['dev', 'test'])) {
            $this->routeAnnotations->updateRouteAction(NjeanerRouteAction::class);
        }

        return $this->isAuthorize($this->getCurrentRouteName($request));
    }

    private function getUser(): ?User
    {
        return $this->user = $this->user ?? $this->security->getUser();
    }
}
