<?php

namespace DBSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validations = [
            'name' => 'required|string|max:255',
            'email' => [
                'required','string','email','max:255',
            ],
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($this->route('user') && $this->route('user')->id) {
            $validations['email'][] = Rule::unique('users')->ignore($this->route('user')->id);
        }

        return $validations;
    }
}
