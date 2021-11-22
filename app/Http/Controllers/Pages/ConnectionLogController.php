<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\ConnectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConnectionLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Connection/Log', [
            'logs' => ConnectionLog::where('user_id', Auth::id())->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConnectionLog  $connectionLog
     * @return \Illuminate\Http\Response
     */
    public function show(ConnectionLog $connectionLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConnectionLog  $connectionLog
     * @return \Illuminate\Http\Response
     */
    public function edit(ConnectionLog $connectionLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConnectionLog  $connectionLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConnectionLog $connectionLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConnectionLog  $connectionLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConnectionLog $connectionLog)
    {
        //
    }
}
