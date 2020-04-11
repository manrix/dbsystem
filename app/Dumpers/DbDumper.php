<?php

namespace DBSystem\Dumpers;

interface DbDumper
{
    /**
     * @param string $dumpFile
     * @return mixed
     */
    public function dumpToFile(string $dumpFile);

    /**
     * @param string $backupFile
     * @return mixed
     */
    public function restoreFromFile(string $backupFile);

    /**
     * @return bool
     */
    public function compressionEnabled(): bool;
}
