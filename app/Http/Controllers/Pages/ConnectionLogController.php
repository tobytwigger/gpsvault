<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\ConnectionLog;
use App\Integrations\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConnectionLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Integration $integration)
    {
        return Inertia::render('Connection/Log', [
            'logs' => ConnectionLog::forIntegration($integration)->forCurrentUser()->get()
        ]);
    }

}
