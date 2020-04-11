<?php

namespace DBSystem\Compressors;

class Tar
{
    /**
     * @var \PharData
     */
    protected $phar;

    /**
     * @var string
     */
    protected $archive;

    public function __construct($archive)
    {
        $this->phar = new \PharData($archive);
        $this->archive = $archive;
    }

    /**
     * @param array $files
     * @param bool|string $directory
     * @return \PharData
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

                $this->phar->addFile($file, $fileName);
            }
        }

        return $this->phar;
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
     * @param bool $overwrite
     * @return bool
     */
    public function extractTo($destination, $only = null, $overwrite = true)
    {
        return $this->phar->extractTo($destination, $only, $overwrite);
    }

    /**
     * @param $type
     * @return bool|object
     */
    public function compress(string $type = 'gzip')
    {
        if (\Phar::canCompress()) {
            if ($type === 'gzip') {
                return $this->phar->compress(\Phar::GZ);
            } elseif ($type === 'bzip2') {
                return $this->phar->compress(\Phar::BZ2);
            }
        }

        return false;
    }

    public function close()
    {
        unset($this->phar);
    }

    public function getArchive()
    {
        return $this->archive;
    }
}