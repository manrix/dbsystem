<?php

namespace DBSystem;

use Spatie\Activitylog\Traits\LogsActivity;

class Task extends BaseModel
{
    use LogsActivity;

    protected $fillable = [
        'name', 'status', 'file_name',
        'file_name_timestamp', 'description',
        'not_save_locally', 'send_to_email', 'email',
        'compression', 'use_shell'
    ];

    protected $casts = [
        'status' => 'boolean',
        'file_name_timestamp' => 'boolean',
        'not_save_locally' => 'boolean',
        'send_to_email' => 'boolean',
        'use_shell' => 'boolean',
    ];

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

    public function databases()
    {
        return $this->hasMany(TaskDatabase::class);
    }

    public function destinations()
    {
        return $this->hasMany(TaskDestination::class);
    }

    public function file()
    {
        return $this->hasOne(TaskFile::class);
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

    public function statistics()
    {
        return $this->hasMany(TaskStatistic::class);
    }
}
