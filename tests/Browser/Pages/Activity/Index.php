<?php

namespace Tests\Browser\Pages\Activity;

use Tests\Browser\Pages\Page;

class Index extends Page
{
    public function routeName(): string
    {
        return 'activity.index';
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
