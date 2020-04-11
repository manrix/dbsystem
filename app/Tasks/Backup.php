<?php

namespace DBSystem\Tasks;

use DBSystem\Compressors\Tar;
use DBSystem\Compressors\Zip;
use DBSystem\Dumpers\DbDumper;
use DBSystem\Dumpers\Files;
use League\Flysystem\Filesystem;

class Backup extends Task
{
    /** @var array */
    protected $destinations = [];

    /** @var bool */
    protected $useZip = true;

    /** @var string */
    protected $destinationPath = './';

    /** @var string */
    protected $backupName = 'backup';

    /** @var string */
    protected $filesBackupName = 'files';

    /** @var array */
    protected $filesToArchive = [];

    /** @var array */
    protected $manifest = [
        'databases' => [],
        'files' => [],
    ];

    /** @var bool */
    protected $useShell = false;

    public function addDatabase(DbDumper $dumper): self
    {
        $this->databaseDumpers[] = $dumper;

        return $this;
    }

    public function addFiles(Files $dumper): self
    {
        $this->filesDumpers[] = $dumper;

        return $this;
    }

    public function addDestination(Filesystem $destination): self
    {
        $this->destinations[] = $destination;

        return $this;
    }

    public function execute(): array
    {
        $this->addLog('Starting backup task...');

        $this->validateBackupExtension();

        $backupPath = $this->fileSystem->path($this->uid) . DIRECTORY_SEPARATOR;

        // Backup the databases
        if (count($this->databaseDumpers)) {
            $this->addLog('Starting backup databases...');
            $this->dumpDatabases($backupPath);
            $this->addLog('Databases backup completed');
        }

        // Backup the files
        if (count($this->filesDumpers)) {
            $this->addLog('Starting backup files...');
            $this->dumpFiles($backupPath);
            $this->addLog('Files backup completed');
        }

        // Create the manifest
        $this->makeManifest($backupPath);

        if (!count($this->filesToArchive)) {
            throw new \Exception("Backup failed. No backup created.");
        }

        // Add all generated backups to a unique archive
        $this->addLog('Archive and compress backups');
        $this->archiveBackups($backupPath);

        // Move the backup to final destination
        $this->addLog('Move backup to destination');
        if (!copy($backupPath . $this->backupName, $this->destinationPath . $this->backupName)) {
            throw new \Exception("Failed moving backup to destination {$this->destinationPath}");
        }

        // Upload the backup to other destinations
        if (count($this->destinations)) {
            $this->addLog('Starting writing backup to additional destinations...');
            $this->writeToDestinations();
            $this->addLog('Backup transfer completed');
        }

        $this->addLog('Backup task completed');

        return [
            'name' => $this->backupName,
            'path' => $this->destinationPath . $this->backupName,
            'size' => $this->fileSystem->size($this->uid . DIRECTORY_SEPARATOR . $this->backupName),
            'modified' => $this->fileSystem->lastModified($this->uid . DIRECTORY_SEPARATOR . $this->backupName),
        ];
    }

    protected function makeManifest($backupPath)
    {
        $file = $this->uid . DIRECTORY_SEPARATOR . 'manifest.json';
        $this->fileSystem->put($file, json_encode($this->manifest));
        $this->filesToArchive[] = $backupPath . DIRECTORY_SEPARATOR . 'manifest.json';
    }

    protected function dumpDatabases($backupPath)
    {
        foreach ($this->databaseDumpers as $dbDumper) {
            $backupName = $dbDumper->getDbName() . '.sql';
            if ($dbDumper->compressionEnabled()) {
                $backupName .= '.gz';
            }
            $backupFile = $backupPath . $backupName;
            $dbDumper->dumpToFile($backupFile);

            if (file_exists($backupFile)) {
                $this->filesToArchive[] = $backupFile;
                $this->manifest['databases'][] = $backupName;
            }
        }
    }

    protected function dumpFiles($backupPath)
    {
        foreach ($this->filesDumpers as $fileDumper) {
            $backupName = $this->filesBackupName;
            $backupName .= $fileDumper->isZip() ? '.zip' : '.tar';
            $backupFile = $backupPath . $backupName;
            if ($this->useShell) {
                $fileDumper->dumpToFileShell($backupFile);
            } else {
                $fileDumper->dumpToFile($backupFile);
            }

            if (file_exists($backupFile)) {
                $this->filesToArchive[] = $backupFile;
                $this->manifest['files'][] = $backupName;
            }
        }
    }

    protected function writeToDestinations()
    {
        foreach ($this->destinations as $destination) {
            $destination->put(
                $this->backupName,
                $this->fileSystem->get($this->uid . DIRECTORY_SEPARATOR . $this->backupName)
            );
        }
    }

    protected function archiveBackups($backupPath)
    {
        if ($this->useZip) {
            $zip = new Zip($backupPath . $this->backupName);
            $zip->addFiles($this->filesToArchive);
            $zip->close();
        } else {
            $tar = new Tar($backupPath . $this->backupName);
            $tar->addFiles($this->filesToArchive);
            $tar->close();
        }
    }

    protected function validateBackupExtension()
    {
        $path_info = pathinfo($this->backupName);
        if ($this->useZip) {
            $this->backupName .= !isset($path_info['extension']) || $path_info['extension'] !== 'zip' ? '.zip' : '';
        } else {
            $this->backupName .= !isset($path_info['extension']) || $path_info['extension'] !== 'tar' ? '.tar' : '';
        }
    }

    /**
     * @param string $filesBackupName
     * @return Backup
     */
    public function setFilesBackupName($filesBackupName): self
    {
        $this->filesBackupName = $filesBackupName;

        return $this;
    }

    /**
     * @param mixed $backupName
     * @return Backup
     */
    public function setBackupName($backupName): self
    {
        $this->backupName = $backupName;

        return $this;
    }

    /**
     * @param string $destinationPath
     * @return Backup
     */
    public function setDestinationPath(string $destinationPath): self
    {
        $this->destinationPath = rtrim($destinationPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * @param bool $useZip
     * @return Backup
     */
    public function setUseZip(bool $useZip): Backup
    {
        $this->useZip = $useZip;

        return $this;
    }

    /**
     * @param bool $useShell
     * @return Backup
     */
    public function useShell(bool $useShell): Backup
    {
        $this->useShell = $useShell;

        return $this;
    }
}