<?php

namespace App\Controller\Backend\Operations;

use App\Controller\Backend\Accounts\HandleAccountFlow;
use App\Controller\Backend\AdminBaseController;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Entity\Utils\CashOutFlows;
use App\Form\Operations\OperationFromAccountType;
use App\Form\Operations\OperationType;
use App\Entity\Utils\OperationFromAccount;
use App\Events\Fund\CreateFundEvent;
use App\Events\Fund\DeleteFundEvent;
use App\Events\Fund\UpdateFundEvent;
use App\Events\Operation\DeleteOperationEvent;
use App\Events\Operation\UpdateOperationEvent;
use App\Form\Operations\DeleteOperationType;
use App\Form\Operations\UpdateOperationType;
use App\Tools\AppConstants;
use App\Tools\Entity\AccountManager;
use Njeaner\UserRoleBundle\Annotations\Module;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/operations")
 * @Module(name="base", is_activated=true)
 */
class OperationController extends AdminBaseController
{

    use HandleAccountFlow;

    protected $viewPath = 'operations/operation/';

    /**
     * @Route("/{_locale}", name="app_backend_operation_index", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_index", title="operation.index.action", targets={"admins"}, is_index=true)
     */
    public function index(): Response
    {
        return $this->render('/index.html.twig', [
            'operations' => $this->collection($this->getOperationRepository()->findAll())
                ->sortBy(fn (Operation $operation) => $operation->getId())
                ->sortBy(fn (Operation $operation) => $operation->getCreatedAt())
        ]);
    }



    /**
     * @Route("/{_locale}/new-{type}-{action}", name="app_backend_operation_new", methods={"GET", "POST"}, requirements={"type":"\d+", "action":"in|out", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_new", title="operation.add.action", targets={"admins"})
     */
    public function create(Request $request, Type $type, string $action = 'in'): Response
    {
        $operation = new Operation;
        $operation->setType($type)->setAdmin($this->getUser())->setBalance($type->getBalance());
        $form = $this->createForm(OperationType::class, $operation, ['action_type' => $action]);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $this->getManager()->persist($operation);
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_show', ['id' => $type->getId()]);
        }
        return $this->render('/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'title' => $this->trans(($action === 'in') ? 'inflow operation' : 'outflow operation')
        ]);
    }

    /**
     * @Route("{_locale}/new-{type}-{action}-user", name="app_backend_operation_new_from_user", methods={"GET", "POST"}, requirements={"type":"\d+", "action":"in|out", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_new_from_user", title="operation.from_user.add.action", targets={"admins"})
     */
    public function createFromUser(Request $request, AccountManager $accountManager, Type $type, string $action = 'in'): Response
    {
        $operation = new OperationFromAccount;
        $operation->setType($type)->setAdmin($this->getUser())->setBalance($type->getBalance());
        $form = $this->createForm(OperationFromAccountType::class, $operation, ['action_type' => $action]);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $users = $operation->getUsers();
            /** @var Fund[] $funds */
            $funds = [];
            foreach ($users as $user) {
                $balance = $user->getAccount()->getCashBalances() - $operation->getInflows();
                if ($balance < 0 && !AppConstants::$FUND_CAN_BE_NEGATIVE) {
                    $this->errorFlash('user fund balance is less than operation inflow amount', [
                        '%user%' => $user->getName(),
                        '%balance%' => $user->getAccount()->getCashBalances(),
                        '%inflow%' => $operation->getInflows()
                    ]);
                    return $this->redirectToRoute('app_backend_operation_new_from_use', ['type' => $type->getId(), 'action' => $action]);
                }

                $fund = $accountManager->ManageCashOutFlows((new CashOutFlows())
                    ->setAccount($user->getAccount())
                    ->setUser($user)
                    ->setAdmin($this->getUser())
                    ->setWording($operation->getWording())
                    ->setCreatedAt($operation->getCreatedAt())
                    ->setCashOutFlows($operation->getInflows())
                    ->setObservations($type->getName()));
                $funds[] = $fund;
            }

            foreach ($funds as $fund) {
                $dbOperation = (new Operation)
                    ->setBalance($type->getBalance())
                    ->setType($type)
                    ->setWording($operation->getWording())
                    ->setInflows($operation->getInflows())
                    ->setCreatedAt($operation->getCreatedAt())
                    ->setObservation(
                        $operation->getObservation() .  ' ---member: ' . $fund->getUser()->getName() . ' :___: ' . $fund->getUser()->getId()
                    )
                    ->setAdmin($operation->getAdmin());
                $this->getManager()->persist($dbOperation);
                $fund->setOperation($dbOperation);
                $this->getManager()->persist($fund);
                $this->dispatcher->dispatch(new CreateFundEvent($fund));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_show', ['id' => $type->getId()]);
        }

        return $this->render('/new.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'title' => $this->trans(($action === 'in') ? 'inflow operation' : 'outflow operation')
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-update", name="app_backend_operation_update", methods={"GET", "POST"}, requirements={"id":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_update", title="operation.update.action", targets={"admins"})
     */
    public function update(Request $request, Operation $operation): Response
    {
        $cloned = clone $operation;
        $form = $this->createForm(UpdateOperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            /** @var UpdateOperationEvent */
            $event = $this->dispatcher
                ->dispatch(new UpdateOperationEvent($operation, $cloned));
            if ($event->isLinked()) {
                $fund = $operation->getFund();
                $previous =  clone $fund;

                $fund->setPreviousBalances($previous->getCashBalances())
                    ->setCashBalances($previous->getCashBalances() - $previous->getCashOutFlows())
                    ->setPreviousTotalInflows($previous->getPreviousTotalInflows())
                    ->setPreviousTotalOutflows($previous->getPreviousTotalOutflows())
                    ->setCashOutFlows($operation->getInflows())
                    ->setCashInFlows($operation->getOutflows());
                $event = $this->dispatcher
                    ->dispatch(new UpdateFundEvent($fund, $previous));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_show', [
                'id' => $operation->getType()->getId()
            ]);
        }
        return $this->render('edit.html.twig', [
            "form" => $form->createView(),
            "operation" => $operation,
            "title" => "Edition " . $operation->getWording()
        ]);
    }

    /**
     * @Route("/{_locale}/{id}-delete", name="app_backend_operation_delete", methods={"GET", "POST"}, requirements={"id":"\d+", "_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="app_backend_operation_delete", title="operation.delete.action", targets={"admins"})
     */
    public function delete(Request $request, Operation $operation): Response
    {
        $form = $this->createForm(DeleteOperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            /** @var DeleteOperationEvent */
            $event = $this->dispatcher->dispatch(new DeleteOperationEvent($operation));
            if ($event->isLinked()) {
                $event = $this->dispatcher
                    ->dispatch(new DeleteFundEvent($operation->getFund()));
            }
            $this->getManager()->flush();
            $this->successFlash('operation.success');
            return $this->redirectToRoute('app_backend_operation_type_show', [
                'id' => $operation->getType()->getId()
            ]);
        }

        return $this->render('delete.html.twig', [
            "form" => $form->createView(),
            "operation" => $operation,
            "title" => "Suppression " . $operation->getWording()
        ]);
    }
}
