<?php

namespace DBSystem\Compressors;

use Symfony\Component\Process\Process;

abstract class ShellCompressor
{
    /**
     * @var string
     */
    protected $archive;

    /**
     * @var mixed
     */
    protected $filesList;

    /**
     * @var integer
     */
    protected $timeout = 0;

    /**
     * @var array
     */
    protected $filesToExclude = [];

    public function __construct($archive)
    {
        $this->archive = $archive;
    }

    /**
     * Execute the process
     * If files is a file, take the list of files from it.
     * If is an array execute the process on each one separately.
     */
    public function execute()
    {
        if (!is_array($this->filesList) && is_file($this->filesList)) {
            $process = null;
            $command = $this->getCommand();
            $process = new Process($command);

            if (! is_null($this->timeout)) {
                $process->setTimeout($this->timeout);
            }

            $process->disableOutput()->mustRun();
        } else if (is_array($this->filesList)) {
            $i = 0;
            foreach ($this->filesList as $file) {
                $process = null;
                $info = pathinfo($file);

                $command = $this->getCommand($info['filename'], $i > 0);
                $process = new Process($command, realpath($info['dirname']));

                if (! is_null($this->timeout)) {
                    $process->setTimeout($this->timeout);
                }

                $process->disableOutput()->mustRun();

                $i++;
            }
        } else {
            throw new \InvalidArgumentException('Missing list of files to zip');
        }
    }

    /**
     * @param null $file
     * @param bool $update
     * @return mixed
     */
    abstract protected function getCommand($file = null, $update = false);

    /**
     * @param int $timeout
     * @return ShellCompressor
     */
    public function setTimeout(int $timeout): ShellCompressor
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param $files
     * @return ShellCompressor
     */
    public function addFiles($files): ShellCompressor
    {
        $this->filesList = $files;

        return $this;
    }

    /**
     * @param array $filesToExclude
     * @return ShellCompressor
     */
    public function setFilesToExclude(array $filesToExclude): ShellCompressor
    {
        $this->filesToExclude = $filesToExclude;

        return $this;
    }
}