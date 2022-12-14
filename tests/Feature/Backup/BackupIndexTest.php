<?php

namespace Tests\Feature\Backup;

use App\Models\File;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BackupIndexTest extends TestCase
{
    /** @test */
    public function index_loads_the_page()
    {
        $this->authenticated();

        $this->get(route('backup.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Backups/Index')
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('backup.index'))->assertRedirect(route('login'));
    }
}
