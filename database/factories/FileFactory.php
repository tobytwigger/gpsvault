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
            'disk' => 'test-fake',
            'hash' => Str::random(32),
            'user_id' => fn () => User::factory(),
            'thumbnail_id' => null,
        ];
    }

    public function withThumbnail()
    {
        return $this->state(fn (array $attributes) => [
            'thumbnail_id' => File::factory()->thumbnail(),
        ]);
    }

    public function withoutThumbnail()
    {
        return $this->state(fn (array $attributes) => [
            'thumbnail_id' => null,
        ]);
    }

    public function thumbnail()
    {
        $path = 'map_thumbnails/' . Str::random(40) . '.jpeg';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/images/image1.jpeg')));

        return $this->withoutThumbnail()->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::IMAGE_THUMBNAIL,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function image()
    {
        $path = 'map_thumbnails/' . Str::random(40) . '.jpeg';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/images/image1.jpeg')));

        return $this->withThumbnail()->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::MAP_THUMBNAIL,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function routeMedia()
    {
        $path = 'routeMedia/' . Str::random(40) . '.jpeg';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/images/image1.jpeg')));

        return $this->withThumbnail()->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::ROUTE_MEDIA,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function activityMedia()
    {
        $path = 'activityMedia/' . Str::random(40) . '.jpeg';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/images/image2.jpeg')));

        return $this->withThumbnail()->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.jpeg',
            'extension' => 'jpeg',
            'type' => FileUploader::ACTIVITY_MEDIA,
            'mimetype' => 'image/jpeg',
        ]);
    }

    public function activityFile()
    {
        $path = 'activityFile/' . Str::random(40) . '.gpx';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/gpx/Afternoon_Ride.gpx')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function simpleGpx()
    {
        $path = 'gpx_' . Str::uuid() . '.gpx';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/parsing/gpx.gpx')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'disk' => 'test-fake',
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/octet-stream',
        ]);
    }

    public function simpleFit()
    {
        $path = 'fit_' . Str::uuid() . '.fit';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/parsing/fit.fit')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.fit',
            'disk' => 'test-fake',
            'extension' => 'fit',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function simpleTcx()
    {
        $path = 'fit_' . Str::uuid() . '.tcx';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/parsing/tcx.tcx')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.tcx',
            'disk' => 'test-fake',
            'extension' => 'tcx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/xml',
        ]);
    }

    public function dartmoorDevilGpx()
    {
        $path = 'dartmoorDevil/' . Str::random(40) . '.gpx';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/DartmoorDevil/Dartmoor_Devil.gpx')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function dartmoorDevilFit()
    {
        $path = 'dartmoorDevil/' . Str::random(40) . '.fit';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/DartmoorDevil/Dartmoor_Devil.fit')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.fit',
            'extension' => 'fit',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function routeFile()
    {
        $path = 'routeFile/' . Str::random(40) . '.gpx';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/gpx/Afternoon_Ride.gpx')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.gpx',
            'extension' => 'gpx',
            'type' => FileUploader::ROUTE_FILE,
            'mimetype' => 'application/xml+gpx',
        ]);
    }

    public function archive()
    {
        $path = 'archive/' . Str::random(40) . '.zip';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/zip/archive_created_2022_01_15_18_01_16.zip')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.zip',
            'extension' => 'zip',
            'type' => FileUploader::ARCHIVE,
            'mimetype' => 'application/zip',
        ]);
    }

    public function activityPoints()
    {
        $path = 'activityPoints/' . Str::random(40) . '.tar.gz';
        Storage::disk('test-fake')->put($path, file_get_contents(base_path('tests/assets/points/points.tar.gz')));

        return $this->state(fn (array $attributes) => [
            'path' => $path,
            'filename' => $this->faker->word . '.tar.gz',
            'extension' => 'tar.gz',
            'type' => FileUploader::ACTIVITY_FILE_POINT_JSON,
            'mimetype' => 'application/gzip',
        ]);
    }
}
