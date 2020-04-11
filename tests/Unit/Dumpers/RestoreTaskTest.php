<?php

namespace Tests\Unit;

use DBSystem\Dumpers\Files;
use DBSystem\Dumpers\MySql;
use DBSystem\Tasks\Restore;
use Tests\TestCase;

class RestoreTaskTest extends TestCase
{
    protected $pdo;

    protected $resetQuery;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->pdo = new \PDO("mysql:host=localhost;dbname={$this->config['mysql']['database2']}", $this->config['mysql']['user']);
        $this->resetQuery = "DROP DATABASE IF EXISTS '{$this->config['mysql']['database2']}';";
    }

    protected function getMysqlDumper()
    {
        return (new MySql())->setDbName($this->config['mysql']['database2'])
            ->setUserName($this->config['mysql']['user'])
            ->setDumpBinaryPath($this->config['mysql']['bin'])
            ->enableCompression();
    }

    protected function getFilesDumper()
    {
        return new Files();
    }

    protected function assertDatabaseNotEmpty()
    {
        $tables = $this->pdo->query("SHOW TABLES")->fetchAll();
        $tables = array_map(function ($table){
            return $table[0];
        }, $tables);

        $this->assertGreaterThan(0, count($tables));
    }

    public function test_basic_class_initialization()
    {
        $class = new Restore('tests/backup.zip');

        $this->assertTrue($class instanceof Restore);
    }

    public function test_it_restores_a_self_files_backup()
    {
        $dumper = $this->getFilesDumper();

        (new Restore())
            ->addFiles($dumper, 'tests/backup.zip', 'tests')
            ->execute();

        $this->assertFileExists('tests/sample.txt');

        if (file_exists('tests/sample.txt')) {
            unlink('tests/sample.txt');
        }
    }

    public function test_it_restores_a_files_backup()
    {
        $dumper = $this->getFilesDumper();

        (new Restore('tests/tests.zip'))
            ->addFiles($dumper, 'backup.zip', 'tests')
            ->execute();

        $this->assertFileExists('tests/sample.txt');

        if (file_exists('tests/sample.txt')) {
            unlink('tests/sample.txt');
        }
    }

    public function test_it_restores_a_self_database_backup()
    {
        $dumper = $this->getMysqlDumper();

        (new Restore())
            ->addDatabase($dumper, 'tests/samples/sakila.sql.gz')
            ->execute();

        $this->assertDatabaseNotEmpty();

        $this->pdo->query($this->resetQuery);
    }

    public function test_it_restores_a_full_backup_from_zip()
    {
        $dbDumper = $this->getMysqlDumper();
        $filesDumper = $this->getFilesDumper();

        (new Restore('tests/samples/samples.zip'))
            ->addDatabase($dbDumper, 'sakila.sql')
            ->addFiles($filesDumper, 'backup.zip', 'tests/restored')
            ->execute();

        $this->assertFileExists('tests/restored/sample.txt');

        $this->assertDatabaseNotEmpty();

        $this->pdo->query($this->resetQuery);

        if (file_exists('tests/restored/sample.txt')) {
            @unlink('tests/restored');
        }
    }

    public function test_it_restores_a_full_backup_from_tar()
    {
        $dbDumper = $this->getMysqlDumper();
        $filesDumper = $this->getFilesDumper();

        (new Restore('tests/samples/samples.tar.gz'))
            ->addDatabase($dbDumper, 'sakila.sql')
            ->addFiles($filesDumper, 'backup.zip', 'tests/restored')
            ->execute();

        $this->assertFileExists('tests/restored/sample.txt');

        $this->assertDatabaseNotEmpty();

        $this->pdo->query($this->resetQuery);

        if (file_exists('tests/restored/sample.txt')) {
            @unlink('tests/restored');
        }
    }
}