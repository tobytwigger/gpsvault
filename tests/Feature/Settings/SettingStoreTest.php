<?php

namespace Tests\Feature\Settings;

use App\Console\Commands\InstallPermissions;
use App\Integrations\Strava\Models\StravaClient;
use App\Settings\DarkMode;
use App\Settings\UnitSystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\Assert;
use Tests\TestCase;
use function Deployer\artisan;

class SettingStoreTest extends TestCase
{

    /** @test */
    public function dark_mode_can_be_updated(){
        $this->authenticated();

        DarkMode::setValue(true, $this->user->id);

        $response = $this->post(route('settings.store'), ['dark_mode' => false]);

        $this->assertFalse(DarkMode::getValue($this->user->id));
    }

    /** @test */
    public function unit_system_can_be_updated(){
        $this->authenticated();

        UnitSystem::setValue('metric', $this->user->id);

        $response = $this->post(route('settings.store'), ['unit_system' => 'imperial']);

        $this->assertFalse(DarkMode::getValue($this->user->id));
    }

    /** @test */
    public function strava_client_id_can_be_updated_with_the_right_permission(){
        $this->authenticated();

        $this->user->givePermissionTo('manage-global-settings');

        $client1 = StravaClient::factory()->create();
        $client2 = StravaClient::factory()->create();

        \App\Settings\StravaClient::setValue($client1->id);

        $response = $this->post(route('settings.store'), ['strava_client_id' => $client2->id]);

        $this->assertEquals($client2->id, \App\Settings\StravaClient::getValue());
    }

    /** @test */
    public function stats_order_can_be_updated(){
        $this->authenticated();

        UnitSystem::setValue('metric', $this->user->id);

        $response = $this->post(route('settings.store'), ['unit_system' => 'imperial']);

        $this->assertFalse(DarkMode::getValue($this->user->id));
    }

    /** @test */
    public function strava_client_id_cannot_be_updated_without_the_right_permission(){
        $this->authenticated();

        $client1 = StravaClient::factory()->create();
        $client2 = StravaClient::factory()->create();

        \App\Settings\StravaClient::setValue($client1->id);

        $response = $this->post(route('settings.store'), ['strava_client_id' => $client2->id]);

        $this->assertEquals($client1->id, \App\Settings\StravaClient::getValue());
    }

    /** @test */
    public function it_redirects_to_the_settings_index(){
        $this->authenticated();
        $response = $this->post(route('settings.store'));
        $response->assertRedirect(route('settings.index'));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($key, $value, $error){
        $this->authenticated();

        $response = $this->post(route('settings.store'), [$key => $value]);
        if(!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['unit_system', 'metric', false],
            ['unit_system', 'imperial', false],
            ['unit_system', 'another', 'The selected unit system is invalid.'],
            ['unit_system', null, 'The unit system must be a string.'],
        ];
    }

    /** @test */
    public function you_must_be_logged_in(){
        $this->get(route('settings.store'))
            ->assertRedirect(route('login'));
    }


}
