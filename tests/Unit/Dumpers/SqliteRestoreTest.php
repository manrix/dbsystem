<?php

namespace Tests\Unit;

use DBSystem\Dumpers\Sqlite;
use Tests\TestCase;

class SqliteRestoreTest extends TestCase
{
    public function __construct($name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (!file_exists($this->config['sqlite']['database'])) {
            new \PDO('sqlite:'.$this->config['sqlite']['database']);
        }
    }

    protected function getDumper()
    {
        return (new Sqlite())->setDbName($this->config['sqlite']['database']);
    }

    public function test_it_restores_a_backup()
    {
        $this->getDumper()->restoreFromFile($this->config['sqlite']['database2']);

        $this->assertFileExists($this->config['sqlite']['database']);

        if (file_exists($this->config['sqlite']['database'])) {
            unlink($this->config['sqlite']['database']);
        }
    }

    public function test_it_restores_a_gzipped_backup()
    {
        $this->getDumper()->restoreFromFile($this->config['sqlite']['database2'].'.gz');

        $this->assertFileExists('tests/test.sqlite');

        if (file_exists('tests/test.sqlite')) {
            unlink('tests/test.sqlite');
        }
    }
}