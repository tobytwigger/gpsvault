<?php

namespace Tests\Unit\Console;

use App\Jobs\CreateThumbnailImage;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CreateThumbnailsForImagesTest extends TestCase
{

    /** @test */
    public function it_calls_the_thumbnail_job_correctly(){
        Bus::fake(CreateThumbnailImage::class);

        $imagesToGenerate = Model::withoutEvents(fn() => File::factory()->image()->count(5)->create(['thumbnail_id' => null]));
        Model::withoutEvents(fn() => File::factory()->image()->withThumbnail()->count(6)->create());
        Model::withoutEvents(fn() => File::factory()->simpleGpx()->count(7)->create());
        Model::withoutEvents(fn() => File::factory()->image()->count(8)->create(['thumbnail_id' => null, 'type' => FileUploader::IMAGE_THUMBNAIL]));

        $this->artisan('thumbnail:generate')
            ->expectsOutput('Generating 5 thumbnails.')
            ->assertSuccessful();

        Bus::assertDispatchedTimes(CreateThumbnailImage::class, 5);

        foreach($imagesToGenerate as $image) {
            Bus::assertDispatched(CreateThumbnailImage::class, function(CreateThumbnailImage $job) use ($image) {
                return $job->file->is($image);
            });
        }
    }

    /** @test */
    public function existing_thumbnails_can_be_force_regenerated(){
        Bus::fake(CreateThumbnailImage::class);

        $imagesToGenerate = Model::withoutEvents(fn() => File::factory()->image()->count(5)->create(['thumbnail_id' => null]));
        $imagesToGenerate2 = Model::withoutEvents(fn() => File::factory()->image()->withThumbnail()->count(6)->create());
        Model::withoutEvents(fn() => File::factory()->simpleGpx()->count(7)->create());
        Model::withoutEvents(fn() => File::factory()->image()->count(8)->create(['thumbnail_id' => null, 'type' => FileUploader::IMAGE_THUMBNAIL]));

        $this->artisan('thumbnail:generate --regenerate')
            ->expectsOutput('Generating 11 thumbnails.')
            ->assertSuccessful();

        Bus::assertDispatchedTimes(CreateThumbnailImage::class, 11);

        foreach($imagesToGenerate as $image) {
            Bus::assertDispatched(CreateThumbnailImage::class, function(CreateThumbnailImage $job) use ($image) {
                return $job->file->is($image);
            });
        }
        foreach($imagesToGenerate2 as $image) {
            Bus::assertDispatched(CreateThumbnailImage::class, function(CreateThumbnailImage $job) use ($image) {
                return $job->file->is($image);
            });
        }
    }

}
