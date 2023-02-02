<?php

namespace App\Subscriber\Operation;

use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Events\Operation\DeleteOperationEvent;
use App\Events\Operation\DeleteOperationMemberFundEvent;
use App\Events\Operation\UpdateOperationEvent;
use App\Events\Operation\UpdateOperationMemberFundEvent;
use App\Repository\Main\Operations\OperationRepository;
use App\Subscriber\AbstractEvent;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OperationSubscriber extends AbstractEvent implements EventSubscriberInterface
{

    public function __construct(
        private OperationRepository $operationRepository,
        private EntityManagerInterface $manager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdateOperationMemberFundEvent::class => "onUpdatingFund",
            DeleteOperationMemberFundEvent::class => "onDeletingFund",
            UpdateOperationEvent::class => "onUpdating",
            DeleteOperationEvent::class => "onDeleting"
        ];
    }

    public function onUpdating(UpdateOperationEvent $event)
    {
        $operation = $event->getEntity();
        if ($operation->isInflow()) {
            $operation->getType()
                ->setOutflow($event->getPrevious()->getInflows())
                ->setInflow($operation->getInflows());
        } else {
            $operation->getType()
                ->setOutflow($operation->getOutflows())
                ->setInflow($event->getPrevious()->getOutflows());
        }
        $this->operationsActualization($operation);
    }

    public function onDeleting(DeleteOperationEvent $event)
    {
        $operation = $this->operationsActualization($event->getEntity(), false);
        $operation->getType()
            ->setOutflow($operation->getInflows());
        if ($operation->getFund()) {
        }
        $this->manager->remove($operation);
    }

    public function onUpdatingFund(UpdateOperationMemberFundEvent $event)
    {
        $fund = $event->getEntity();
        $operation = $event->getOperation();
        $operation->getType()
            ->setOutflow($operation->getInflows())
            ->setInflow($fund->getCashOutFlows());
        $operation->setInflows($fund->getCashOutFlows());
        $this->operationsActualization($operation);
    }

    public function onDeletingFund(DeleteOperationMemberFundEvent $event)
    {
        $operation = $event->getOperation();
        $operation = $this->operationsActualization($operation, false);
        $operation->getType()
            ->setOutflow($operation->getInflows());
        $this->manager->remove($operation);
        // $operation = ;
        // $operations = $this->operationRepository->findByConditions(
        //     [
        //         'type' => $operation->getType(),
        //         "id" => [$operation->getId(), "<>"]
        //     ]
        // );
        // $operations = $this->collection($operations)
        //     ->sortBy(fn (Operation $operation) => $operation->getId())
        //     ->sortBy(fn (Operation $operation) => $operation->getCreatedAt());
        // $balance = 0;
        // /** @var Operation[] $operations */
        // foreach ($operations as $after) {
        //     $after->setBalance((int)$balance)
        //         ->setInflows((int)$after->getInflows())
        //         ->setOutflows((int)$after->getOutflows());
        //     $balance += $after->getInflows() - $after->getOutflows();
        // }
        // $operation->getType()
        //     ->setOutflow($operation->getInflows());
        // $this->manager->remove($operation);
    }

    private function operationsActualization(Operation $operation, bool $isUpdatingEvent = true): Operation
    {
        $operations = $this->operationRepository->findByConditions(
            [
                'type' => $operation->getType(),
                "id" => [$operation->getId(), "<>"]
            ]
        );
        if ($isUpdatingEvent) {
            array_unshift($operations, $operation);
        }
        $operations = $this->collection($operations)
            ->sortBy(fn (Operation $operation) => $operation->getId())
            ->sortBy(fn (Operation $operation) => $operation->getCreatedAt());
        $balance = 0;
        /** @var Operation[] $operations */
        foreach ($operations as $after) {
            $after->setBalance((int)$balance)
                ->setInflows((int)$after->getInflows())
                ->setOutflows((int)$after->getOutflows());
            $balance += $after->getInflows() - $after->getOutflows();
        }

        return $operation;
    }
}
