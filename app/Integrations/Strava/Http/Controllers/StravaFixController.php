<?php

namespace App\Integrations\Strava\Http\Controllers;

use Alchemy\Zippy\Archive\MemberInterface;
use Alchemy\Zippy\Zippy;
use App\Models\Activity;
use App\Models\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class StravaFixController extends Controller
{

    const SUCCESS = 'success';

    const UNMATCHED = 'unmatched';

    public function fix(Request $request)
    {
        $request->validate(['file' => 'file|mimes:zip']);

        // Save the zip file locally
        $archivePath = $request->file('file')->store('archives', 'temp');
        $zipFile = Zippy::load()->open(Storage::disk('temp')->path($archivePath));

        // Get the entries to extract for the activities
        $entries = collect($zipFile->getMembers())
            ->filter(fn(MemberInterface $member) => Str::startsWith($member->getLocation(), 'activities/') && Str::length($member->getLocation()) > 11);

        // Extract the zip file
        $newDirectoryPath = sprintf('extracted/%s', Str::random(10));
        Storage::disk('temp')->put($newDirectoryPath . '/cycle_store_test.txt', 'test');
        $extractTo = dirname(
            Storage::disk('temp')->path($newDirectoryPath . '/cycle_store_test.txt')
        );
        $zipFile->extractMembers($entries->all(), $extractTo);

        // Upload each entry
        $successes = $entries->map(fn(MemberInterface $member) => Str::substr($member->getLocation(), 11))
            ->mapWithKeys(fn(string $entry) => [$entry => $this->processEntry($entry, $newDirectoryPath)]);

        Storage::disk('temp')->deleteDirectory($newDirectoryPath);
        Storage::disk('temp')->delete($archivePath);

        return redirect()->route('sync.index');
    }

    private function processEntry(string $entry, string $directory): string
    {
        try {
            // Get the activity that this file relates to
            $uploadId = Str::of($entry)->before('.');
            try {
                $activity = Activity::linkedTo('strava')->whereAdditionalDataContains('upload_id', $uploadId)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return static::UNMATCHED;
            }

            // If file is a tar, then unzip it
            $locationInTemp = sprintf('%s/activities/%s', $directory, $entry);
            $locationInTemp = $this->isTar($locationInTemp) ? $this->unzip($locationInTemp) : $locationInTemp;
            $type = pathinfo(Storage::disk('temp')->path($locationInTemp), PATHINFO_EXTENSION);

            $extractedFileName = sprintf('activities/%s.%s', Str::random(40), $type);
            if(!Storage::disk(Auth::user()->disk())->put($extractedFileName, trim(file_get_contents($locationInTemp)))) {
                throw new \Exception('Could not save the file');
            }

            $file = File::create([
                'path' => $extractedFileName,
                'filename' => $this->isTar($entry) ? Str::replace('.gz', '', $entry) : $entry,
                'size' => Storage::size($extractedFileName),
                'mimetype' => Storage::mimeType($extractedFileName),
                'extension' => $type,
                'disk' => Auth::user()->disk()
            ]);

            $activity->activity_file_id = $file->id;
            $activity->save();

            return static::SUCCESS;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function isTar(string $fileName): bool
    {
        return Str::endsWith($fileName, '.gz');
    }

    private function unzip(string $fileName): string
    {
        $path = Storage::disk('temp')->path($fileName);

        $bufferSize = 4096; // read 4kb at a time
        $outputFileName = str_replace('.gz', '', $path);

        // Open the file
        $file = gzopen($path, 'rb');
        $outputFile = fopen($outputFileName, 'wb');

        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($outputFile, gzread($file, $bufferSize));
        }

        fclose($outputFile);
        gzclose($file);

        return $outputFileName;
    }

}
