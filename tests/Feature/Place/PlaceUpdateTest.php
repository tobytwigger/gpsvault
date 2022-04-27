<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlaceUpdateTest extends TestCase
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
    public function it_updates_a_place()
    {
        $this->authenticated();

        $place = Place::factory()->create(['user_id' => $this->user->id, 'type' => 'food_drink']);

        foreach (array_filter($this->getPlaceAttributes(['location' => ['lat' => 0.1, 'lng' => 55], 'user_id' => null])) as $key => $attribute) {
            $response = $this->patch(route('place.update', $place), $this->getPlaceAttributes());
            $response->assertRedirect();
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseCount('places', 1);
            if ($key !== 'location') {
                $this->assertDatabaseMissing('places', [$key => $place->{$key}]);
                $this->assertDatabaseHas('places', [$key => $attribute]);
            }
        }
    }

    /** @test */
    public function the_user_id_cannot_be_updated()
    {
        $this->authenticated();

        $user = User::factory()->create();

        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('place.update', $place), ['user_id' => $user->id]);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('places', 1);
        $this->assertDatabaseHas('places', ['user_id' => $this->user->id]);
        $this->assertDatabaseMissing('places', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_redirects_to_show_the_place()
    {
        $this->authenticated();
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('place.update', $place), [
            'location' => ['lat' => -0.9, 'lng' => 51.3]
        ]);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseCount('places', 1);

        $response->assertRedirect(route('place.show', Place::firstOrFail()));
    }

    /** @test */
    public function you_get_a_403_if_you_try_to_edit_a_place_that_is_not_yours()
    {
        $this->authenticated();

        $place = Place::factory()->create();

        $response = $this->patch(route('place.update', $place), [
            'name' => 'This is the name',
            'description' => 'A new description'
        ]);
        $response->assertStatus(403);

        $this->assertDatabaseCount('places', 1);

        $this->assertDatabaseHas('places', [
            'name' => $place->name,
            'description' => $place->description,
            'email' => $place->email,
            'address' => $place->address,
            'id' => $place->id,
            'phone_number' => $place->phone_number,
        ]);
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     * @param mixed $key
     * @param mixed $value
     * @param mixed $error
     * @param null|mixed $overrideErrorKey
     */
    public function it_validates($key, $value, $error, $overrideErrorKey = null)
    {
        $this->authenticated();
        if (is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }

        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('place.update', $place), [$key => $value]);

        if (!$error) {
            $response->assertSessionMissing($overrideErrorKey ?? $key);
        } else {
            $response->assertSessionHasErrors([$overrideErrorKey ?? $key => $error]);
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
            ['type', 'toilets', false],
            ['type', 'water', false],            ['type', 'other', false],
            ['type', 'tourist', false],
            ['url', 'fsdkjhfskdjf', 'The url must be a valid URL.'],
            ['url', 'https://google.com', false],
            ['phone_number', ['not a string'], 'The phone number must be a string.'],
            ['phone_number', 12345678900, false],
            ['phone_number', '+44123456789', false],
            ['email', 'notanemail', 'The email must be a valid email address.'],
            ['email', 'anemail@example.com', false],
            ['email', Str::random(300) . '@hotmail.co.uk', 'The email must not be greater than 255 characters.'],
            ['address', Str::random(65570), 'The address must not be greater than 65535 characters.'],
            ['location', ['lat' => -0.2, 'lng' => 55], false],
            ['location', ['lat' => -91, 'lng' => 55], 'The location.lat must be at least -90.', 'location.lat'],
            ['location', ['lat' => 91, 'lng' => 55], 'The location.lat must not be greater than 90.', 'location.lat'],
            ['location', ['lat' => -0.7, 'lng' => -181], 'The location.lng must be at least -180.', 'location.lng'],
            ['location', ['lat' => 0.7, 'lng' => 181], 'The location.lng must not be greater than 180.', 'location.lng'],
            ['location', ['lat' => 80], 'The location.lng field is required when location is present.', 'location.lng'],
            ['location', ['lng' => 80], 'The location.lat field is required when location is present.', 'location.lat'],
            ['location', 'test', 'The location must be an array.'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $place = Place::factory()->create();

        $response = $this->patch(route('place.update', $place), $this->getPlaceAttributes());
        $response->assertRedirect(route('login'));
    }
}
