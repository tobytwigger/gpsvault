<?php

namespace App\Integrations\Strava\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Import\Upload\Models\StravaImport;
use App\Integrations\Strava\Jobs\ImportStravaExport;
use App\Services\Filepond\FilePondFile;
use App\Services\Filepond\FilePondRepository;
use App\Services\Filepond\FilePondResolver;
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

        $file = FilePondFile::fromArray($request->input('archive'));

        Storage::disk('temp')->writeStream(
            'strava_archives/' . $file->tempFileNameWithoutPath(),
            $file->readAsStream()
        );

        ImportStravaExport::dispatch(Auth::user(), 'strava_archives/' . $file->tempFileNameWithoutPath());

        return redirect()->route('integration.strava');
    }
}
