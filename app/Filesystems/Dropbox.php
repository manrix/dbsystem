<?php

namespace DBSystem\Filesystems;

use League\Flysystem\Filesystem as Flysystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class Dropbox extends Filesystem
{
    protected $name = 'Dropbox';

    /**
     * @param array $config
     * @return Flysystem
     */
    public function get(array $config)
    {
        $client = new Client($config['token']);
        $adapter = new DropboxAdapter($client, $config['root']);

        return new Flysystem($adapter);
    }
}
