<?php

namespace Tests\Unit;

use DBSystem\Dumpers\Files;
use Tests\TestCase;

class FilesDumpTest extends TestCase
{
    protected function getDumper()
    {
        return (new Files())
            ->addFiles($this->config['files']['path']);
    }

    public function test_it_creates_a_zip_backup()
    {
        $this->getDumper()->dumpToFile('tests/backup.zip');

        $this->assertFileExists('tests/backup.zip');

        if (file_exists('tests/backup.zip')) {
            unlink('tests/backup.zip');
        }
    }

    public function test_it_creates_a_zip_backup_without_excluded_files()
    {
        $dumper = $this->getDumper();
        $dumper->addExcludedFiles([
            'sample2.txt',
            'folder',
            'backup.zip'
        ])->dumpToFile('tests/backup.zip');

        $this->assertFileExists('tests/backup.zip');
        $zip = new \ZipArchive();
        $zip->open('tests/backup.zip');
        $this->assertNotTrue($zip->locateName('sample2.txt'));
        $zip->close();

        if (file_exists('tests/backup.zip')) {
            unlink('tests/backup.zip');
        }
    }

    public function test_it_creates_a_tar_backup()
    {
        $dumper = $this->getDumper();
        $dumper->useZip(false)->dumpToFile('tests/backup.tar');

        $this->assertFileExists('tests/backup.tar');

        if (file_exists('tests/backup.tar')) {
            unlink('tests/backup.tar');
        }
    }

    public function test_it_creates_a_gzipped_backup()
    {
        $dumper = $this->getDumper();
        $dumper->useZip(false)
            ->useCompression(true)
            ->dumpToFile('tests/backup.tar');

        $this->assertFileExists('tests/backup.tar.gz');

        if (file_exists('tests/backup.tar.gz')) {
            unlink('tests/backup.tar.gz');
        }
    }

    public function test_it_throws_exception_on_restore()
    {
        $this->expectException(\InvalidArgumentException::class);

        $dumper = $this->getDumper();
        $dumper->restoreFromFile('tests/samples/backup', 'tests');
    }

    public function test_it_restores_a_backup()
    {
        $dumper = $this->getDumper();
        $dumper->restoreFromFile($this->config['files']['backup'], 'tests');

        $this->assertFileExists('tests/sample.txt');
    }

    public function test_it_creates_a_zip_backup_with_shell()
    {
        $destination = 'tests/backup.zip';

        $this->getDumper()->dumpToFileShell(base_path($destination));

        $this->assertFileExists($destination);

        if (file_exists($destination)) {
            @unlink($destination);
        }
    }

    public function test_it_creates_a_tar_backup_with_shell()
    {
        $destination = 'tests/backup.tar';

        $this->getDumper()->useZip(false)->dumpToFileShell(base_path($destination));

        $this->assertFileExists($destination);

        if (file_exists($destination)) {
            @unlink($destination);
        }
    }
}