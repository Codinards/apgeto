<?php

namespace App\Tools;

use Exception;

class JsonFileManager
{
    protected $filename;

    public function __construct(string $filename)
    {
        if (stripos($filename, '.json') === false) {
            throw new JsonFileManagerException("\"{$filename}\" is not a json file");
        }
        $this->filename = $filename;
        if (!file_exists($filename)) {
            $this->filePutContent('{}');
        }
    }

    public function encode(array $data): self
    {
        $this->filePutContent(json_encode($data));
        return $this;
    }

    public function decode(): array
    {
        try {
            return json_decode(file_get_contents($this->filename), true);
        } catch (Exception $e) {
            return [];
        }
    }

    public function merge($info, $primaryKey = null): self
    {
        $data = $this->decode();

        $count = count($data) + 1;

        $info['id'] = $info['id'] ?? $count;
        $primaryKey = $primaryKey ?? $count;
        $data[$primaryKey] = $info;
        return $this->encode($data);
    }

    public function update($info, $primaryKey): self
    {
        $data = $this->decode();
        if (isset($data[$primaryKey])) {
            throw new JsonFileManagerException("Entity with key $primaryKey does not exists");
        }
        $data[$primaryKey] = $info;
        return $this->encode($data);
    }

    protected function filePutContent(string $content)
    {
        try {
            return file_put_contents($this->filename, $content);
        } catch (Exception $e) {
            throw new JsonFileManagerException($e->getMessage());
        }
    }

    /**
     * Get the value of filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @return  self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    public function deleteFile()
    {
        unlink($this->filename);
        return $this;
    }
}
