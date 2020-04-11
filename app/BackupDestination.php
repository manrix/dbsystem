<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class BackupDestination extends Model
{
    protected $fillable = [
        'backup_id', 'destination_id', 'path'
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
