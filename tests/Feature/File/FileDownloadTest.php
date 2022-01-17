<?php

namespace Tests\Feature\File;

use App\Models\File;
use Tests\TestCase;

class FileDownloadTest extends TestCase
{

    /** @test */
    public function it_downloads_a_file(){
        $this->authenticated();
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id, 'filename' => 'filename.jpeg']);

        $response = $this->get(route('file.download', $file));

        $response->assertDownload('filename.jpeg');
    }

    /** @test */
    public function you_can_only_download_your_own_files(){
        $this->authenticated();
        $file = File::factory()->activityMedia()->create(['filename' => 'filename.jpeg']);

        $response = $this->get(route('file.download', $file))
            ->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist(){
        $this->authenticated();

        $response = $this->get(route('file.download', 1000))
            ->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $file = File::factory()->activityMedia()->create(['filename' => 'filename.jpeg']);

        $response = $this->get(route('file.download', $file))
            ->assertRedirect(route('login'));
    }

}
