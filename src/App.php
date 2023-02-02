<?php

namespace App;

class App
{
    protected $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }
}
