<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateThumbnailImage;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use Tests\TestCase;

class CreateThumbnailImageTest extends TestCase
{

    /** @test */
    public function it_creates_a_lower_resolution_image(){
        $file = Model::withoutEvents(fn() => File::factory()->image()->create([
            'title' => 'My file title',
            'caption' => 'This is my file caption'
        ]));

        $this->assertDatabaseCount( 'files', 1);

        $this->assertEquals(1023, Image::make($file->getFileContents())->width());
        $this->assertEquals(682, Image::make($file->getFileContents())->height());

        $job = new  CreateThumbnailImage($file);
        $job->handle();

        $this->assertDatabaseCount( 'files', 2);

        $retrievedFile = File::orderBy('id', 'DESC')->first();

        $this->assertEquals('My file title', $retrievedFile->title);
        $this->assertEquals('This is my file caption', $retrievedFile->caption);
        $this->assertEquals(344, Image::make($retrievedFile->getFileContents())->width());
        $this->assertEquals(229, Image::make($retrievedFile->getFileContents())->height());

    }

    /** @test */
    public function it_creates_a_lower_resolution_image_on_a_file_being_saved(){
        $this->assertDatabaseCount( 'files', 0);

        $file = File::factory()->image()->create([
            'title' => 'My file title',
            'caption' => 'This is my file caption'
        ]);

        $this->assertEquals(1023, Image::make($file->getFileContents())->width());
        $this->assertEquals(682, Image::make($file->getFileContents())->height());

        $this->assertDatabaseCount( 'files', 2);

        $retrievedFile = File::orderBy('id', 'DESC')->first();

        $this->assertEquals('My file title', $retrievedFile->title);
        $this->assertEquals('This is my file caption', $retrievedFile->caption);
        $this->assertEquals(344, Image::make($retrievedFile->getFileContents())->width());
        $this->assertEquals(229, Image::make($retrievedFile->getFileContents())->height());
    }

}
