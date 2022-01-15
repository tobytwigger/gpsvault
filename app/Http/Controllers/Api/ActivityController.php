<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'sometimes|nullable|string|min:1|max:255'
        ]);

        return Activity::where('user_id', Auth::id())
            ->when($request->has('query') && $request->input('query'), function(Builder $query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('query') . '%')->get();
            })
            ->orderBy('updated_at')
            ->limit(15)
            ->get();
    }

}
