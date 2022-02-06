<?php

namespace Tests\Integration\File;

use App\Models\File;
use App\Models\User;
use App\Services\Analysis\Parser\Point;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileUploaderTest extends TestCase
{

    /** @test */
    public function it_saves_a_file_with_contents(){
        $contents = 'This is a test';
        $user = User::factory()->create();

        $file = Upload::withContents($contents, 'myfile.txt', $user, 'some-type');

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('myfile.txt', $file->filename);
        $this->assertEquals('txt', $file->extension);
        $this->assertEquals('some-type', $file->type);
        $this->assertNull($file->title);
        $this->assertNull($file->caption);
        $this->assertEquals('text/plain', $file->mimetype);
        $this->assertEquals($user->id, $file->user_id);
        $this->assertEquals($contents, $file->getFileContents());
    }

    /** @test */
    public function it_saves_a_file_from_an_uploaded_file(){
        $contents = 'This is a test';
        $path = Str::random('20') . '.txt';
        Storage::disk('tests')->put($path, $contents);
        $uploadedFile = new UploadedFile(Storage::disk('tests')->path($path), 'filename2.txt', 'text/plain');

        $user = User::factory()->create();

        $file = Upload::uploadedFile($uploadedFile, $user, 'some-type');

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('filename2.txt', $file->filename);
        $this->assertEquals('txt', $file->extension);
        $this->assertEquals('some-type', $file->type);
        $this->assertNull($file->title);
        $this->assertNull($file->caption);
        $this->assertEquals('text/plain', $file->mimetype);
        $this->assertEquals($user->id, $file->user_id);
        $this->assertEquals($contents, $file->getFileContents());
    }

    /** @test */
    public function it_saves_a_file_from_activity_points(){
        $point1 = (new Point())->setLongitude(55)->setLatitude(0.83)->setSpeed(32);
        $point2 = (new Point())->setLongitude(54)->setLatitude(0.93)->setSpeed(31);
        $point3 = (new Point())->setLongitude(53)->setLatitude(1.03)->setSpeed(30);

        $user = User::factory()->create();

        $file = Upload::activityPoints([
            $point1, $point2, $point3
        ], $user);

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('json.gz', $file->extension);
        $this->assertEquals(FileUploader::ACTIVITY_FILE_POINT_JSON, $file->type);
        $this->assertNull($file->title);
        $this->assertNull($file->caption);
        $this->assertEquals('application/gzip', $file->mimetype);
        $this->assertEquals($user->id, $file->user_id);
        $contents = json_decode(gzuncompress($file?->getFileContents()) ?? [], true);
        $this->assertEquals([
            ['latitude' => 0.83, 'longitude' => 55, 'speed' => 32],
            ['latitude' => 0.93, 'longitude' => 54, 'speed' => 31],
            ['latitude' => 1.03, 'longitude' => 53, 'speed' => 30],
        ], $contents);
    }

    /** @test */
    public function it_saves_a_file_from_route_points(){
        $point1 = (new Point())->setLongitude(55)->setLatitude(0.83)->setSpeed(32);
        $point2 = (new Point())->setLongitude(54)->setLatitude(0.93)->setSpeed(31);
        $point3 = (new Point())->setLongitude(53)->setLatitude(1.03)->setSpeed(30);

        $user = User::factory()->create();

        $file = Upload::routePoints([
            $point1, $point2, $point3
        ], $user);

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals('json.gz', $file->extension);
        $this->assertEquals(FileUploader::ROUTE_FILE_POINT_JSON, $file->type);
        $this->assertNull($file->title);
        $this->assertNull($file->caption);
        $this->assertEquals('application/gzip', $file->mimetype);
        $this->assertEquals($user->id, $file->user_id);
        $contents = json_decode(gzuncompress($file?->getFileContents()) ?? [], true);
        $this->assertEquals([
            ['latitude' => 0.83, 'longitude' => 55, 'speed' => 32],
            ['latitude' => 0.93, 'longitude' => 54, 'speed' => 31],
            ['latitude' => 1.03, 'longitude' => 53, 'speed' => 30],
        ], $contents);
    }

}
