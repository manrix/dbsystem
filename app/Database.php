<?php

namespace DBSystem;

use Spatie\Activitylog\Traits\LogsActivity;

class Database extends BaseModel
{
    use LogsActivity;

    protected $fillable = [
        'name', 'driver', 'host', 'port', 'user', 'password'
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = encrypt($value);
        } else {
            $this->attributes['password'] = '';
        }
    }

    public function getPasswordAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return $value;
    }
}
