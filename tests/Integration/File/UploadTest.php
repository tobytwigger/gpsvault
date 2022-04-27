<?php

namespace Tests\Integration\File;

use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Tests\TestCase;

class UploadTest extends TestCase
{

    /** @test */
    public function it_resolves_the_underlying_instance()
    {
        $instance = Upload::getFacadeRoot();
        $this->assertInstanceOf(FileUploader::class, $instance);
    }
}
