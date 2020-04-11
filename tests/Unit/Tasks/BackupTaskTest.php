<?php


namespace Tests\Unit;

use DBSystem\Dumpers\Files;
use DBSystem\Dumpers\MySql;
use DBSystem\Tasks\Backup;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Tests\TestCase;

class BackupTaskTest extends TestCase
{
    protected function getMysqlDumper()
    {
        return (new MySql())->setDbName($this->config['mysql']['database'])
            ->setUserName($this->config['mysql']['user'])
            ->setDumpBinaryPath($this->config['mysql']['bin'])
            ->useCompressor(new GzipCompressor());
    }

    protected function getFilesDumper()
    {
        return (new Files())
            ->addFiles($this->config['files']['path']);
    }

    public function test_basic_class_initialization()
    {
        $class = new Backup();

        $this->assertTrue($class instanceof Backup);
    }

    public function test_it_creates_a_database_backup()
    {
        $dumper = $this->getMysqlDumper();

        (new Backup())->addDatabase($dumper)
            ->setDestinationPath('tests')
            ->execute();

        $this->assertFileExists('tests/backup.zip');

        if (file_exists('tests/backup.zip')) {
            unlink('tests/backup.zip');
        }
    }

    public function test_it_creates_a_files_backup()
    {
        $dumper = $this->getFilesDumper();

        (new Backup())->addFiles($dumper)
            ->setDestinationPath('tests')
            ->execute();

        $this->assertFileExists('tests/backup.zip');

        if (file_exists('/tests/backup.zip')) {
            unlink('/tests/backup.zip');
        }
    }

    public function test_it_creates_a_full_backup()
    {
        $filesDumper = $this->getFilesDumper();
        $dbDumper = $this->getMysqlDumper();

        (new Backup())->addDatabase($dbDumper)
            ->addFiles($filesDumper)
            ->setDestinationPath('tests')
            ->setBackupName('full_test')
            ->execute();

        $this->assertFileExists('tests/full_test.zip');
        $zip = new \ZipArchive();
        $zip->open('tests/full_test.zip');
        $this->assertNotFalse($zip->locateName('files.zip'));
        $this->assertNotFalse($zip->locateName('sakila.sql.gz'));
        $zip->close();

        if (file_exists('tests/full_test.zip')) {
            unlink('tests/full_test.zip');
        }
    }
}