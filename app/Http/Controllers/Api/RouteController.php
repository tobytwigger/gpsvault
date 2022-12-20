<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Route::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Auth::user()->routes()
            ->orderBy('updated_at', 'DESC')
            ->with('mainPath')
            ->paginate(request()->input('perPage', 8));
    }

//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return \Illuminate\Http\Response
//     */
//    public function store(Request $request)
//    {
//    }
//
    /**
     * Display the specified resource.
     *
     * @param Route $route
     * @return Response
     */
    public function show(Route $route)
    {
        return $route;
    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \App\Models\Activity  $activity
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, ActivityController $activity)
//    {
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  \App\Models\Activity  $activity
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(ActivityController $activity)
//    {
//    }
}
