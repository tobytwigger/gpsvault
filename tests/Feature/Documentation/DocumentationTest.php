<?php

namespace Tests\Feature\Documentation;

use Tests\TestCase;

class DocumentationTest extends TestCase
{

    /** @test */
    public function it_redirects_you_to_the_docs_site()
    {
        config()->set('app.docs', 'https://test-documentation.com');
        $response = $this->get(route('documentation'));
        $response->assertRedirect('https://test-documentation.com');
    }
}
