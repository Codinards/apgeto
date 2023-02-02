<?php

namespace App\Tools\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheRetriever
{
    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    public function getCache(): FilesystemAdapter
    {
        return $this->cache;
    }

    public static function cache()
    {
        return (new CacheRetriever)->getCache();
    }
}
