<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    protected $fillable = [
        'uid', 'task_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function($model) {
            $model->logs()->delete();
        });
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
