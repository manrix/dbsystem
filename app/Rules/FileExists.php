<?php

namespace DBSystem\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileExists implements Rule
{
    protected $value;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;

        return file_exists(base_path($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.file_exists', [
            'value' => $this->value
        ]);
    }
}
