<?php

namespace App\Http\Controllers\Pages\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitySearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'sometimes|nullable|string|min:1|max:255',
        ]);

        if ($request->has('query') && $request->input('query')) {
            return Activity::search($request->input('query'))
                ->where('user_id', Auth::id())
                ->orderBy('updated_at', 'DESC')
                ->take(15)
                ->get();
        }

        return Activity::where('user_id', Auth::id())
            ->orderBy('updated_at', 'DESC')
            ->take(15)
            ->get();
    }
}
