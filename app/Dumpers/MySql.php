<?php

namespace DBSystem\Dumpers;

use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;
use Symfony\Component\Process\Process;
use Spatie\DbDumper\Databases\MySql as MySqlDumper;

class MySql extends MySqlDumper implements DbDumper
{
    /**
     * Restores a database backup from a file
     *
     * @param $backupFile
     * @throws CannotStartDump
     * @throws DumpFailed
     */
    public function restoreFromFile(string $backupFile)
    {
        $this->guardAgainstIncompleteCredentials();

        $process = null;
        $command = $this->getRestoreCommand($backupFile);
        $process = new Process($command);

        if (! is_null($this->timeout)) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        if (! $process->isSuccessful()) {
            throw DumpFailed::processDidNotEndSuccessfully($process);
        }
    }

    /**
     * Get the command that should be performed to restore the database.
     *
     * @param string $backupFile
     *
     * @return string
     */
    public function getRestoreCommand($backupFile)
    {
        $quote = $this->determineQuote();

        $command = [
            "{$quote}{$this->dumpBinaryPath}mysql{$quote}",
            "--host={$this->host}",
            "--port={$this->port}",
            "--user={$this->userName}",
            "--password=\"{$this->password}\"",
            $this->dbName
        ];

        if (pathinfo($backupFile, PATHINFO_EXTENSION) === 'gz') {
            array_unshift($command, "gzip -dc {$backupFile} |");
        } else {
            $command[] = "-e \"source {$backupFile}\"";
        }

        return implode(' ', $command);
    }

    public function compressionEnabled(): bool
    {
        return !is_null($this->compressor);
    }
}
