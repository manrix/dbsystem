<?php

namespace DBSystem;

use Spatie\Activitylog\Traits\LogsActivity;

class Destination extends BaseModel
{
    use LogsActivity;

    protected $fillable = [
        'driver', 'name', 'host', 'user',
        'password', 'port', 'root',
        'token', 'client_id', 'client_secret',
        'refresh_token'
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

    public function setTokenAttribute($value)
    {
        if ($value) {
            $this->attributes['token'] = encrypt($value);
        } else {
            $this->attributes['token'] = '';
        }
    }

    public function getTokenAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return $value;
    }

    public function setClientSecretAttribute($value)
    {
        if ($value) {
            $this->attributes['client_secret'] = encrypt($value);
        } else {
            $this->attributes['client_secret'] = '';
        }
    }

    public function getClientSecretAttribute($value)
    {
        if ($value) {
            return decrypt($value);
        }

        return $value;
    }
}
