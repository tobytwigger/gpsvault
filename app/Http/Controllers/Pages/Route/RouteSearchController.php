<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'sometimes|nullable|string|min:1|max:255',
        ]);

        if ($request->has('query') && $request->input('query')) {
            return Route::search($request->input('query'))
                ->where('user_id', Auth::id())
                ->orderBy('updated_at', 'DESC')
                ->paginate(request()->input('perPage', 15));
        }

        return Route::where('user_id', Auth::id())
            ->orderBy('updated_at', 'DESC')
            ->paginate(request()->input('perPage', 15));
    }
}
