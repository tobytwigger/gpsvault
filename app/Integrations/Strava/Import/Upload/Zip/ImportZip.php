<?php

namespace App\Integrations\Strava\Import\Upload\Zip;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use PhpZip\ZipFile;

class ImportZip
{
    private string $extractedDirectory;

    private string $archivePath;

    private ZipFile $zip;

    public ZipContents $contents;

    public function __construct(string $path)
    {
        $this->archivePath = $path;
        $this->zip = new ZipFile();
        $this->zip->openFile($this->archivePath);
        $this->contents = $this->read();
    }

    public static function fromTempArchivePath(string $path): ImportZip
    {
        return new static(Storage::disk('temp')->path($path));
    }

    public function read(): ZipContents
    {
        return new ZipContents(
            $this->zip->getListFiles()
        );
    }

//    public function extractAll(): ZipContents
//    {
//        $this->extractedDirectory = sprintf('extracted/%s', Str::random(10));
//
//        // Ensure the new folder exists so we can extract to it.
//        Storage::disk('temp')->put($this->extractedDirectory . '/cycle_store_test.txt', 'test');
//
//        // Get the absolute path to this directory
//        $extractTo = dirname(
//            Storage::disk('temp')->path($this->extractedDirectory . '/cycle_store_test.txt')
//        );
//        $this->zip->extractTo($extractTo);
//
//        return new ZipContents($extractTo);
//    }

    public function extract(\App\Integrations\Strava\Import\Upload\Zip\ZipFile $file): string
    {
        return $this->zip->getEntryContents((string) $file);
    }

//    /**
//     * @return \Illuminate\Support\Collection|MemberInterface[]
//     */
//    public function getMembers(): \Illuminate\Support\Collection
//    {
//        return collect($this->zip->getMembers());
//    }
//
//    public function getFullExtractedDirectory(?string $append = null): string
//    {
//        return sprintf(
//            '%s%s%s',
//            Storage::disk('temp')->path($this->extractedDirectory),
//            ($append && !Str::startsWith($append, DIRECTORY_SEPARATOR)) ? '/' : '',
//            $append ?? ''
//        );
//    }
//
//    public function getExtractedDirectory(?string $append = null): string
//    {
//        return $this->extractedDirectory . Str::startsWith($append, DIRECTORY_SEPARATOR) ? $append : '/' . $append;
//    }
//
//    public function getCsv(string $path): array
//    {
//        $member = collect($this->zip->getMembers())
//            ->filter(fn (MemberInterface $member) => $member->getLocation() === $path)
//            ->first();
//
//        if (!$member) {
//            throw new \Exception('Photos csv not found');
//        }
//
//        $data = [];
//        $lines = explode(PHP_EOL, file_get_contents($this->getFullExtractedDirectory($member->getLocation())));
//
//        array_shift($lines);
//
//        foreach ($lines as $line) {
//            $parsedLine = str_getcsv($line);
//            if (count($parsedLine) === 2) {
//                $data[$parsedLine[0]] = $parsedLine[1];
//            }
//        }
//
//        return $data;
//    }

    public function getCsv(string $filename): array
    {
        $csvReader = Reader::createFromString($this->extract(new \App\Integrations\Strava\Import\Upload\Zip\ZipFile($filename)));

        $csv = $csvReader->jsonSerialize();
        array_shift($csv);

        return $csv;
    }
}
