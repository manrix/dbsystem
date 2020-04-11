<?php

namespace DBSystem\Traits;

use DBSystem\Database;
use DBSystem\Dumpers\MySql;
use DBSystem\Dumpers\PostgreSql;
use DBSystem\Dumpers\Sqlite;

trait HandlesDatabases
{
    /**
     * @param Database $database
     * @return Sqlite|MySql|PostgreSql
     * @throws \Exception
     */
    protected function getDatabaseDumper(Database $database)
    {
        $dumper = $this->resolveDatabaseDumperClass($database->driver);

        $dumper->setDbName($database->name)
            ->setUserName($database->user)
            ->setHost($database->host)
            ->setPassword($database->password);

        if ($database->port) {
            $dumper->setPort($database->port);
        }

        return $dumper;
    }

    /**
     * Resolve database dumper class by driver
     *
     * @param string $driver
     * @return Sqlite|MySql|PostgreSql
     * @throws \Exception
     */
    protected function resolveDatabaseDumperClass(string $driver)
    {
        switch ($driver) {
            case 'mysql':
                $dumper = new MySql();

                return $dumper;
            case 'pgsql':
                $dumper = new PostgreSql();

                return $dumper;
            case 'sqlite':
                return new Sqlite();
            default:
                throw new \Exception('Unknown database driver');
        }
    }
}