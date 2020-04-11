<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'task_log_id', 'level', 'message', 'context'
    ];

    protected $casts = [
        'context' => 'array',
    ];
}
