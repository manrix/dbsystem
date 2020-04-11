<?php

namespace DBSystem\Traits;

use DBSystem\Destination;
use DBSystem\Filesystems\Dropbox;
use DBSystem\Filesystems\Ftp;
use DBSystem\Filesystems\GoogleDrive;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

trait HandlesDestinations
{
    /**
     * Upload a file to flysystem destination
     *
     * @param Destination $destination
     * @param string $path
     * @param $file
     * @return bool
     * @throws \Exception
     */
    protected function uploadToDestination(Destination $destination, string $path, $file)
    {
        $filesystem = $this->resolveDestinationClass($destination);

        return $filesystem->put($path, $file);
    }

    /**
     * Download a file from flysystem destination
     *
     * @param Destination $destination
     * @param string $file
     * @return \League\Flysystem\Directory|\League\Flysystem\File|\League\Flysystem\Handler
     * @throws \Exception
     */
    protected function downloadFromDestination(Destination $destination, string $file)
    {
        $filesystem = $this->resolveDestinationClass($destination);

        return $filesystem->read($file);
    }

    /**
     * Delete file from flysystem destination
     *
     * @param Destination $destination
     * @param string $file
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function deleteFromDestination(Destination $destination, string $file)
    {
        $filesystem = $this->resolveDestinationClass($destination);

        // if is google drive get the file id
        if ($destination->driver === 'g3') {
            $driverFiles = $filesystem->listContents();
            foreach ($driverFiles as $driverFile) {
                if ($driverFile['name'] === $file) {
                    $file = $driverFile['path'];
                    break;
                }
            }
        }

        return $filesystem->delete($file);
    }

    /**
     * Resolve destination class by driver
     *
     * @param Destination $destination
     * @return \League\Flysystem\Filesystem
     * @throws \Exception
     */
    protected function resolveDestinationClass(Destination $destination)
    {
        switch ($destination->driver) {
            case 'ftp':
                $adapter = new Ftp();

                return $adapter->get([
                    'host' => $destination->host,
                    'username' => $destination->user,
                    'password' => $destination->password,

                    /** optional config settings */
                    'port' => $destination->port,
                    'root' => $destination->root,
                    'passive' => $destination->passive,
                    'ssl' => $destination->ssl,
                ]);
            case 'dropbox':
                $adapter = new Dropbox();

                return $adapter->get([
                    'token' => $destination->token,
                    'root' => $destination->root,
                ]);
            case 'g3':
                $adapter = new GoogleDrive();

                return  $adapter->get([
                    'client_id' => $destination->client_id,
                    'client_secret' => $destination->client_secret,
                    'refresh_token' => $destination->refresh_token,
                    'root' => $destination->root,
                ]);
            case 'local':
                $adapter = new Local(base_path($destination->root));
                return  new Filesystem($adapter);
            default:
                throw new \Exception('Unknown destination driver');
        }
    }
}