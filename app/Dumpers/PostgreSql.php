<?php

namespace DBSystem\Dumpers;

use Spatie\DbDumper\Exceptions\DumpFailed;
use Symfony\Component\Process\Process;
use Spatie\DbDumper\Databases\PostgreSql as PgDumper;

class PostgreSql extends PgDumper implements DbDumper
{
    /**
     * Restores a database backup from a file
     *
     * @param $backupFile
     * @throws DumpFailed
     * @throws \Spatie\DbDumper\Exceptions\CannotStartDump
     */
    public function restoreFromFile(string $backupFile)
    {
        $this->guardAgainstIncompleteCredentials();

        $command = $this->getRestoreCommand($backupFile);

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getContentsOfCredentialsFile());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        $process = new Process($command, null, $this->getEnvironmentVariablesForDumpCommand($temporaryCredentialsFile));

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
        $command = [
            "{$this->dumpBinaryPath}psql",
            "-U {$this->userName}",
            '-h '.($this->socket === '' ? $this->host : $this->socket),
            "-p {$this->port}"
        ];

        if (pathinfo($backupFile, PATHINFO_EXTENSION) === 'gz') {
            array_unshift($command, "gzip -dc {$backupFile} |");
        } else {
            $command[] = "-f \"{$backupFile}\"";
        }

        return implode(' ', $command);
    }

    public function compressionEnabled(): bool
    {
        return !is_null($this->compressor);
    }
}
