<?php

namespace App\Entity\JsonEntity;

use App\Entity\Main\Users\User;
use App\Tools\DirectoryResolver;
use App\Tools\JsonFileManager;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Collection;

class ActivitiesTracesJsonEntity implements JsonEntityInterface
{
    private string $id;

    private int $admin;

    protected $title;

    protected $description;

    public function __construct(string $title, User|int $admin, ?string $description = null)
    {
        $this->id = (new DateTime())->format('H:i:s');
        $this->admin = is_int($admin) ? $admin : $admin->getId();
        $this->title = $title;
        $this->description = $description;
    }

    public function getFilename(): string
    {
        return '';
    }

    public function toArray(): array
    {
        return [];
    }

    public function getPrimaryKey()
    {
        return $this->id;
    }

    /**
     * Get the value of admin
     */
    public function getAdmin(): int
    {
        return $this->admin;
    }

    /**
     * Get the value of title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
