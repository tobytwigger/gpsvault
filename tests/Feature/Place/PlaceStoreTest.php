<?php

namespace Tests\Feature\Place;

use App\Jobs\AnalyseFile;
use App\Models\Place;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Geometries\Point;
use Tests\TestCase;

class PlaceStoreTest extends TestCase
{

    private function getPlaceAttributes(array $overrides = [], bool $withoutLocation = false): array
    {
        return array_merge([
            'name' => 'Test',
            'description' => 'My Description',
            'type' => 'accommodation',
            'url' => 'https://google.com',
            'phone_number' => '01234567890',
            'email' => 'test@example.com',
            'address' => 'Testville, TO21 3TE',
            'user_id' => $this->user?->id
        ], $withoutLocation ? [] : ['location' => ['lat' => -0.7, 'lng' => 52.0]], $overrides);
    }

    /** @test */
    public function it_creates_a_place(){
        $this->authenticated();

        $response = $this->post(route('place.store'), $this->getPlaceAttributes());
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseCount('places', 1);

        $this->assertDatabaseHas('places', $this->getPlaceAttributes([], true));
    }

    /** @test */
    public function the_user_id_is_always_taken_from_the_user(){
        $this->authenticated();

        $response = $this->post(route('place.store'), $this->getPlaceAttributes(['user_id' => Auth::user()->id]));
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseCount('places', 1);

        $this->assertDatabaseHas('places', $this->getPlaceAttributes(['user_id' => $this->user->id], true));
    }

    /** @test */
    public function it_redirects_to_show_the_new_place(){
        $this->authenticated();

        $response = $this->post(route('place.store'), $this->getPlaceAttributes());
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseCount('places', 1);

        $response->assertRedirect(route('place.show', Place::firstOrFail()));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($key, $value, $error, $errorKeyOverride = null){
        $this->authenticated();
        if(is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }

        $response = $this->post(route('place.store'), $this->getPlaceAttributes([$key => $value]));

        if(!$error) {
            $response->assertSessionMissing($errorKeyOverride ?? $key);
        } else {
            $response->assertSessionHasErrors([$errorKeyOverride ?? $key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', null, 'The name must be a string.'],
            ['name', null, 'The name must be at least 3 characters.'],
            ['name', 'This is a valid namne', false],
            ['description', Str::random(65570), 'The description must not be greater than 65535 characters.'],
            ['description', null, false],
            ['description', 'This is a valid description', false],
            ['type', 'Some other type', 'The selected type is invalid.'],
            ['type', 'accommodation', false],
            ['type', 'food_drink', false],
            ['type', 'shops', false],
            ['type', 'amenities', false],
            ['type', 'other', false],
            ['type', 'tourist', false],
            ['url', 'fsdkjhfskdjf', 'The url must be a valid URL.'],
            ['url', 'https://google.com', false],
            ['phone_number', ['not a string'], 'The phone number must be a string.'],
            ['phone_number', 12345678900, false],
            ['phone_number', '+44123456789', false],
            ['email', 'notanemail', 'The email must be a valid email address.'],
            ['email', 'anemail@example.com', false],
            ['email', Str::random(300).'@hotmail.co.uk', 'The email must not be greater than 255 characters.'],
            ['address', Str::random(65570), 'The address must not be greater than 65535 characters.'],
            ['location', ['lat' => -0.2, 'lng' => 55], false],
            ['location', ['lat' => -91, 'lng' => 55], 'The location.lat must be at least -90.', 'location.lat'],
            ['location', ['lat' => 91, 'lng' => 55], 'The location.lat must not be greater than 90.', 'location.lat'],
            ['location', ['lat' => -0.7, 'lng' => -181], 'The location.lng must be at least -180.', 'location.lng'],
            ['location', ['lat' => 0.7, 'lng' => 181], 'The location.lng must not be greater than 180.', 'location.lng'],
            ['location', ['lat' => 80], 'The location.lng field is required.', 'location.lng'],
            ['location', ['lng' => 80], 'The location.lat field is required.', 'location.lat'],
            ['location', 'test', 'The location must be an array.'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated(){

        $response = $this->post(route('place.store'), $this->getPlaceAttributes());
        $response->assertRedirect(route('login'));
    }

}
