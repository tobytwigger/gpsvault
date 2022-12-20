<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Auth::user()->activities()
            ->orderByStat('started_at')
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
     * @param  \App\Models\Activity  $activity
     * @return Response
     */
    public function show(Activity $activity)
    {
        return $activity;
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
