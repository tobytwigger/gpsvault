<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\User;
use App\Services\File\FileUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FileTest extends TestCase
{

    /** @test */
    public function it_has_a_relationship_with_a_user()
    {
        $user = User::factory()->create();
        $file = File::factory()->activityFile()->create(['user_id' => $user]);

        $this->assertInstanceOf(User::class, $file->user);
        $this->assertTrue($user->is($file->user));
    }

    /** @test */
    public function it_auto_assigns_the_user_id_on_creation()
    {
        $this->authenticated();
        $file = File::factory()->activityFile()->make(['user_id' => null]);
        $file->save();

        $this->assertEquals($this->user->id, $file->refresh()->user_id);
    }

    /** @test */
    public function it_hashes_the_file_contents_on_save()
    {
        Storage::disk('tests')->put('hashing.txt', 'abc123');
        $hash = md5('abc123');
        $file = File::factory()->create([
            'path' => 'hashing.txt',
            'filename' => 'hashing.txt',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/plain',
            'disk' => 'tests',
            'hash' => null,
        ]);

        $this->assertEquals($hash, $file->hash);
    }

    /** @test */
    public function it_deletes_the_file_when_the_model_is_deleted()
    {
        Storage::disk('test-fake')->put('hashing.txt', 'abc123');
        $hash = md5('abc123');
        $file = File::factory()->create([
            'path' => 'hashing.txt',
            'filename' => 'hashing.txt',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/plain',
            'disk' => 'test-fake',
        ]);

        Storage::disk('test-fake')->assertExists('hashing.txt');
        $file->delete();
        Storage::disk('test-fake')->assertMissing('hashing.txt');
    }

    /** @test */
    public function get_full_path_returns_the_full_path_of_the_file_relative_to_the_disk()
    {
        Storage::disk('test-fake')->put('/tests/hashing.txt', 'abc123');
        $hash = md5('abc123');
        $file = File::factory()->create([
            'path' => '/tests/hashing.txt',
            'filename' => 'hashing.txt',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/plain',
            'disk' => 'test-fake',
        ]);

        $this->assertEquals(Storage::disk('test-fake')->path('/tests/hashing.txt'), $file->fullPath());
    }

    /** @test */
    public function get_file_contents_returns_the_contents_of_the_file()
    {
        Storage::disk('test-fake')->put('hashing.txt', 'abc123');
        $hash = md5('abc123');
        $file = File::factory()->create([
            'path' => 'hashing.txt',
            'filename' => 'hashing.txt',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/plain',
            'disk' => 'test-fake',
        ]);

        $this->assertEquals('abc123', $file->getFileContents());
    }

    /** @test */
    public function return_download_response_returns_a_download_response()
    {
        Storage::disk('test-fake')->put('hashing.txt', 'abc123');
        $hash = md5('abc123');
        $file = File::factory()->create([
            'path' => 'hashing.txt',
            'filename' => 'hashing.txt',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_FILE,
            'mimetype' => 'text/plain',
            'disk' => 'test-fake',
        ]);

        $response = TestResponse::fromBaseResponse($file->returnDownloadResponse());
        $response->assertDownload('hashing.txt');
    }
}
