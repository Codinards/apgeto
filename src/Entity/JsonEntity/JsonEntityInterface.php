<?php

namespace App\Entity\JsonEntity;

interface JsonEntityInterface
{
    public function getFilename(): string;

    public function toArray(): array;

    public function getPrimaryKey();
}
