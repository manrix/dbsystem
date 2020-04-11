<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(settings());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request)
    {
        $request->validate([
            'max_backups' => 'integer',
            'max_backups_days' => 'integer',
            'logs_days' => 'integer',
            'max_execution_time' => 'integer',
            'memory_limit' => 'integer',
            'task_notification' => 'required|boolean',
            'api_token' => 'nullable|string|min:8|max:255|regex:/^\S*$/u',
        ]);

        $new_data = [
            'max_backups' => $request->max_backups ?? 0,
            'max_backups_days' => $request->max_backups_days ?? 0,
            'logs_days' => $request->logs_days ?? 0,
            'max_execution_time' => $request->max_execution_time ?? 0,
            'memory_limit' => $request->memory_limit ?? 0,
            'task_notification' => $request->task_notification ?? true,
            'api_token' => $request->api_token ?? '',
        ];

        foreach ($new_data as $key => $value) {
            Settings::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $value
                ]);
        }

        cache()->forget('app.settings');

        return response()->json([
            'message' => "Settings successfully updated"
        ]);
    }
}
