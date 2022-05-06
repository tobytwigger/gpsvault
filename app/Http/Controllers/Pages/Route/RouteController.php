<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Models\Route;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RouteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Route::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Route/Index', [
            'routes' => Auth::user()->routes()
                ->orderBy('updated_at', 'DESC')
                ->with('mainPath')
                ->paginate(request()->input('perPage', 8)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRouteRequest $request
     * @return Response
     */
    public function store(StoreRouteRequest $request)
    {
        $fileId = null;
        if ($request->has('file')) {
            $fileId = Upload::uploadedFile($request->file('file'), Auth::user(), FileUploader::ROUTE_FILE)->id;
        }

        $route = Route::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'file_id' => $fileId,
        ]);

        if ($request->has('file') && $request->file('file') !== null) {
//            $route->analyse();
        }

        return redirect()->route('route.show', $route);
    }

    /**
     * Display the specified resource.\.
     *
     * @param Route $route
     * @return \Inertia\Response
     */
    public function show(Route $route)
    {
        return Inertia::render('Route/Show', [
            'routeModel' => $route->load(['files'])->append('path'),
            'places' => $route->places()->paginate(request()->input('perPage', 8)),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Route $route
     * @return RedirectResponse
     */
    public function update(Request $request, Route $route)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'notes' => 'sometimes|nullable|string|max:65535',
            'file' => ['sometimes', 'nullable', 'file'],
        ]);

        $fileId = $route->file_id;
        if ($request->has('file') && $request->file('file') !== null) {
            $fileId = Upload::uploadedFile($request->file('file'), Auth::user(), FileUploader::ROUTE_FILE)->id;
        }

        $route->name = $request->input('name', $route->name);
        $route->description = $request->input('description', $route->description);
        $route->notes = $request->input('notes', $route->notes);
//        $route->file_id = $fileId;
        $route->save();

        if ($request->has('file') && $request->file('file') !== null) {
//            $route->analyse();
        }

        return redirect()->route('route.show', $route);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Route $route
     * @return Response
     */
    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('route.index');
    }
}
