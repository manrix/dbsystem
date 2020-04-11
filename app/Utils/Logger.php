<?php

namespace DBSystem\Tasks;

use DBSystem\TaskLog;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    /** @var Model */
    private $model;

    /** @var TaskLog */
    private $taskLog;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $task_log_id = optional($this->taskLog)->id;

        $this->model->create(compact('task_log_id', 'level', 'message', 'context'));
    }

    /**
     * @param TaskLog $taskLog
     */
    public function setTaskLog(TaskLog $taskLog)
    {
        $this->taskLog = $taskLog;
    }
}