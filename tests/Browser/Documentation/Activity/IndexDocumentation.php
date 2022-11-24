<?php

namespace Tests\Browser\Documentation\Activity;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Activity\Index as IndexPage;
use Tests\DocumentationGenerator;

class IndexDocumentation extends DocumentationGenerator
{
    public function testMainPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user())
                ->visit(new IndexPage())
                ->docsScreenshot('activity/index/basic');
        });
    }
}
