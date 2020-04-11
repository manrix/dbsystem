<?php

namespace DBSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveDatabase extends FormRequest
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
        return [
            'name' => 'required|string|max:150',
            'host' => 'string|max:150',
            'port' => 'nullable|numeric',
            'driver' => 'required|alpha',
            'user' => 'required|alpha_dash|max:255',
            'password' => 'nullable|max:255',
        ];
    }
}
