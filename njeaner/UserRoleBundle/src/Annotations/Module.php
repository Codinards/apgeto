<?php

namespace Njeaner\UserRoleBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Module
{
    protected ?string $name = null;

    protected bool $isActivated = false;

    public function __construct(array $values)
    {
        $this->name = $this->isset($values, 'name');
        $this->isActivated = $this->has($values, 'is_activated', false);
    }

    private function isset(array $values, string $name)
    {
        if ($this->has($values, $name, null) === null) {
            throw new RouteActionException("The value $name is missed in " . __CLASS__ . ' annotation constructor arguments');
        }
        return $values[$name];
    }

    private function has(array $values, string $name, $default)
    {
        return $values[$name] ?? $default;;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of isActivated
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }
}
