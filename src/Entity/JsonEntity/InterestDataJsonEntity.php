<?php

namespace Entity\JsonEntity;

use App\Entity\JsonEntity\JsonEntityInterface;
use App\Tools\DirectoryResolver;
use App\Tools\JsonFileManager;

class InterestDataJsonEntity implements JsonEntityInterface
{
    private string $filename = 'interest_data.json';

    public array $data = [];

    public function __construct()
    {
        $filename = DirectoryResolver::getDirectory('var/configs') . $this->filename;
        $this->fileManager = new JsonFileManager($filename);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function toArray(): array
    {
        return [];
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function save()
    {
        $this->fileManager->encode([$this->data]);
    }
}
