<?php

namespace DBSystem\Filesystems;

use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\Filesystem as Flysystem;

class Ftp extends Filesystem
{
    protected $name = 'Ftp';

    /**
     * @param array $config
     * @return Flysystem
     */
    public function get(array $config)
    {
        return new Flysystem(new Adapter($config));
    }
}
