<?php

namespace DBSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ScheduleController extends Controller
{
    public function run(Request $request)
    {
        if (!$request->query('key') || !$request->query('key') == settings('api_token')) {
            return response('', 403);
        }

        Artisan::call('schedule:run');

        return response(Artisan::output());
    }
}
