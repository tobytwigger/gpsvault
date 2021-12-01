<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Integrations\Strava\Import\Models\Import;
use Inertia\Inertia;

class ImportController extends \Illuminate\Routing\Controller
{

    public function show(Import $import)
    {
        return Inertia::render('Import/Show', [
            'importData' => $import
        ]);
    }

}
