<?php

namespace DBSystem\Tasks;

use DBSystem\Dumpers\DbDumper;
use DBSystem\Dumpers\Files;

class Restore extends Task
{
    /** @var string */
    protected $backup;

    public function __construct(string $backupFile = '')
    {
        parent::__construct();

        $this->backup = $backupFile;
    }

    public function addDatabase(DbDumper $dumper, string $file): self
    {
        $this->databaseDumpers[$file] = $dumper;

        return $this;
    }

    public function addFiles(Files $dumper, string $file, string $destination, array $only = null): self
    {
        $this->filesDumpers[$file] = [
            'dumper' => $dumper,
            'destination' => $destination,
            'only' => $only
        ];

        return $this;
    }

    public function execute()
    {
        $this->addLog('Starting restore task...');

        $backupPath = '';

        // if the file is a backup itself, don't extract it
        if ($this->backup) {
            $backupPath = $this->fileSystem->path($this->uid) . DIRECTORY_SEPARATOR;

            (new Files())->restoreFromFile($this->backup, $backupPath);
        }

        // restore the databases
        $this->addLog('Starting databases restore...');
        $this->restoreDatabases($backupPath);
        $this->addLog('Databases restore completed');

        // restore the files
        $this->addLog('Starting files restore...');
        $this->restoreFiles($backupPath);
        $this->addLog('Databases restore completed');

        $this->addLog('Restore task completed');
    }

    protected function restoreDatabases($backupPath)
    {
        foreach ($this->databaseDumpers as $file => $databaseDumper) {
            $databaseDumper->restoreFromFile($backupPath . $file);
        }
    }

    protected function restoreFiles($backupPath)
    {
        foreach ($this->filesDumpers as $file => $data) {
            $data['dumper']->restoreFromFile($backupPath . $file, $data['destination'], $data['only']);
        }
    }
}