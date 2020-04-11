<?php

namespace DBSystem;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    protected $appends = [
        'normalized_subject', 'normalized_causer'
    ];

    public function getNormalizedSubjectAttribute()
    {
        return strtolower(array_last(explode('\\', $this->attributes['subject_type'])));
    }

    public function getNormalizedCauserAttribute()
    {
        return strtolower(array_last(explode('\\', $this->attributes['causer_type'])));
    }
}