<?php

namespace App\Tools\Entity;

use Illuminate\Support\Collection;

class DataProvider
{
    private $data;

    private $formDataRef = [];

    static $index = 0;

    static $instance;

    /**
     * Get the value of data
     */
    public function getAllData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        $data = $this->data[self::$index] ?? null;
        self::$index++;
        return $data;
    }


    public function getFormDataRef()
    {
        return $this->formdataRef;
    }

    /**
     * @return  self
     */
    public function setFormRef(int $index, &$form)
    {
        $this->formdataRef[$index] = $form;
        return $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new DataProvider();
        }
        return self::$instance;
    }
}
