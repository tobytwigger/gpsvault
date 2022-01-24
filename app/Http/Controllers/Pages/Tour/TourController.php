<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TourController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tour::class, 'tour');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Tour/Index', [
            'tours' => Tour::where('user_id', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->paginate($request->input('perPage', 8))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $tour = Tour::create([
            'name' => $request->input('name')
        ]);

        return redirect()->route('tour.show', $tour);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show(Tour $tour)
    {
        return Inertia::render('Tour/Show', [
            'tour' => $tour->load(['stages', 'stages.route'])->append(['stats', 'human_started_at', 'human_ended_at'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'notes' => 'sometimes|nullable|string|max:65535',
        ]);

        $tour->fill($validated);
        $tour->save();

        return redirect()->route('tour.show', $tour);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tour $tour)
    {
        $tour->delete();

        return redirect()->route('tour.index');
    }
}
