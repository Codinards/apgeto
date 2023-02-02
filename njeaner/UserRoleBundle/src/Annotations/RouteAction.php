<?php

namespace Njeaner\UserRoleBundle\Annotations;

use App\Entity\Main\Users\UserAction;
use App\Tools\Entity\ModuleList;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 */
class RouteAction
{
    protected ?string $name = null;

    protected ?string $title = null;

    protected array $targets = [];

    protected bool $isUpdatable = true;

    protected ?string $module = null;

    protected bool $hasAuth = true;

    protected bool $isIndex = false;

    protected bool $isUpdated = false;

    protected bool $updatedRole = false;

    public function __construct(array $values)
    {
        $this->name = $this->isset($values, 'name');
        $this->title = $this->isset($values, 'title');
        $this->targets = $this->isset($values, 'targets');
        $this->module = $this->has($values, 'module', ModuleList::BASE_MODULE);
        $this->isUpdatable = $this->has($values, 'is_updatable', true);
        $this->hasAuth = $this->has($values, 'has_auth', true);
        $this->isIndex = $this->has($values, 'is_index', false);
        $this->isUpdated = $this->has($values, 'is_updated', false);
        $this->updatedRole = $this->has($values, 'updated_role', false);
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
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the value of targets
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Get the value of isUpdatable
     */
    public function getIsUpdatable()
    {
        return $this->isUpdatable;
    }

    /**
     * Get the value of module
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Get the value of hasAuth
     */
    public function getHasAuth()
    {
        return $this->hasAuth;
    }

    /**
     * Get the value of isIndex
     */
    public function getIsIndex()
    {
        return $this->isIndex;
    }

    /**
     * Get the value of isUpdated
     */
    public function getIsUpdated()
    {
        return $this->isUpdated;
    }

    public function updateAction(UserAction $action): UserAction
    {
        if ($this->name !== $action->getName()) $action->setName($this->name);
        if ($this->title !== $action->getTitle()) $action->setTitle($this->title);
        if ($this->isUpdatable !== $action->getIsUpdatable()) $action->setIsUpdatable($this->isUpdatable);
        if ($this->hasAuth !== $action->getHasAuth()) $action->setHasAuth($this->hasAuth);
        if ($this->isIndex !== $action->getIsIndex()) $action->setIsIndex($this->isIndex);
        return $action;
    }

    /**
     * Get the value of updatedRole
     */
    public function getUpdatedRole()
    {
        return $this->updatedRole;
    }
}
