<?php

namespace App\Controller\Backend;

use App\Controller\BaseController as ControllerBaseController;
use App\Entity\Main\Users\User;
use Doctrine\Persistence\ObjectManager;
use App\Repository\Main\Users\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminBaseController extends ControllerBaseController
{
    protected $viewPath = 'backend';

    /*protected $menu_routes = [
        'users' => [
            'members' => 'app_backend_user_index',
            'accounts' => 'app_backend_account_index',
            'authorizations' => 'app_backend_user_action_index',
        ],
        'tontines' => [
            'types' => 'app_backend_tontinetype_index',
            'tontines' => 'app_backend_tontine_index'
        ],
        'administration' => [
            'dashboard' => "app_backend_admin_index"
        ]
    ];*/

    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $view = $this->viewPath . $view;
        $parameters = array_merge($parameters, [
            'viewPath' => $this->viewPath,
            //'menu_routes' => $this->menu_routes,
        ]);
        //$parameters['viewPath'] = $this->viewPath;
        return parent::render($view, $parameters, $response);
    }

    /**
     * Get the value of viewPath
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }
}
