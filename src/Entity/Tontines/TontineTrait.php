<?php

namespace App\Entity\Tontines;

use App\Entity\Main\Users\User;
use App\Tools\Entity\StaticEntityManager;
use Illuminate\Support\Collection;

trait TontineTrait
{
    public function getAdmin(): ?User
    {
        return $this->admin = $this->admin ?? StaticEntityManager::getUserRepository()->find($this->getAdminId());
    }

    public function collection(array $data = []): Collection
    {
        return new Collection($data);
    }
}
