<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Integrations\Strava\Import\Models\Import;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ImportController extends \Illuminate\Routing\Controller
{

    public function show(Import $import)
    {
        abort_if($import->user_id !== Auth::id(), 403, 'You can only view your own imports.');

        return Inertia::render('Import/Show', [
            'importData' => $import
        ]);
    }

}
