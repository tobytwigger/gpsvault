<?php

namespace App\Integrations\Strava\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Import\Upload\Importer;
use App\Integrations\Strava\Import\Upload\Importers\ImportZip;
use App\Integrations\Strava\Jobs\ImportStravaExport;
use App\Services\File\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{

    public function index()
    {
        dd('Imported');
    }

    public function store(Request $request)
    {
        $request->validate([
            'archive' => 'file'
        ]);

        $path = $request->file('archive')->store('strava_archives', 'temp');

        ImportStravaExport::dispatch(Auth::user(), $path);

        return redirect()->route('strava.import.index');
    }

}
