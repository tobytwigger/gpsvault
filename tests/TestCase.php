<?php

namespace Tests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations, ProphecyTrait;

    protected ?User $user = null;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [realpath(__DIR__ . '/../resources/js/pages')],
        ));
        config()->set('filesystems.disks.tests', [
            'driver' => 'local', 'root' => storage_path('tests')
        ]);
    }

    public function authenticated(array $parameters = [])
    {
        $this->user = $this->user ?? User::factory()->create($parameters);

        $this->be($this->user);
    }
}
