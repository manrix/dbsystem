<?php

namespace DBSystem\Compressors;

class ZipShell extends ShellCompressor
{
    /**
     * Get the shell command to zip
     *
     * @param null $file
     * @param bool $update
     * @return string
     */
    protected function getCommand($file = null, $update = false)
    {
        $command = [
            "zip",
            "-r",
            $this->archive
        ];

        if ($file) {
            $command[] = $file;
        } else {
            $command[] = '-@';
            $command[] = '<';
            $command[] = $this->filesList;
        }

        if (count($this->filesToExclude)) {
            $command[] = "-x";
            $command[] = implode(' ', array_map(function ($fileToExclude) {
                return '"'. $fileToExclude . '"';
            }, $this->filesToExclude));
        }

        return implode(' ', $command);
    }
}