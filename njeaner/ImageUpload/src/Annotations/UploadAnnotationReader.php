<?php

declare(strict_types=1);

namespace Njeaner\ImageUpload\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;

class UploadAnnotationReader
{
    protected $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function isUploadable($entity, ReflectionClass $refletion = null)
    {
        $refletion = $refletion ?? new ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($refletion, Uploadable::class) != null;
    }

    /**
     * Undocumented function
     *
     * @param mixed $entity
     * @return UploadableField[]
     */
    public function getUploadableFields($entity): array
    {
        $refletion = new ReflectionClass(get_class($entity));
        if (!$this->isUploadable($entity, $refletion)) return [];
        $properties = [];
        foreach ($refletion->getProperties() as $property) {
            if (($annotation = $this->reader->getPropertyAnnotation($property, UploadableField::class)) != null) {
                $properties[$property->getName()] = $annotation;
            }
        }

        return $properties;
    }
}
