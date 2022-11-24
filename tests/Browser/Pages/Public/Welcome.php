<?php

namespace Tests\Browser\Pages\Public;

use Tests\Browser\Pages\Page;

class Welcome extends Page
{
    public function routeName(): string
    {
        return 'welcome';
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
