<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class TaskDatabase extends Model
{
    protected $fillable = [
        'task_id', 'database_id', 'tables',
        'skip_comments', 'use_extended_inserts',
        'use_single_transaction', 'use_inserts', 'use_compression'
    ];

    protected $casts = [
        'tables' => 'array',
        'skip_comments' => 'boolean',
        'use_extended_inserts' => 'boolean',
        'use_single_transaction' => 'boolean',
        'use_inserts' => 'boolean',
        'use_compression' => 'boolean',
    ];

    public function database()
    {
        return $this->belongsTo(Database::class);
    }
}
