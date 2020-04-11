<?php

namespace DBSystem\Http\Requests;

use DBSystem\Rules\FileExists;
use Illuminate\Foundation\Http\FormRequest;

class SaveTask extends FormRequest
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
            'status' => 'required|boolean',
            'file_name' => 'required|alpha_dash|max:150',
            'file_name_timestamp' => 'nullable|boolean',
            'description' => 'nullable|string',
            'databases' => 'required_without:file|array',
            'databases.*.tables' => 'nullable|array',
            'databases.*.database' => 'required|array',
            'file' => 'required_without:databases|array',
            'file.include' => 'required_without:databases|array',
            'file.include.*' => [new FileExists()],
            'file.exclude' => 'nullable|array',
            'destinations' => 'required_if:not_save_locally,true|array',
            'destinations.*.destination' => 'required|array',
            'destinations.*.path' => 'nullable|string|max:255',
            'not_save_locally' => 'nullable|boolean',
            'send_to_mail' => 'nullable|boolean',
            'email' => 'nullable|email|max:255',
            'compression' => 'in:zip,tar',
            'use_shell' => 'nullable|boolean',
        ];
    }
}
