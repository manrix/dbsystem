<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class TaskStatistic extends Model
{
    protected $fillable = [
        'task_id', 'execution_time', 'memory_used', 'backup_size'
    ];
}
