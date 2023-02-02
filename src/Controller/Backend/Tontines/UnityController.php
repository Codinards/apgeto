<?php

namespace App\Controller\Backend\Tontines;

use App\Entity\Tontines\Unity;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Tontines\MultiWinners;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\MultiWinnersSelection;
use App\Form\Tontines\MultiWinnersSelectionType;
use App\Form\Tontines\MultiWinnersType;
use App\Form\Tontines\UnityDeleteType;
use App\Form\Tontines\UnityUpdateType;
use App\Form\Tontines\UnityWonType;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/backend/tontine/unity")
 * @Module(name="tontine", is_activated=false)
 */
class UnityController extends AdminBaseController
{
    use TontinesControllerTrait;
    protected $viewPath = 'tontines/unity/';


    /**
     * @Route("/{_locale}/{id}", name="app_backend_unity_show", methods={"GET"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_show", title="unity.see.action", targets={"admins"})
     */
    public function show(Unity $unity): Response
    {

        return $this->render('show.html.twig', [
            'unity' => $unity,
            'is_valid' => $unity->getTontine()->getIsCurrent() and !$unity->getIsStopped()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-won", name="app_backend_unity_won", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_won", title="unity.won.action", targets={"admins"})
     */
    public function benefit(Request $request, Unity $unity): Response
    {
        /** Control of tontine an unity action validity */
        $this->tontineIsValid($unity, $request);
        $this->isValid($unity, $request);
        // $unity->setBenefitAt(new DateTime());
        /** Managing unity benefit action */
        $unity->resolveBenefitAt();
        $form = $this->createForm(UnityWonType::class, $unity);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getTontineManager();
            $unity->update();
            $manager->flush();
            $this->flashMessage('success', 'unity.benefit.successful');
            return $this->redirectToRoute('app_backend_tontine_info', ['id' => $unity->getTontine()->getId()]);
        }

        return $this->render('benefit.html.twig', [
            'unity' => $unity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-muti-won-selection", name="app_backend_multi_won_selection", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(
     *  name="app_backend_multi_won_selection",
     *  title="Voir la page pour sélectionner plusieurs bénéficiaire d'une tontine",
     *  targets={"admins"}
     *)
     */
    public function multiBenefitSelection(Request $request, Tontine $tontine): Response
    {
        /** Control of tontine an unity action validity */
        $this->tontineIsValid($tontine, $request);

        $selection = new MultiWinnersSelection();
        $form = $this->createForm(MultiWinnersSelectionType::class, $selection, [
            "selected_data" => $this->collection($tontine->getNotWonUnities()->toArray())
                ->sortBy(fn (Unity $unity) => $unity->getTontineur()->getName())
                ->toArray()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            return $this->redirectToRoute("app_backend_multi_won", [
                "id" => $tontine->getId(),
                "selection" => implode(
                    "_",
                    array_map(fn (Unity $unity) => $unity->getId(), $selection->getWinners())
                )
            ]);
        }

        return $this->render('multi_benefit_selection.html.twig', [
            'tontine' => $tontine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-muti-won-{selection}", name="app_backend_multi_won", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+", "selection":"([0-9]+_)+[0-9]+"})
     * @RouteAction(
     *  name="app_backend_multi_won",
     *  title="Voir la page pour enregistrer plusieurs bénéficiaire d'une tontine",
     *  targets={"admins"}
     *)
     */
    public function multiBenefit(Request $request, Tontine $tontine, string $selection): Response
    {

        /** Control of tontine an unity action validity */
        $this->tontineIsValid($tontine, $request);

        $selection = explode("_", $selection);
        $selection = (new MultiWinners())
            ->setWinners($this->getUnityRepository()->findBy(["id" => $selection]));
        $form = $this->createForm(MultiWinnersType::class, $selection);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            foreach ($selection->getWinners() as $unity) {
                $this->isValid($unity, $request);
            }

            $manager = $this->getTontineManager();
            $adminId = $this->getUser()->getId();
            foreach ($selection->getWinners() as $unity) {
                $unity->update($adminId);
            }
            $manager->flush();
            $this->flashMessage('success', 'unity.benefit.successful');
            return $this->redirectToRoute('app_backend_tontine_info', ['id' => $tontine->getId()]);
        }

        return $this->render('multi_benefit.html.twig', [
            'tontine' => $tontine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-update", name="app_backend_unity_update", methods={"GET", "POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_update", title="unity.update.action", targets={"admins"})
     */
    public function update(Request $request, Unity $unity): Response
    {
        /** Control of tontine an unity action validity */
        $this->tontineIsValid($unity, $request);
        $this->isValid($unity, $request, false, true);

        /** Managing unity benefit action */
        $unity->resolveBenefitAt();
        $form = $this->createForm(UnityUpdateType::class, $unity);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $manager = $this->getTontineManager();
            $manager->flush();
            $this->flashMessage('success', 'unity.update.successful');
            return $this->redirectToRoute('app_backend_tontine_info', ['id' => $unity->getTontine()->getId()]);
        }

        return $this->render('update.html.twig', [
            'unity' => $unity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{_locale}/{id}-cancel-benefit", name="app_backend_unity_cancel_benefit", methods={"POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_cancel_benefit", title="unity.cancel.benefit.action", targets={"admins"})
     */
    public function camcelBenefit(Request $request, Unity $unity): Response
    {
        if ($this->isCsrfTokenValid('unity_cancel_benefit', $request->request->get('_csrf_token'))) {
            /** Control of tontine an unity action validity */
            $this->tontineIsValid($unity, $request);
            $this->isValid($unity, $request, false, true);
            $manager = $this->getTontineManager();
            $unity->cancelBenefit();
            $manager->flush();
            $this->flashMessage('success', 'operation.success');
            return $this->redirectToRoute('app_backend_tontine_info', ['id' => $unity->getTontine()->getId()]);
        }
        $this->flashMessage('error', 'operation.fail');
        return $this->redirectToRoute('app_backend_unity_show', ['id' => $unity->getId()]);
    }

    /**
     * @Route("/{_locale}/{id}-lock", name="app_backend_unity_lock", methods={"POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_lock", title="unity.lock.action", targets={"admins"})
     */
    public function lock(Unity $unity, Request $request): Response
    {
        /** Control of tontine an unity action validity */
        if ($this->isCsrfTokenValid('unity_lock', $request->request->get('_csrf_token'))) {
            $this->isValid($unity, $request);
            $unity->setIsStopped(true);
            $unity->getTontine()->decrementCotisation($unity->getAmount());
            if ($unity->getIsDemiNom()) {
                $unity->getTontine()->incrementDemiLockedCount();
            } else {
                $unity->getTontine()->incrementLockedCount();
            }
            $unity->getTontineurData()->incrementLockedCount();
            $this->getTontineManager()->flush();
            $this->flashMessage('success', 'unity.lock.success');
            return $this->redirectToRoute('app_backend_unity_show', ['id' => $unity->getId()]);
        }
        $this->flashMessage('success', 'unity.lock.unsuccess');
        return $this->redirectToRoute('app_backend_unity_show', ['id' => $unity->getId()]);
    }


    /**
     * @Route("/{_locale}/{id}-delete", name="app_backend_unity_delete", methods={"GET","POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_delete", title="Supprimer un nom de cotisation", targets={"admins"})
     */
    public function delete(Unity $unity, Request $request): Response
    {
        $this->isValid($unity, $request);
        $form = $this->createForm(UnityDeleteType::class, $unity);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            /******************************** */
            /******************************** */
            // ECRIRE LE CODE DE SUPPRESSION ICI
            $unity->delete($this->getManager());
            /******************************** */
            /******************************** */
            $this->getTontineManager()->flush();
            $this->flashMessage('success', 'operation.success');
            return $this->redirectToRoute('app_backend_tontine_show', ['id' => $unity->getTontine()->getId()]);
        }
        return $this->render('delete.html.twig', [
            "unity" => $unity,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-unlock", name="app_backend_unity_unlock", methods={"POST"},
     * requirements={"_locale":"en|fr|es|it|pt", "id":"\d+"})
     * @RouteAction(name="app_backend_unity_unlock", title="unity.unlock.action", targets={"admins"})
     */
    public function unlock(Request $request, Unity $unity): Response
    {
        /** Control of tontine an unity action validity */
        if ($this->isCsrfTokenValid('unity_unlock', $request->request->get('_csrf_token'))) {
            $this->isValid($unity, $request, true);
            $unity->setIsStopped(false);
            $unity->getTontine()->incrementCotisation($unity->getAmount());
            if ($unity->getIsDemiNom()) {
                $unity->getTontine()->decrementDemiLockedCount();
            } else {
                $unity->getTontine()->decrementLockedCount();
            }
            $unity->getTontineurData()->decrementLockedCount();
            $this->getTontineManager()->flush();
            $this->flashMessage('success', 'unity.unlock.success');
            return $this->redirectToRoute('app_backend_unity_show', ['id' => $unity->getId()]);
        }
        $this->errorFlash('unity.unlock.unsuccess');
        return $this->redirectToRoute('app_backend_unity_show', ['id' => $unity->getId()]);
    }

    /**
     * @param Unity $unity
     * @param Request $request
     * @param boolean $validate_stopped, valid if unity is already stooped or not
     * @param boolean $permit_no_won, valid if unity is already won or not
     * @return void
     */
    private function isValid(Unity $unity, Request $request, bool $validate_stopped = false, $permit_no_won = false)
    {
        $this->tontineIsValid($unity, $request);
        $wonCond = $permit_no_won ? !$unity->getIsWon() : $unity->getIsWon();
        $WonMessage = $permit_no_won ? 'unity.already.won' : 'unity.is.not.won';
        $this->unityIsValid($wonCond, $WonMessage, ['_locale' => $request->getLocale(), 'id' => $unity->getId()]);
        if (!$validate_stopped) {
            $this->unityIsValid($unity->getIsStopped(), 'unity.already.locked', ['_locale' => $request->getLocale(), 'id' => $unity->getId()]);
        } else {
            $this->unityIsValid(!$unity->getIsStopped(), 'unity.not.locked', ['_locale' => $request->getLocale(), 'id' => $unity->getId()]);
        }
    }


    public function unityIsValid(bool $condition, string $message, array $params = [], string $route = 'app_backend_unity_show'): void
    {
        $this->throwRedirectRequest(
            $condition,
            $this->generateUrl($route, $params),
            $this->trans($message),
            true
        );
    }
}
