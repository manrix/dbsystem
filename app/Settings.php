<?php

namespace DBSystem;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Settings extends Model
{
    use LogsActivity;

    protected static $recordEvents = ['updated'];

    public $timestamps = false;

    protected $fillable = [
        'key', 'value'
    ];
}
