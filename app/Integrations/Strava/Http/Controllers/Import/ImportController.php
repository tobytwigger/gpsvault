<?php

namespace App\Integrations\Strava\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Import\Upload\Models\StravaImport;
use App\Integrations\Strava\Jobs\ImportStravaExport;
use App\Services\Filepond\FilepondRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sopamo\LaravelFilepond\Filepond;

class ImportController extends Controller
{
    public function show(StravaImport $import)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'archive' => [app(FilepondRule::class)],
        ]);


        $newPath = Str::replace('filepond', 'strava_archives', app(Filepond::class)->getPathFromServerId(
            $request->input('archive')
        ));

        $fileStream = Storage::disk(config('filepond.temporary_files_disk'))->readStream(
            app(Filepond::class)->getPathFromServerId(
                $request->input('archive')
            )
        );
        Storage::disk('temp')->writeStream(
            $newPath,
            $fileStream
        );

        ImportStravaExport::dispatch(Auth::user(), $newPath);

        return redirect()->route('integration.strava');
    }
}
