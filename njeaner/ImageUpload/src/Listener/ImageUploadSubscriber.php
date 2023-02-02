<?php

declare(strict_types=1);

namespace Njeaner\ImageUpload\Listener;

use App\Tools\Request\UploadedFile;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\PreUpdateEventArgs;
use Njeaner\ImageUpload\Annotations\UploadAnnotationReader;
use Njeaner\ImageUpload\Handler\UploadHandler;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageUploadSubscriber implements EventSubscriber
{
    protected $reader;

    protected $handler;

    protected $requestStack;

    public function __construct(
        UploadAnnotationReader $reader,
        UploadHandler $handler,
        RequestStack $requestStack
    ) {
        $this->reader = $reader;
        $this->handler = $handler;
        $this->requestStack = $requestStack;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postLoad,
            Events::preUpdate,
            Events::preRemove
        ];
    }

    public function prePersist(EventArgs $event)
    {

        $this->preEvents($event);
    }

    public function preUpdate(EventArgs $event)
    {
        $this->preEvents($event, true);
    }

    private function preEvents($event, bool $update = false)
    {

        /** @var LifecycleEventArgs|PreUpdateEventArgs $event */
        $entity = $event->getObject();
        foreach ($this->reader->getUploadableFields($entity) as $property => $annotation) {
            $file = PropertyAccess::createPropertyAccessor()->getValue($entity, $property);
            if ($file instanceof UploadedFile) {
                if ($update) {
                    $this->handler->removeFileOld($entity, $annotation);
                }
                $this->handler->uploadFile($entity, $annotation, $file);
            }
        }
    }


    public function postLoad(EventArgs $event)
    {
        /** @var LifecycleEventArgs $event */

        $entity = $event->getObject();
        if ($this->reader->isUploadable($entity)) {
            foreach ($this->reader->getUploadableFields($entity) as $property => $annotation) {
                $this->handler->loadFileFromDatabase($entity, $property, $annotation);
            }
        }
    }

    public function preRemove(EventArgs $event)
    {
        /** @var LifecycleEventArgs $event */
        $entity = $event->getObject();
        if ($this->reader->isUploadable($entity)) {
            foreach ($this->reader->getUploadableFields($entity) as $annotation) {
                $this->handler->removeFileOld($entity, $annotation);
            }
        }
    }
}
