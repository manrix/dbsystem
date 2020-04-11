<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Backup extends Model
{
    use LogsActivity;

    protected static $recordEvents = ['updated', 'deleted'];

    protected $fillable = [
        'name', 'size', 'task_id', 'saved_locally', 'type', 'user_id'
    ];

    protected $casts = [
        'saved_locally' => 'boolean',
    ];

    public function destinations()
    {
        return $this->hasMany(BackupDestination::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLocal($query)
    {
        return $query->where('saved_locally', 1);
    }
}
