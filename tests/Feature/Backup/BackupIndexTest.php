<?php

namespace Tests\Feature\Backup;

use App\Models\Backup;
use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BackupIndexTest extends TestCase
{

    /** @test */
    public function index_loads_backups_ordered_by_date(){
        $this->authenticated();
        $backups = File::factory()->archive()->count(5)->create(['user_id' => $this->user->id]);
        $backups[0]->created_at = Carbon::now()->subDays(4);
        $backups[1]->created_at = Carbon::now()->subDays(2);
        $backups[2]->created_at = Carbon::now()->subDays(1);
        $backups[3]->created_at = Carbon::now()->subDays(11);
        $backups[4]->created_at = Carbon::now()->subDays(4);
        $backups->map->save();

        $this->get(route('backup.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Backups/Index')
                ->has('backups', fn (Assert $page) => $page
                    ->has('data', 5)
                    ->has('data.0', fn(Assert $page) => $page->where('id', $backups[2]->id)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $backups[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $backups[0]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $backups[4]->id)->etc())
                    ->has('data.4', fn(Assert $page) => $page->where('id', $backups[3]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_paginates_backups(){
        $this->authenticated();
        $backups = File::factory()->archive()->count(20)->create(['user_id' => $this->user->id, 'created_at' => null]);
        foreach($backups as $index => $backup) {
            $backup->created_at = Carbon::now()->subDays($index);
            $backup->save();
        }

        $this->get(route('backup.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Backups/Index')
                ->has('backups', fn (Assert $page) => $page
                    ->has('data', 4)
                    ->has('data.0', fn(Assert $page) => $page->where('id', $backups[4]->id)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $backups[5]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $backups[6]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $backups[7]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_only_returns_your_backups(){
        $this->authenticated();
        $backups = File::factory()->archive()->count(3)->create(['user_id' => $this->user->id, 'created_at' => null]);
        File::factory()->archive()->count(2)->create(['created_at' => null]);

        $this->get(route('backup.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Backups/Index')
                ->has('backups', fn (Assert $page) => $page
                    ->has('data', 3)
                    ->has('data.0', fn(Assert $page) => $page->where('id', $backups[0]->id)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $backups[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $backups[2]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function you_must_be_authenticated(){
        $this->get(route('backup.index'))->assertRedirect(route('login'));
    }

}
