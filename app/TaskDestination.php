<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class TaskDestination extends Model
{
    protected $fillable = [
        'task_id', 'destination_id', 'path',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
