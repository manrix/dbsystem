<?php

namespace DBSystem\Filesystems;

abstract class Filesystem
{
    protected $name = '';

    /**
     * Get filesystem instance
     *
     * @param array $config
     * @return \League\Flysystem\Filesystem
     */
    abstract public function get(array $config);

    /**
     * Get the name of filesystem
     *
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }
} 
