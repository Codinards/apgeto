<?php

namespace App\Repository\JsonFileRepository;

use App\Entity\JsonEntity\JsonEntityInterface;
use App\Tools\DirectoryResolver;
use App\Tools\DirectoryResolverInterface;
use App\Tools\Entity\EntityHydrator;
use App\Tools\JsonFileManager;

class BaseJsonFileRepository implements JsonFileRepositoryInterface
{
    //protected $jsonFilename;

    protected $JsonEntity;

    protected $fileData = [];

    /**
     * @var JsonFileManger
     */
    protected $jsonFileManager;

    private static $INSTANCE;

    public function __construct($JsonEntity, $jsonFilename)
    {
        $this->JsonEntity = $JsonEntity;
        $filename = (new $JsonEntity)->getFilename();
        $jsonFilename = $jsonFilename ?? DirectoryResolver::getDirectory('var/entities') . $filename;
        $this->jsonFileManager = new JsonFileManager($jsonFilename);
        $this->fileData = $this->jsonFileManager->decode();
        self::$INSTANCE = $this;
    }

    public function find($key): ?JsonEntityInterface
    {
        foreach ($this->fileData as $k => $data) {
            if ($k == $key) {
                return $this->hydrate($data);
            }
        }
        return null;
    }

    public function findBy(array $condition): array
    {
    }

    public function findOneBy(array $condition): ?JsonEntityInterface
    {
    }

    public function findFirst(): ?JsonEntityInterface
    {
    }

    public function findLast(): ?JsonEntityInterface
    {
    }

    public function findFirstBy(array $condition): ?JsonEntityInterface
    {
    }

    public function findLastBy(array $condition): ?JsonEntityInterface
    {
    }

    public function findAll(): array
    {
    }

    public function save(JsonEntityInterface $entity): self
    {
        $data = $entity->toArray();
        $this->jsonFileManager->merge($data, $data[$entity->getPrimaryKey()]);
        $this->fileData = $this->jsonFileManager->decode();
        return $this;
    }

    public function update(JsonEntityInterface $entity): self
    {
        $data = $entity->toArray();
        $this->jsonFileManager->update($data, $data[$entity->getPrimaryKey()]);
        $this->fileData = $this->jsonFileManager->decode();
        return $this;
    }

    public function delete(JsonEntityInterface $entity): self
    {
    }

    public function deleteByKey($key): self
    {
    }

    private function hydrate(array $data)
    {
        return EntityHydrator::hydrate($this->JsonEntity, $data);
    }

    /**
     * Get the value of JsonEntity
     */
    public function getJsonEntity()
    {
        return $this->JsonEntity;
    }

    /**
     * Get the value of fileData
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    public function deleteJsonFile()
    {
        $this->jsonFileManager->deleteFile();
        return $this;
    }

    public static function getInstance($JsonEntity): self
    {
        if (is_null(self::$INSTANCE)) {
            $className = get_called_class();
            self::$INSTANCE = new $className($JsonEntity);
        }
        return self::$INSTANCE;
    }
}
