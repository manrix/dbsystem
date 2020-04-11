<?php

namespace DBSystem\Http\Controllers;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (config('app.env') === 'production') {
            $new_version = check_new_version();
        }

        return view('backend/main')->with(compact('new_version'));
    }
}
