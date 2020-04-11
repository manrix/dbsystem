<?php

namespace DBSystem\Dumpers;

interface FilesDumper
{
    /**
     * @param string $dumpFile
     * @return mixed
     */
    public function dumpToFile(string $dumpFile);

    /**
     * @param string $backup
     * @param string $destination
     * @param null $only
     * @return mixed
     */
    public function restoreFromFile(string $backup, string $destination, $only = null);
}