<?php

namespace DBSystem\Console\Commands;

use Carbon\Carbon;
use DBSystem\TaskLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ApplicationCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbsystem:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean task logs and unused directories';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // clean task logs
        $todaySubDays = Carbon::now()->subDays(settings('logs_days'))->toDateTimeString();
        $logs = TaskLog::orderBy('created_at', 'desc')
            ->where('created_at', '<', $todaySubDays)
            ->get();

        foreach ($logs as $log) {
            $log->delete();
        }

        // clean dbsystem unused folders
        $directories = Storage::disk('dbsystem')->directories();
        $time = Carbon::now()->subDays(1)->format('U');
        foreach ($directories as $directory) {
            if (filemtime(Storage::disk('dbsystem')->path($directory)) >= $time) {
                Storage::disk('dbsystem')->deleteDirectory($directory);
            }
        }

        $this->info('Cleanup completed');
        $this->info('Total logs removed: ' . count($logs));
        $this->info('Total directories removed: ' . count($directories));
    }
}
