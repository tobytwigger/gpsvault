<?php

namespace App\Http\Controllers\Pages\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function welcome()
    {
        return Inertia::render('Public/Welcome');
    }
}
