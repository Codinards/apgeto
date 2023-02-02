<?php

namespace App\Subscriber\Assistance;

use App\Events\Assistance\DeleteContributorFundEvent;
use App\Events\Assistance\UpdateContributorFundEvent;
use App\Subscriber\AbstractEvent;
use Doctrine\ORM\EntityManagerInterface;

class AssistanceSubscriber extends AbstractEvent
{
    private bool $isDeleting = false;

    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdateContributorFundEvent::class => "onUpdatingFund",
            DeleteContributorFundEvent::class => "onDeletingFund",
        ];
    }

    public function onUpdatingFund(UpdateContributorFundEvent $event)
    {
        $contributor = $event->getContributor();
        $contributor->getAssistance()
            ->incrementTotalContribution($event->getEntity()->getCashOutFlows())
            ->decrementTotalContribution($contributor->getAmount());
        $contributor->setAmount($event->getEntity()->getCashOutFlows());
    }
    public function onDeletingFund(DeleteContributorFundEvent $event)
    {
        $contributor = $event->getContributor();
        $contributor->getAssistance()->decrementTotalContribution($contributor->getAmount());
        $this->manager->remove($contributor);
    }
}
