<?php

namespace App\Entity;

use App\Entity\JsonEntity\FunctionalitiesJsonEntity;
use Exception;

class DevEntity
{
    public $status;

    public $key;

    public $subKey;

    public $index;
    private $data;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->status = $data[0] ?? null;
            $this->key = $data[1]  ?? null;
            $this->subKey = $data[2]  ?? null;
            $this->index = $data[3]  ?? null;
            $this->data = $data;
        }
    }

    public function update()
    {

        if ($this->status == null) return '{"error": "data status is missing"}';
        if ($this->key == null) return '{"error": "data key is missing"}';
        if ($this->subKey == null) return '{"error": "data subkey is missing"}';
        if ($this->index == null) return '{"error": "data index is missing"}';
        $jsonEntity = new FunctionalitiesJsonEntity;
        $data = $jsonEntity->getData();
        $newStatus = '';
        if ($this->status == 'ended') $newStatus = 'ongoing';
        elseif ($this->status == 'ongoing') $newStatus = 'ended';
        else $newStatus = 'ongoing';
        try {
            $data[$newStatus][$this->key][$this->subKey][] = $data[$this->status][$this->key][$this->subKey][$this->index];
            $data[$newStatus][$this->key][$this->subKey] = [...$data[$newStatus][$this->key][$this->subKey]];
            unset($data[$this->status][$this->key][$this->subKey][$this->index]);
            $data[$this->status][$this->key][$this->subKey] = [...$data[$this->status][$this->key][$this->subKey]];
            $jsonEntity->saveForce($data);
            return '{"success": "data updated successfully"}';
        } catch (Exception $e) {
            return '{ "Error" : "' . $e->getMessage() . '"}';
        }
    }

    public function insert(array $data)
    {
        if (($data['module'] ?? null) == null) return '{"error": "module is missing"}';
        if (($data['submodule'] ?? null) == null) return '{"error": "submodule is missing"}';
        if (($data['action'] ?? null) == null) return '{"error": "action is missing"}';
        $jsonEntity = new FunctionalitiesJsonEntity;
        $fetched = $jsonEntity->getData();
        $state = 'waiting';
        $module = $data['module'];
        $submodule = $data['submodule'];
        $action = $data['action'];
        try {
            if (isset($fetched[$state][$module][$submodule])) {
                if (!in_array($action, $fetched[$state][$module][$submodule])) {
                    $fetched[$state][$module][$submodule][] = $action;
                    $jsonEntity->saveForce($fetched);
                    return '{"success": "data added successfully"}';
                }
                return '{"error": "this action already is in waiting"}';
            } else {
                $fetched[$state][$module][$submodule] = [];
                $fetched[$state][$module][$submodule][] = $action;
                $jsonEntity->saveForce($fetched);
                return '{"success": "data added successfully"}';
            }
        } catch (Exception $e) {
            return '{ "Error" : "' . $e->getMessage() . '"}';
        }
    }


    public function __toString()
    {
        return  '{ "' . __CLASS__ . '" : "' . json_encode($this->data) . '"}';
    }
}
