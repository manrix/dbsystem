<?php

namespace Tests\Unit\Dumpers;

use DBSystem\Dumpers\Files;
use Tests\TestCase;

class LargeFilesDumpTest extends TestCase
{
    public function test_it_creates_backup_of_large_files()
    {
        (new Files())
            ->addFiles('storage')
            ->dumpToFile('tests/backup_large.zip');

        $this->assertFileExists('tests/backup_large.zip');

        if (file_exists('tests/backup_large.zip')) {
            @unlink('tests/backup_large.zip');
        }
    }
}