<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use Spatie\Url\Url;

abstract class Page extends BasePage
{
    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    public function url()
    {
        return Url::fromString(route($this->routeName()))->getPath();
    }

    abstract public function routeName(): string;
}
