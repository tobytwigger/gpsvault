<?php

namespace App\Integrations\Strava\Http\Controllers;

use Alchemy\Zippy\Archive\MemberInterface;
use Alchemy\Zippy\Zippy;
use App\Models\Activity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

        // In local
        $path = $request->file('file')->store('strava_fixes_archive');

        $zipFile = Zippy::load()->open(sprintf('%s/app/%s', storage_path(), $path));

        // Get the entries to extract for the activities
        $entries = collect($zipFile->getMembers())
            ->filter(fn(MemberInterface $member) => Str::startsWith($member->getLocation(), 'activities/') && Str::length($member->getLocation()) > 11);

        // Extract the entries
        $zipFileArchivePath = sprintf('%s/%s', $this->getStravaTempDir(), Str::random(10));
        mkdir($zipFileArchivePath, 0775);
        $zipFile->extractMembers($entries->all(), $zipFileArchivePath);

        // Upload each entry
        $successes = $entries->map(fn(MemberInterface $member) => Str::substr($member->getLocation(), 11))
            ->mapWithKeys(fn(string $entry) => [$entry => $this->processEntry($entry, $zipFileArchivePath)]);

        unlink(sprintf('%s/app/%s', storage_path(), $path));
        $this->delete($zipFileArchivePath);

        return redirect()->route('activity.create');
    }

    private function getStravaTempDir(): string
    {
        $directory = sprintf('%s/app/strava_fixes', storage_path());
        if(!is_dir($directory)) {
            mkdir($directory, 0775);
        }
        return $directory;
    }

    private function processEntry(string $entry, string $directory): string
    {
        try {
            $uploadId = Str::of($entry)->before('.');

            try {
                $activity = Activity::linkedTo('strava')->whereAdditionalDataContains('upload_id', $uploadId)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return static::UNMATCHED;
            }

            // If file is a tar, then unzip it and update the entry key
            $fileName = sprintf('%s/activities/%s', $directory, $entry);
            $fileName = $this->isTar($fileName) ? $this->unzip($fileName) : $fileName;
            $type = pathinfo($fileName, PATHINFO_EXTENSION);
            $cloudFileName = sprintf('activities/%s.%s', Str::random(40), $type);

            // Store in cloud
            if(!Storage::put($cloudFileName, trim(file_get_contents($fileName)))) {
                throw new \Exception('Could not save the file');
            }

            $activity->filepath = $cloudFileName;
            $activity->type = pathinfo($fileName, PATHINFO_EXTENSION);
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
        $bufferSize = 4096; // read 4kb at a time
        $outputFileName = str_replace('.gz', '', $fileName);

        // Open the file
        $file = gzopen($fileName, 'rb');
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

    private function delete(string $dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

}
