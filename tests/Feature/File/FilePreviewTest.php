<?php

namespace Tests\Feature\File;

use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FilePreviewTest extends TestCase
{

    /** @test */
    public function it_previews_a_file()
    {
        $this->authenticated();
        $path = 'preview-file-' . Str::random(10) . '.txt';
        Storage::disk('tests')->put($path, 'Text Content');
        $file = File::factory()->create([
            'path' => $path,
            'disk' => 'tests',
            'user_id' => $this->user->id,
            'filename' => 'filename.jpeg',
            'mimetype' => 'text/plain',
            'extension' => 'txt',
            'type' => FileUploader::ACTIVITY_MEDIA
        ]);
        $response = $this->get(route('file.preview', $file));

        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSeeText('Text Content');
    }

    /** @test */
    public function you_can_only_preview_your_own_files()
    {
        $this->authenticated();
        $file = File::factory()->activityMedia()->create(['filename' => 'filename.jpeg']);

        $response = $this->get(route('file.preview', $file))
            ->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist()
    {
        $this->authenticated();

        $response = $this->get(route('file.preview', 1000))
            ->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $file = File::factory()->activityMedia()->create(['filename' => 'filename.jpeg']);

        $response = $this->get(route('file.preview', $file))
            ->assertRedirect(route('login'));
    }
}
