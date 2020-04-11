<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    protected $fillable = [
        'include', 'exclude'
    ];

    protected $casts = [
        'include' => 'array',
        'exclude' => 'array',
    ];
}
