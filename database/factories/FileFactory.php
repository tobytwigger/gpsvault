<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'size' => $this->faker->numberBetween(20, 100000),
            'title' => $this->faker->words(3, true),
            'caption' => $this->faker->paragraph,
            'disk' => 'tests',
            'hash' => Str::random(32),
            'user_id' => fn () => User::factory(),
        ];
    }

    public function routeMedia()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/images'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::ROUTE_MEDIA,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function activityMedia()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/images'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::ACTIVITY_MEDIA,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function activityFile()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/gpx'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function simpleGpx()
    {
        $path = 'gpx_' . Str::uuid() . '.gpx';
        Storage::disk('tests')->put($path, file_get_contents(base_path('tests/assets/parsing/gpx.gpx')));
        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/octet-stream',
        ]);
    }

    public function simpleFit()
    {
        $path = 'fit_' . Str::uuid() . '.fit';
        Storage::disk('tests')->put($path, file_get_contents(base_path('tests/assets/parsing/fit.fit')));
        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.fit',
            'extension' => 'fit',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function simpleTcx()
    {
        $path = 'fit_' . Str::uuid() . '.fit';
        Storage::disk('tests')->put($path, file_get_contents(base_path('tests/assets/parsing/tcx.tcx')));
        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.tcx',
            'extension' => 'tcx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function dartmoorDevilGpx()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/DartmoorDevil'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function dartmoorDevilFit()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/DartmoorDevilFit'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.fit',
            'extension' => 'fit',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function routeFile()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/gpx'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ROUTE_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function archive()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/zip'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.zip',
            'extension' => 'zip',
            'type' => FileUploader::ARCHIVE,
            'mimetype' => 'application/zip',
        ]);
    }

    public function activityPoints()
    {
        return $this->state(fn (array $attributes) => [
            'path' => Str::after('/tests/' . $this->faker->file(base_path('tests/assets/points'), storage_path('tests'), false), 'tests/'),
            'filename' => $this->faker->word . '.tar.gz',
            'extension' => 'tar.gz',
            'type' => FileUploader::ACTIVITY_FILE_POINT_JSON,
            'mimetype' => 'application/gzip',
        ]);
    }
}
