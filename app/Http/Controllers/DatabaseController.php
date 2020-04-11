<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Database;
use DBSystem\Http\Requests\SaveDatabase;
use DBSystem\Traits\HasDataTable;
use Illuminate\Http\Request;

class DatabaseController extends Controller
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
        $query = Database::query();

        if ($request->query('driver')) {
            $query = $query->whereDriver($request->query('driver'));
        }

        $data = $this->getDataTable($query, $request, [
            'id','name','driver','host','user','port','updated_at'
        ]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveDatabase $request
     * @param Database $database
     * @return \Illuminate\Http\Response
     */
    public function store(SaveDatabase $request, Database $database)
    {
        $database->fill($request->all());
        $database->save();

        $message = "Database successfully saved";

        return response()->json(compact('database', 'message'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Database $database
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Database $database)
    {
        return response()->json($database);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Database $database
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Database $database)
    {
        $database->fill($request->all());
        $database->save();

        return response()->json([
            'message' => 'Database successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Database $database
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Database $database)
    {
        $database->delete();

        return response()->json([
            'message' => 'Database successfully removed'
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
            Database::destroy($id);
        });

        return response()->json([
            'message' => "Databases successfully deleted"
        ]);
    }

    /**
     * Check database connection
     *
     * @param Database $database
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConnection(Database $database)
    {
        $driver = $database->driver == 'postgresql' ? 'pgsql' : $database->driver;

        $tables = get_database_tables(
            $driver,
            $database->name,
            $database->host,
            $database->user,
            $database->password
        );

        return response()->json($tables);
    }

    /**
     * Get a simple list of databases
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDatabases()
    {
        $databases = Database::select(['id','name','driver','host'])->get();

        return response()->json($databases);
    }
}
