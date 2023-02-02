<?php

namespace App\Controller\Frontend;

use App\Controller\Backend\Accounts\DebtController;
use App\Controller\Backend\Accounts\FundController;
use App\Controller\Backend\Users\UserController as UsersUserController;
use App\Controller\BaseController;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Users\User;
use App\Form\Main\Users\UserImageType;
use App\Tools\Entity\TotalResolver;
use App\Tools\Files\PdfProvider;
use App\Tools\Request\UrlSessionManager;
use App\Tools\Twig\LocalLanguages;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/users/user")
 * @Module(name="base", is_activated=true)
 */
class UserController extends BaseController
{

    /**
     * @Route("/{_locale}/{id}-image_edit", name="app_frontend_user_image_edit", methods={"GET", "POST"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "year":"\d{4}", "id": "\d+"})
     * @RouteAction(name="app_frontend_user_image_edit", title="user.image.edit.action", targets={"all"})
     */
    public function image(Request $request, UsersUserController $userController, User $user): Response
    {
        $form = $this->createForm(UserImageType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->resolveUpdatedAt();
            $this->getManager()->flush();
            return $this->redirectToRoute('app_frontend_user_profil', ['_locale' => $request->getLocale(), 'id' => $user->getId()]);
        }
        return $this->render($userController->getViewPath() . 'image_update.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-profil", name="app_frontend_user_profil", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_frontend_user_profil", title="user.profile.see.action", targets={"all"})
     */
    public function profile(UsersUserController $userController, User $user): Response
    {
        return $userController->show($user);
    }

    /**
     * @Route("/{_locale}/{id}/{year}", name="app_frontend_user_index", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "year":"\d{4}", "id":"\d+"})
     * @RouteAction(name="app_frontend_user_index", title="user.account.index.action", targets={"all"})
     */
    public function show(FundController $fundController, Request $request, User $user, ?int $year = null): Response
    {
        return $fundController->show($request, $user->getAccount(), true, $year);
    }


    /**
     * @Route("/{_locale}/{id}-debt-show", name="app_frontend_user_debt_index", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_frontend_user_debt_index", title="user.account.debt.index.action", targets={"all"})
     */
    public function debt(DebtController $debtController, Request $request, User $user): Response
    {

        return $debtController->show($request, $user->getAccount(), true);
    }

    /**
     * @Route("/{_locale}/{id}-debt-show-{debt}", name="app_frontend_user_show_previous", methods={"GET"}, 
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+", "debt":"\d+"})
     * @RouteAction(name="app_frontend_user_show_previous", title="user.account.debt.previous.action", targets={"all"})
     */
    public function showPrevious(DebtController $debtController, Request $request, User $user, Debt $debt): Response
    {
        return $debtController->showPrevious($request, $user->getAccount(), $debt, true);
    }
}
