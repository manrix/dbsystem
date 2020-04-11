<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TaskExecutionController extends Controller
{
    /**
     * Execute the task
     *
     * @param Request $request
     * @param $token
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function execute(Request $request, $token)
    {
        $task = Task::whereToken($token)->firstOrFail();

        Artisan::call('task:run', ['task' => $task->id]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Task successfully executed'
            ]);
        } else {
            return response()->make('Task successfully executed');
        }
    }
}
