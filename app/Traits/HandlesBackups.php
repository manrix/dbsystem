<?php

namespace DBSystem\Traits;

use DBSystem\Backup;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

trait HandlesBackups
{
    use HandlesDestinations;

    /**
     * Delete multiple backups
     *
     * @param array $backups
     * @throws \Exception
     */
    public function bulkDelete(array $backups)
    {
        foreach ($backups as $backup) {
            $backup = Backup::with('destinations.destination')->find($backup['id']);

            if ($backup->saved_locally) {
                Storage::disk('backups')->delete($backup->name);
            }

            foreach ($backup->destinations as $backupDestination) {
                if ($backupDestination->path) {
                    $backupDestination->destination->root = $backupDestination->path;
                }

                try {
                    $this->deleteFromDestination($backupDestination->destination, $backup->name);
                } catch (FileNotFoundException $exception) {
                } finally {
                    $backupDestination->delete();
                }
            }

            $backup->delete();
        }
    }
}