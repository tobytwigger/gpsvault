<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Place::orderBy('name')->paginate(request()->input('perPage', 8));
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
//    /**
//     * Display the specified resource.
//     *
//     * @param  \App\Models\Activity  $activity
//     * @return \Illuminate\Http\Response
//     */
//    public function show(ActivityController $activity)
//    {
//    }
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
