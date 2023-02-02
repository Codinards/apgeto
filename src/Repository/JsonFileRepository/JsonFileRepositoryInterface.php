<?php

namespace App\Repository\JsonFileRepository;

use App\Entity\JsonEntity\JsonEntityInterface;

interface JsonFileRepositoryInterface
{
    public function find($key): ?JsonEntityInterface;

    public function findBy(array $condition): array;

    public function findOneBy(array $condition): ?JsonEntityInterface;

    public function findFirst(): ?JsonEntityInterface;

    public function findLast(): ?JsonEntityInterface;

    public function findFirstBy(array $condition): ?JsonEntityInterface;

    public function findLastBy(array $condition): ?JsonEntityInterface;

    public function findAll(): array;

    public function save(JsonEntityInterface $entity): self;

    public function update(JsonEntityInterface $entity): self;

    public function delete(JsonEntityInterface $entity): self;

    public function deleteByKey($key): self;
}
