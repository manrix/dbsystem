<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Http\Requests\SaveTask;
use DBSystem\Task;
use DBSystem\TaskLog;
use DBSystem\Traits\HasDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    use HasDataTable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->query('user')) {
            $query = $query->where('user_id', $request->query('user'));
        }

        if (!is_null($request->query('status'))) {
            $query = $query->where('status', (int)$request->query('status'));
        }

        $data = $this->getDataTable($query, $request, [
            'id', 'name', 'token', 'status', 'executed_at', 'updated_at'
        ]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveTask $request
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function store(SaveTask $request, Task $task)
    {
        DB::transaction(function () use ($task, $request) {
            $task->fill($request->all());
            $task->save();

            $this->updateRelations($request, $task);
        });

        $message = "Task successfully saved";

        return response()->json(compact('task', 'message'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $task->load(['databases' => function ($query) {
            $query->with(['database' => function ($query) {
                $query->select(['id', 'name', 'driver', 'host']);
            }]);
        }, 'destinations' => function ($query) {
            $query->with(['destination' => function ($query) {
                $query->select(['id', 'name', 'driver']);
            }]);
        }, 'file']);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SaveTask $request
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(SaveTask $request, Task $task)
    {
        DB::transaction(function () use ($task, $request) {
            $task->fill($request->all());
            $task->save();

            $this->updateRelations($request, $task);
        });

        return response()->json([
            'message' => 'Task successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task successfully removed'
        ]);
    }

    /**
     * Delete multiple backups
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
        ]);

        collect($data['items'])->pluck('id')->each(function ($id) {
            Task::destroy($id);
        });

        return response()->json([
            'message' => "Tasks successfully deleted"
        ]);
    }

    public function overview($task_id)
    {
        $task = Task::with('statistics')->withCount(['backups', 'logs'])->findOrFail($task_id);

        return response()->json($task);
    }

    public function getLogs(Request $request, $task)
    {
        $query = TaskLog::where('task_id', $task)->with('logs');
        $data = $this->getDataTable($query, $request, ['*']);

        return response()->json($data);
    }

    public function getNewToken(Request $request, Task $task)
    {
        $token = generate_token(32);
        $task->token = $token;
        $task->save();

        return response()->json([
            'message' => "New Token generated",
            'token' => $token,
        ]);
    }

    protected function updateRelations(Request $request, Task $task)
    {
        $task->databases()->delete();
        $task->file()->delete();
        $task->destinations()->delete();

        foreach ($request->databases as $database) {
            $database['database_id'] = $database['database']['id'];
            $task->databases()->create($database);
        }

        if (isset($request->file['include']) && count($request->file['include'])) {
            $task->file()->create($request->file);
        }

        foreach ($request->destinations as $destination) {
            $destination['destination_id'] = $destination['destination']['id'];
            $task->destinations()->create($destination);
        }
    }
}
