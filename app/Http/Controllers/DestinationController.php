<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Destination;
use DBSystem\Http\Requests\SaveDestination;
use DBSystem\Traits\HasDataTable;
use Illuminate\Http\Request;

class DestinationController extends Controller
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
        $query = Destination::query();

        if ($request->query('driver')) {
            $query = $query->whereDriver($request->query('driver'));
        }

        $data = $this->getDataTable($query, $request, [
            'id','driver','name','host','user','root','updated_at'
        ]);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SaveDestination $request
     * @param Destination $destination
     * @return \Illuminate\Http\Response
     */
    public function store(SaveDestination $request, Destination $destination)
    {
        $destination->fill($request->all());
        $destination->save();

        $message = "Destination successfully saved";

        return response()->json(compact('destination', 'message'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Destination $destination
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Destination $destination)
    {
        return response()->json($destination);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Destination $destination
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Destination $destination)
    {
        $destination->fill($request->all());
        $destination->save();

        return response()->json([
            'message' => 'Destination successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Destination $destination
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();

        return response()->json([
            'message' => 'Destination successfully removed'
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
            Destination::destroy($id);
        });

        return response()->json([
            'message' => "Destinations successfully deleted"
        ]);
    }

    /**
     * Get a simple list of destinations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDestinations()
    {
        $databases = Destination::select(['id','name','driver','root'])->get();

        return response()->json($databases);
    }
}
