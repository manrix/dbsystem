<?php

namespace DBSystem\Compressors;

class TarShell extends ShellCompressor
{
    /**
     * @var bool
     */
    protected $compress = false;

    /**
     * Get the shell command to zip
     *
     * @param null $file
     * @param bool $update
     * @return string
     */
    protected function getCommand($file = null, $update = false)
    {
        $options = $update ? "-r" : "-c";
        if ($this->compress) {
            $options .= 'z';
        }
        $options .= 'f';

        $command[] = "tar";

        // add exclusions
        foreach ($this->filesToExclude as $fileToExclude) {
            $command[] = "--exclude='" . $fileToExclude . "'";
        }

        $command[] = $options;
        $command[] = $this->archive;

        if ($file) {
            $command[] = $file;
        } else {
            $command[] = "-T";
            $command[] = $this->filesList;
        }

        // force on local drive
        $command[] = "--force-local";

        return implode(' ', $command);
    }

    /**
     * @param bool $compress
     * @return TarShell
     */
    public function compress(bool $compress): TarShell
    {
        $this->compress = $compress;

        return $this;
    }
}