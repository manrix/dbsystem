<?php

namespace Tests\Unit\Compressors;

use DBSystem\Compressors\ZipShell;
use Tests\TestCase;

class ZipShellTest extends TestCase
{
    protected $destination = 'tests/zip_shell_test.zip';

    public function test_it_fails_with_no_file()
    {
        $this->expectException('InvalidArgumentException');

        $compressor = new ZipShell(base_path($this->destination));
        $compressor->execute();
    }

    public function test_it_creates_a_zip_archive_from_array_of_files()
    {
        $compressor = new ZipShell(base_path($this->destination));
        $compressor->addFiles(['tests/samples/folder'])->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }

    public function test_it_creates_a_zip_archive_from_array_of_files_with_exclusions()
    {
        $compressor = new ZipShell(base_path($this->destination));
        $compressor->addFiles(['tests/samples/folder'])
            ->setFilesToExclude(['folder/sample.txt'])->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }

    public function test_it_creates_a_zip_archive_from_a_list_in_a_file()
    {
        $compressor = new ZipShell(base_path($this->destination));
        $compressor->addFiles('tests/samples/list_of_files.txt')->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }
}