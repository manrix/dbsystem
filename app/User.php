<?php

namespace DBSystem;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\HasActivity;

class User extends Authenticatable
{
    use Notifiable, HasActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

    public function filesBackups()
    {
        return $this->hasMany(Backup::class)->whereType('files');
    }

    public function databaseBackups()
    {
        return $this->hasMany(Backup::class)->whereType('database');
    }

    public function fullBackups()
    {
        return $this->hasMany(Backup::class)->whereType('full');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function activeTasks()
    {
        return $this->hasMany(Task::class)->whereStatus(1);
    }

    public function inactiveTasks()
    {
        return $this->hasMany(Task::class)->whereStatus(0);
    }
}
