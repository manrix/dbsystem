<?php

namespace Tests\Unit\Compressors;

use DBSystem\Compressors\TarShell;
use Tests\TestCase;

class TarShellTest extends TestCase
{
    protected $destination = 'tests/zip_shell_test.tar';

    public function test_it_fails_with_no_file()
    {
        $this->expectException('InvalidArgumentException');

        $compressor = new TarShell(base_path($this->destination));
        $compressor->execute();
    }

    public function test_it_creates_a_tar_archive_from_array_of_files()
    {
        $compressor = new TarShell(base_path($this->destination));
        $compressor->addFiles(['tests/samples/folder'])->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }

    public function test_it_creates_a_tar_archive_from_array_of_files_with_exclusions()
    {
        $compressor = new TarShell(base_path($this->destination));
        $compressor->addFiles(['tests/samples/folder'])
            ->setFilesToExclude(['folder/sample.txt'])->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }

    public function test_it_creates_a_tar_archive_from_a_list_in_a_file()
    {
        $compressor = new TarShell(base_path($this->destination));
        $compressor->addFiles('tests/samples/list_of_files.txt')->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }

    public function test_it_creates_a_tar_archive_with_gzip_compression()
    {
        $this->destination .= '.gz';

        $compressor = new TarShell(base_path($this->destination));
        $compressor->compress(true)
            ->addFiles('tests/samples/list_of_files.txt')->execute();

        $this->assertFileExists($this->destination);

        if (file_exists($this->destination)) {
            @unlink($this->destination);
        }
    }
}