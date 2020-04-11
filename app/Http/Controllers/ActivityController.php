<?php

namespace DBSystem\Http\Controllers;

use Carbon\Carbon;
use DBSystem\Activity;
use DBSystem\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Activity::query()->with(['subject', 'causer'])->latest();

        if ($request->query('user')) {
            $query = $query->where('causer_type', User::class)
                ->where('causer_id', $request->query('user'));
        }

        $data = $query->paginate($request->query('perPage'))->toArray();

        return response()->json($data);
    }
}
