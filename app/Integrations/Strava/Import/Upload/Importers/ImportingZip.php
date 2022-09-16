<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use Alchemy\Zippy\Archive\MemberInterface;
use Alchemy\Zippy\Zippy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportingZip
{
    private string $extractedDirectory;

    private string $archivePath;

    private \Alchemy\Zippy\Archive\ArchiveInterface $zip;

    public function __construct(string $path)
    {
        $this->archivePath = $path;
        $this->zip = Zippy::load()->open($this->archivePath);
        $this->extract();
    }

    public static function fromTempArchivePath(string $path): ImportingZip
    {
        return new static(Storage::disk('temp')->path($path));
    }

    protected function extract(): void
    {
        $this->extractedDirectory = sprintf('extracted/%s', Str::random(10));

        // Ensure the new folder exists so we can extract to it.
        Storage::disk('temp')->put($this->extractedDirectory . '/cycle_store_test.txt', 'test');

        // Get the absolute path to this directory
        $extractTo = dirname(
            Storage::disk('temp')->path($this->extractedDirectory . '/cycle_store_test.txt')
        );
        $this->zip->extract($extractTo);
    }

    public function clearUp(): void
    {
        Storage::disk('temp')->deleteDirectory($this->extractedDirectory);
    }

    /**
     * @return \Illuminate\Support\Collection|MemberInterface[]
     */
    public function getMembers(): \Illuminate\Support\Collection
    {
        return collect($this->zip->getMembers());
    }

    public function getFullExtractedDirectory(?string $append = null): string
    {
        return sprintf(
            '%s%s%s',
            Storage::disk('temp')->path($this->extractedDirectory),
            ($append && !Str::startsWith($append, DIRECTORY_SEPARATOR)) ? '/' : '',
            $append ?? ''
        );
    }

    public function getExtractedDirectory(?string $append = null): string
    {
        return $this->extractedDirectory . Str::startsWith($append, DIRECTORY_SEPARATOR) ? $append : '/' . $append;
    }

    public function getCsv(string $path): array
    {
        $member = collect($this->zip->getMembers())
            ->filter(fn (MemberInterface $member) => $member->getLocation() === $path)
            ->first();

        if (!$member) {
            throw new \Exception('Photos csv not found');
        }

        $data = [];
        $lines = explode(PHP_EOL, file_get_contents($this->getFullExtractedDirectory($member->getLocation())));

        array_shift($lines);

        foreach ($lines as $line) {
            $parsedLine = str_getcsv($line);
            if (count($parsedLine) === 2) {
                $data[$parsedLine[0]] = $parsedLine[1];
            }
        }

        return $data;
    }
}
