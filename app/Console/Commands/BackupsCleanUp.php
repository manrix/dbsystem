<?php

namespace DBSystem\Console\Commands;

use Carbon\Carbon;
use DBSystem\Backup;
use DBSystem\Traits\HandlesBackups;
use Illuminate\Console\Command;

class BackupsCleanUp extends Command
{
    use HandlesBackups;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:cleanup {--max_backups=} {--max_days=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old backups based on settings';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $query = Backup::orderBy('created_at', 'desc');

        $maxDays = $this->option('max_days') ?? settings('max_backups_days');
        if ($maxDays > 0) {
            $dateSubDays = Carbon::now()->subDays($maxDays);
            $query = $query->whereDate('created_at', '<', $dateSubDays);
        }

        $maxBackups = $this->option('max_backups') ?? settings('max_backups');
        if ($maxBackups > 0) {
            $query = $query->take($maxBackups)
                ->skip($maxBackups);
        }

        $backups = $query->with('destinations.destination')->get();

        $this->bulkDelete($backups->toArray());

        $this->info('Backups cleanup completed');
        $this->info('Total backups removed: ' . count($backups));
    }
}
