<?php

namespace App\Repository\JsonFileRepository;

use App\Entity\JsonEntity\RoleJsonEntity;

class RoleJsonEntityRepository extends BaseJsonFileRepository
{
    const CLASS_NAME = RoleJsonEntity::class;

    public static function get(int $id): ?RoleJsonEntity
    {
        return self::getInstance(self::CLASS_NAME)->find($id);
    }

    public static function store(RoleJsonEntity $entity)
    {
        return self::getInstance(self::CLASS_NAME)->save($entity);
    }
}
