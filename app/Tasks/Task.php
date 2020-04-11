<?php

namespace DBSystem\Tasks;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class Task
{
    const FILESYSTEM = 'dbsystem';

    /** @var array */
    protected $databaseDumpers = [];

    /** @var array */
    protected $filesDumpers = [];

    /** @var string */
    protected $backupTempPath = 'dbsystem';

    /** @var Filesystem */
    protected $fileSystem;

    /** @var Logger */
    protected $logger;

    /** @var string */
    protected $uid;

    public function __construct()
    {
        $this->uid = uniqid();
        $this->fileSystem = Storage::disk(self::FILESYSTEM);
        $this->fileSystem->createDir($this->uid);
    }

    public function __destruct()
    {
        $this->fileSystem->deleteDir($this->uid);
    }

    public abstract function execute();

    /**
     * @param string $backupTempPath
     * @return Backup
     */
    public function setBackupTempPath(string $backupTempPath): self
    {
        $this->backupTempPath = rtrim($backupTempPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * @param Filesystem $fileSystem
     * @return Backup
     */
    public function setFileSystem(Filesystem $fileSystem): self
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return Task
     */
    public function setLogger(LoggerInterface $logger): Task
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    protected function addLog(string $message, $level = LogLevel::INFO)
    {
        if ($this->logger) {
            $this->logger->log($level, $message);
        }
    }
}