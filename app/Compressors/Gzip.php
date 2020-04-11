<?php

namespace DBSystem\Compressors;

class Gzip
{
    /** @var float|int */
    protected $buffer;

    /** @var string */
    protected $path;

    /** @var string */
    protected $file;

    public function __construct($file, $path = '')
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("File `{$file}` does not exist.");
        }

        $this->file = $file;
        $this->path = $path;
        $this->buffer = 1024 * 512;
    }

    /**
     * Compress a file and adds extension .gz
     *
     * @return string
     */
    public function compress()
    {
        $inputHandle = fopen($this->file, 'rb');
        $outputFile = $this->path . $this->file . '.gz';
        $outputHandle = gzopen($outputFile, 'w9');

        while (!feof($inputHandle)) {
            gzwrite($outputHandle, fread($inputHandle, $this->buffer));
        }

        fclose($inputHandle);
        gzclose($outputHandle);

        return $outputFile;
    }

    /**
     * Decompress a .gz file
     *
     * @return mixed
     */
    public function decompress()
    {
        $outputFile = $this->path . str_replace('.gz', '', $this->file);

        $inputHandle = gzopen($this->file, 'rb');
        $outputHandle = fopen($outputFile, 'wb');

        while (!gzeof($inputHandle)) {
            fwrite($outputHandle, gzread($inputHandle, $this->buffer));
        }

        fclose($outputHandle);
        gzclose($inputHandle);

        return $outputFile;
    }
}