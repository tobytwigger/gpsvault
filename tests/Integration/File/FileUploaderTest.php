<?php

namespace Tests\Integration\File;

use App\Models\File;
use App\Models\User;
use App\Services\File\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileUploaderTest extends TestCase
{
    /** @test */
    public function it_saves_a_file_with_contents()
    {
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
    public function it_saves_a_file_from_an_uploaded_file()
    {
        $contents = 'This is a test';
        $path = Str::random('20') . '.txt';
        Storage::disk('test-fake')->put($path, $contents);
        $uploadedFile = new UploadedFile(Storage::disk('test-fake')->path($path), 'filename2.txt', 'text/plain');

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
}
