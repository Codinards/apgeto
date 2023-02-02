<?php

namespace App\Tools\Entity;

use Illuminate\Support\Collection;

class ContributorData
{
    private $contributors;

    private $formContributorsRef = [];

    static $index = 0;

    static $instance;

    /**
     * Get the value of contributors
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Set the value of contributors
     *
     * @return  self
     */
    public function setContributors($contributors)
    {
        $this->contributors = $contributors;

        return $this;
    }

    public function getContributor()
    {
        $contributor = $this->contributors[self::$index] ?? null;
        self::$index++;
        return $contributor;
    }

    /**
     * Get the value of formContributorsRef
     */
    public function getFormContributorsRef()
    {
        return $this->formContributorsRef;
    }

    /**
     * Set the value of formContributorsRef
     *
     * @return  self
     */
    public function setFormRef(int $index, &$form)
    {
        $this->formContributorsRef[$index] = $form;
        return $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new ContributorData();
        }
        return self::$instance;
    }
}
