<?php

namespace DBSystem\Console\Commands;

use Carbon\Carbon;
use DBSystem\Backup;
use DBSystem\Dumpers\DbDumper;
use DBSystem\Dumpers\Files;
use DBSystem\Events\TaskCompleted;
use DBSystem\Mail\BackupTransfer;
use DBSystem\Task;
use DBSystem\TaskDatabase;
use DBSystem\TaskFile;
use DBSystem\TaskLog;
use DBSystem\Tasks\Backup as BackupTask;
use DBSystem\Tasks\Logger;
use DBSystem\TaskStatistic;
use DBSystem\Traits\HandlesDatabases;
use DBSystem\Traits\HandlesDestinations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Illuminate\Console\Command;

class ExecuteTask extends Command
{
    use HandlesDestinations, HandlesDatabases;

    private $logger;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:run {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the specified task';

    public function __construct(Logger $logger)
    {
        parent::__construct();

        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $task = Task::findOrFail($this->argument('task'));

        if (!$task->status && auth()->guest()) {
            throw new \Exception("Task is disabled or you don't have the rights to execute it");
        }

        $task->disableLogging();

        $task->load('databases.database', 'destinations.destination', 'file');

        $time = -microtime(true);

        // init backup task
        $backup = new BackupTask();

        // set the backup name
        $backupName = $task->file_name;
        $backupName .= $task->file_name_timestamp ? '_' . Carbon::now()->format('Y-m-d_H-i-s') : '';
        $backup->setBackupName($backupName);

        // set compression
        $backup->setUseZip($task->compression === 'zip')
            ->useShell($task->use_shell);

        // create logger entry for this execution
        $taskLog = TaskLog::create([
            'uid' => $backup->getUid(),
            'task_id' => $task->id,
        ]);

        $this->logger->setTaskLog($taskLog);
        $backup->setDestinationPath(Storage::disk('backups')->path(''))
            ->setLogger($this->logger);

        foreach ($task->databases as $database) {
            $dumper = $this->getDatabaseDumper($database->database);
            $dumper = $this->applyDumberTaskOptions($database->database->driver, $dumper, $database);
            $backup->addDatabase($dumper);
        }

        $hasFiles = false;
        if ($task->file && count($task->file->include)) {
            $hasFiles = true;
            $backup->addFiles($this->getFilesDumper($task->file));
        }

        foreach ($task->destinations as $taskDestination) {
            $backup->addDestination($this->resolveDestinationClass($taskDestination->destination));
        }

        $file = $backup->execute();

        // register the activity
        if (auth()->user()) {
            activity()
                ->by(auth()->user())
                ->on($task)
                ->log('executed');
        } else {
            activity()
                ->on($task)
                ->log('executed');
        }

        // collect data for statistics
        $memory = memory_get_usage(true);
        $time += microtime(true);

        DB::beginTransaction();

        $type = '';
        if (count($task->databases) && $hasFiles) {
            $type = 'full';
        } elseif (count($task->databases)) {
            $type = 'database';
        } elseif (count($task->file->include)) {
            $type = 'files';
        }

        // store backup info into database
        $backupModel = Backup::create([
            'user_id' => $task->user_id,
            'name' => $file['name'],
            'type' => $type,
            'size' => $file['size'],
            'task_id' => $task->id,
        ]);

        // associate destination with the new backup model
        foreach ($task->destinations as $taskDestination) {
            $backupModel->destinations()->create([
                'destination_id' => $taskDestination->destination_id,
                'path' => $taskDestination->path,
            ]);
        }

        // save statistics for this execution
        TaskStatistic::create([
            'task_id' => $task->id,
            'execution_time' => $time,
            'memory_used' => $memory,
            'backup_size' => $file['size']
        ]);

        $task->executed_at = Carbon::now();
        $task->save();

        DB::commit();

        event(new TaskCompleted($task));

        if ($task->send_to_email) {
            Mail::to($task->email)->send(new BackupTransfer($backupModel));
        }

        $this->info('Task execution completed');
    }

    /**
     * @param TaskFile $taskFile
     * @return Files
     */
    protected function getFilesDumper(TaskFile $taskFile)
    {
        $dumper = new Files();
        $dumper->addFiles($taskFile->include)
            ->addExcludedFiles($taskFile->exclude);

        return $dumper;
    }

    /**
     * @param $driver
     * @param DbDumper $dumper
     * @param TaskDatabase $taskDatabase
     * @return DbDumper
     * @throws \Exception
     */
    protected function applyDumberTaskOptions($driver, DbDumper $dumper, TaskDatabase $taskDatabase)
    {
        if ($taskDatabase->use_compression) {
            $dumper->useCompressor(new GzipCompressor());
        }

        switch ($driver) {
            case 'mysql':
                if ($taskDatabase->skip_comments) {
                    $dumper->skipComments();
                } else {
                    $dumper->dontSkipComments();
                }

                if ($taskDatabase->use_extended_inserts) {
                    $dumper->useExtendedInserts();
                } else {
                    $dumper->dontUseExtendedInserts();
                }

                if ($taskDatabase->use_single_transaction) {
                    $dumper->useSingleTransaction();
                } else {
                    $dumper->dontUseSingleTransaction();
                }

                return $dumper;
            case 'pgsql':
                if ($taskDatabase->use_inserts) {
                    $dumper->useInserts();
                }

                return $dumper;
            default:
                return $dumper;
        }
    }
}
