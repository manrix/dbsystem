<?php

namespace DBSystem\Dumpers;

use DBSystem\Compressors\Gzip;
use DBSystem\Compressors\Tar;
use DBSystem\Compressors\TarShell;
use DBSystem\Compressors\Zip;
use DBSystem\Compressors\ZipShell;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Files implements FilesDumper
{
    /** @var array */
    protected $files = [];

    /** @var array */
    protected $excludedFiles = [];

    /** @var bool */
    protected $followLinks = false;

    /** @var bool */
    protected $useZip = true;

    /** @var bool */
    protected $compress = false;

    public function addFiles($files): self
    {
        $this->files = array_merge($this->files, $this->sanitize($files));

        return $this;
    }

    public function addExcludedFiles(array $excludedFiles): self
    {
        $this->excludedFiles = $excludedFiles;

        return $this;
    }

    public function setFollowLinks(bool $followLinks): self
    {
        $this->followLinks = $followLinks;

        return $this;
    }

    public function useZip(bool $useZip): self
    {
        $this->useZip = $useZip;

        return $this;
    }

    public function isZip(): bool
    {
        return $this->useZip;
    }

    public function useCompression(bool $useCompression): self
    {
        $this->compress = $useCompression;

        return $this;
    }

    /**
     * Dump the files to a destination archive
     *
     * @param $dumpFile
     */
    public function dumpToFile(string $dumpFile)
    {
        if ($this->useZip) {
            $zip = new Zip($dumpFile);
            foreach ($this->files as $file) {
                if (is_dir($file)) {
                    $zip->addFiles($this->selectFiles($file), $file);
                } else {
                    $zip->addFiles($this->selectFiles($file));
                }
            }

            $zip->close();
        } else {
            $tar = new Tar($dumpFile);
            foreach ($this->files as $file) {
                if (is_dir($file)) {
                    $tar->addFiles($this->selectFiles($file), $file);
                } else {
                    $tar->addFiles($this->selectFiles($file));
                }
            }

            // delete the tar if it was compressed
            if ($this->compress && $tar->compress()) {
                $tar->close();
                unlink($dumpFile);
            } else {
                $tar->close();
            }
        }
    }

    /**
     * Dump the files to a destination archive using shell
     *
     * @param string $dumpFile
     */
    public function dumpToFileShell(string $dumpFile)
    {
        if ($this->useZip) {
            $compressor = new ZipShell($dumpFile);
        } else {
            $compressor = new TarShell($dumpFile);
            $compressor->compress($this->compress);
        }

        $compressor->setFilesToExclude($this->excludedFiles)
            ->addFiles($this->files)
            ->execute();
    }

    /**
     * Restore files to destination
     *
     * @param string $backup
     * @param string $destination
     * @param array $only
     * @throws \Exception
     */
    public function restoreFromFile(string $backup, string $destination, $only = null)
    {
        $extension = pathinfo($backup, PATHINFO_EXTENSION);
        $isCompressed = $extension == 'gz';

        switch ($extension) {
            case 'zip':
                $zip = new Zip($backup);
                $zip->extractTo($destination, $only);
                $zip->close();
                break;
            case 'gz':
            case 'tar':
                if ($isCompressed) {
                    $gzip = new Gzip($backup);
                    $backup = $gzip->decompress();
                }

                $tar = new Tar($backup);
                $tar->extractTo($destination, $only);
                $tar->close();

                if ($isCompressed) {
                    unlink($backup);
                }
                break;
            default:
                throw new \InvalidArgumentException('Archive type not supported');
                break;
        }
    }

    /**
     * Find files and directories
     *
     * @param $path
     * @return \Generator|string[]
     */
    protected function selectFiles(string $path)
    {
        $finder = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->files();

        if ($this->followLinks) {
            $finder->followLinks();
        }

        if (is_file($path)) {
            yield $path;
        } elseif (is_dir($path)) {
            $finder->in($path);

            foreach ($finder->getIterator() as $file) {
                if ($this->shouldExclude($file)) {
                    continue;
                }
                yield $file->getPathname();
            }
        } else {
            return;
        }
    }

    /**
     * Find files and directories to exclude
     *
     * @param $path
     * @return \Generator|string[]
     */
    protected function selectFilesToExclude(string $path)
    {
        $finder = (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->files();

        if ($this->followLinks) {
            $finder->followLinks();
        }

        if (is_dir($path)) {
            $finder->in($path);
            foreach ($finder->getIterator() as $file) {
                if ($this->shouldExclude($file)) {
                    yield $file->getPathname();
                }
            }
        } else {
            return;
        }
    }

    /**
     * Check if file should be excluded
     *
     * @param $file
     * @return bool
     */
    protected function shouldExclude(SplFileInfo $file): bool
    {
        if (in_array($file->getFilename(), $this->excludedFiles)) {
            return true;
        }

        foreach ($this->excludedFiles as $excludedFile) {
            $path = str_replace('\\', '/', $file->getPathname());
            if (preg_match('/'. str_replace('/', '\/', $excludedFile) . '/', $path) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter files and return their paths
     *
     * @param $paths
     * @return array
     */
    protected function sanitize($paths): array
    {
        return collect($paths)
            ->reject(function ($path) {
                return $path === '';
            })
            ->flatMap(function ($path) {
                return glob(base_path($path));
            })
            ->map(function ($path) {
                return realpath($path);
            })
            ->reject(function ($path) {
                return $path === false;
            })->toArray();
    }

    /**
     * Store the files to dump in a temporary txt file
     *
     * @return bool|string
     */
    protected function getListFromFile(): string
    {
        $list = tempnam(sys_get_temp_dir(), 'tmp');
        $data = [];
        foreach ($this->files as $file) {
            foreach ($this->selectFiles($file) as $_file) {
                $data[] = $_file . PHP_EOL;
            }
        }

        file_put_contents($list . '.txt', $data);

        return $list . '.txt';
    }
}