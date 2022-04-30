<?php

namespace Tests;

use App\Console\Commands\InstallPermissions;
use App\Models\User;
use App\Services\Geocoding\Geocoder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, ProphecyTrait;

    protected ?User $user = null;

    public static bool $initialised = false;

    protected function setUp(): void
    {
        parent::setUp();
        $geocoder = $this->prophesize(Geocoder::class);
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        Artisan::call(InstallPermissions::class);
        Carbon::setTestNow(Carbon::now());
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [realpath(__DIR__ . '/../resources/js/pages')],
        ));
        config()->set('filesystems.disks.tests', [
            'driver' => 'local', 'root' => storage_path('tests'),
        ]);
        Storage::fake('test-fake');
    }

    public function authenticated(array $parameters = [])
    {
        $this->user = $this->user ?? User::factory()->create($parameters);

        $this->be($this->user);
    }
}
