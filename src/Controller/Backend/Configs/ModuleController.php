<?php

namespace App\Controller\Backend\Configs;

use Symfony\Component\HttpFoundation\Request;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Backend\AdminBaseController;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;

/**
 * @Route("/backend/configs/module")
 * @Module(name="base", is_activated=true)
 */
class ModuleController extends AdminBaseController
{
}
