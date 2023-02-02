<?php

namespace App\Tools\Entity;

class EntityHydrator
{
    public static function hydrate($entity, array $data)
    {
        $entity = is_object($entity) ? $entity : new $entity();
        foreach ($data as $property => $value) {
            $method = 'set' . ucfirst($property);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }
        return $entity;
    }
}
