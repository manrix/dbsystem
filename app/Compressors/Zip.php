<?php

namespace DBSystem\Compressors;

class Zip
{
    /**
     * @var \ZipArchive
     */
    protected $zip;

    /**
     * @var string
     */
    protected $archive;

    public function __construct($archive)
    {
        $this->zip = new \ZipArchive();
        $this->archive = $archive;

        if (file_exists($this->archive)) {
            $this->zip->open($this->archive, \ZipArchive::EXCL);
        } else {
            $this->zip->open($this->archive, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        }
    }

    /**
     * @param array $files
     * @param bool|string $directory
     * @return \ZipArchive
     */
    public function addFiles($files, $directory = false)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $fileName = $this->getFileNameToArchive($file, $this->archive);
                if ($directory) {
                    $directory = ltrim($directory, '/\\');
                    $fileName = ltrim(str_replace($directory, '', $fileName), '/\\');
                    $fileName = basename($directory) . DIRECTORY_SEPARATOR . $fileName;
                } else {
                    $fileName = pathinfo($fileName, PATHINFO_BASENAME);
                }

                $this->zip->addFile($file, $fileName);
            }
        }

        return $this->zip;
    }

    protected function getFileNameToArchive($pathToFile, $pathToArchive)
    {
        $fileDirectory = pathinfo($pathToFile, PATHINFO_DIRNAME);
        $archiveDirectory = pathinfo($pathToArchive, PATHINFO_DIRNAME);

        // check if the file is inside the archive directory
        if ($pathToArchive && strpos($fileDirectory, $archiveDirectory) === 0) {
            $pathToFile = str_replace($archiveDirectory, '', $pathToFile);
        }

        return ltrim($pathToFile, '/\\');
    }

    /**
     * @param $destination
     * @param null $only
     * @return bool
     * @throws \Exception
     */
    public function extractTo($destination, $only = null)
    {
        if (!$this->zip->open($this->archive) === true) {
            throw new \Exception('Failed opening zip archive');
        };

        return $this->zip->extractTo($destination, $only);
    }

    public function close()
    {
        return $this->zip->close();
    }

    public function getArchive()
    {
        return $this->archive;
    }
}