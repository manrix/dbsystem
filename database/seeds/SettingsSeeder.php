<?php

use DBSystem\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'max_backups' => 50,
            'max_backups_days' => 0,
            'logs_days' => 60,
            'max_execution_time' => 120,
            'memory_limit' => 0,
            'task_notification' => true,
        ];

        foreach ($data as $key => $value) {
            Settings::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $value
                ]);
        }

        Cache::forget('app.settings');
    }
}
