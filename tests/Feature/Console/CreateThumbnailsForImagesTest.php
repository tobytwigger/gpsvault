<?php

namespace Tests\Feature\Console;

use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class CreateThumbnailsForImagesTest extends TestCase
{
    /** @test */
    public function it_generates_thumbnails_for_the_image_files()
    {
        $imagesToGenerate = Model::withoutEvents(fn () => File::factory()->image()->count(5)->create(['thumbnail_id' => null]));
        Model::withoutEvents(fn () => File::factory()->image()->withThumbnail()->count(6)->create());
        Model::withoutEvents(fn () => File::factory()->simpleGpx()->count(7)->create());
        Model::withoutEvents(fn () => File::factory()->image()->count(8)->create(['thumbnail_id' => null, 'type' => FileUploader::IMAGE_THUMBNAIL]));

        $this->assertDatabaseCount('files', 5+6+6+7+8);

        $this->artisan('thumbnail:generate')
            ->expectsOutput('Generating 5 thumbnails.')
            ->assertSuccessful();

        $this->assertDatabaseCount('files', 5+5+6+6+7+8);

        foreach ($imagesToGenerate as $image) {
            $image->refresh();
            $this->assertNotNull($image->thumbnail_id);
            $this->assertEquals($image->title, $image->thumbnail->title);
            $this->assertEquals($image->caption, $image->thumbnail->caption);
        }
    }

    /** @test */
    public function existing_thumbnails_can_be_force_regenerated()
    {
        $imagesToGenerate = Model::withoutEvents(fn () => File::factory()->image()->count(5)->create(['thumbnail_id' => null]));
        $imagesToGenerate2 = Model::withoutEvents(fn () => File::factory()->image()->withThumbnail()->count(6)->create());
        Model::withoutEvents(fn () => File::factory()->simpleGpx()->count(7)->create());
        Model::withoutEvents(fn () => File::factory()->image()->count(8)->create(['thumbnail_id' => null, 'type' => FileUploader::IMAGE_THUMBNAIL]));

        $this->assertDatabaseCount('files', 5+6+6+7+8);

        $this->artisan('thumbnail:generate --regenerate')
            ->expectsOutput('Generating 11 thumbnails.')
            ->assertSuccessful();

        $this->assertDatabaseCount('files', 5+5+6+6+7+8);

        foreach ($imagesToGenerate as $image) {
            $image->refresh();
            $this->assertNotNull($image->thumbnail_id);
            $this->assertEquals($image->title, $image->thumbnail->title);
            $this->assertEquals($image->caption, $image->thumbnail->caption);
        }

        foreach ($imagesToGenerate2 as $image) {
            $oldThumbnail = $image->thumbnail_id;
            $image->refresh();
            $this->assertNotEquals($oldThumbnail, $image->thumbnail_id);
        }
    }
}
