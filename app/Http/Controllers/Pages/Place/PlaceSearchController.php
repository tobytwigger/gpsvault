<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;

class PlaceSearchController extends Controller
{

    public function search()
    {
        return Place::paginate(request()->input('perPage', 8));
    }

}
