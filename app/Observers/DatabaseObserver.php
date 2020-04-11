<?php

namespace DBSystem\Observers;

use DBSystem\Database;
use DBSystem\TaskDatabase;

class DatabaseObserver
{
    /**
     * Delete relations
     *
     * @param Database $database
     */
    public function deleted(Database $database)
    {
        TaskDatabase::where('database_id', $database->id)->delete();
    }
}