<?php

namespace DBSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveDestination extends FormRequest
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
            'driver' => 'required|alpha_num',
            'name' => 'required|string|max:150',
            'user' => 'nullable|required_if:driver,ftp|string|max:255',
            'host' => 'nullable|required_if:driver,ftp|string|max:255',
            'password' => 'nullable|required_if:driver,ftp|max:255',
            'port' => 'nullable|numeric',
            'token' => 'nullable|required_if:driver,dropbox|string|max:255',
            'client_id' => 'nullable|required_if:driver,g3|string|max:255',
            'client_secret' => 'nullable|required_if:driver,g3|string|max:255',
            'refresh_token' => 'nullable|required_if:driver,g3|string|max:255',
            'root' => 'nullable|string|max:255',
        ];
    }
}
