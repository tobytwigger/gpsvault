<?php

namespace Tests\Feature\Documentation;

use Tests\TestCase;

class DocumentationTest extends TestCase
{
    /** @test */
    public function it_redirects_you_to_the_docs_site()
    {
        $response = $this->get(route('documentation'));
        $response->assertRedirect(url('/docs'));
    }
}
