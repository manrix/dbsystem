<?php

namespace DBSystem\Filesystems;

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem as Flysystem;

class GoogleDrive extends Filesystem
{
    protected $name = 'Google Drive';

    /**
     * @param array $config
     * @return Flysystem
     */
    public function get(array $config)
    {
        $client = new \Google_Client();
        $client->setClientId($config['client_id']);
        $client->setClientSecret($config['client_secret']);
        $client->refreshToken($config['refresh_token']);

        $service = new \Google_Service_Drive($client);

        $adapter = new GoogleDriveAdapter($service, $config['root']);

        return new Flysystem($adapter);
    }
}