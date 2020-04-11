<?php

namespace DBSystem\Dumpers;

use Spatie\DbDumper\Exceptions\DumpFailed;
use Symfony\Component\Process\Process;
use Spatie\DbDumper\Databases\Sqlite as SqliteDumper;

class Sqlite extends SqliteDumper implements DbDumper
{
    /**
     * Restores a database backup from a file
     *
     * @param $backupFile
     * @throws DumpFailed
     */
    public function restoreFromFile(string $backupFile)
    {
        $command = $this->getRestoreCommand($backupFile);
        $process = new Process($command);

        if (!is_null($this->timeout)) {
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
        if (pathinfo($backupFile, PATHINFO_EXTENSION) === 'gz') {
            return sprintf(
                "gzip -dc %s | %ssqlite3 --bail %s",
                $backupFile,
                $this->dumpBinaryPath,
                $this->dbName
            );
        } else {
            return sprintf(
                "%ssqlite3 --bail %s < %s",
                $this->dumpBinaryPath,
                $this->dbName,
                $backupFile
            );
        }
    }

    public function compressionEnabled(): bool
    {
        return !is_null($this->compressor);
    }
}
