<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use Illuminate\Support\Str;
use Tests\TestCase;

class TourStoreTest extends TestCase
{

    /** @test */
    public function it_creates_an_tour_from_a_name()
    {
        $this->authenticated();

        $response = $this->post(route('tour.store'), [
            'name' => 'Some name',
        ]);

        $this->assertDatabaseCount('tours', 1);
        $this->assertDatabaseHas('tours', [
            'name' => 'Some name',
        ]);
    }

    /** @test */
    public function it_redirects_to_show_the_new_tour()
    {
        $this->authenticated();

        $response = $this->post(route('tour.store'), [
            'name' => 'Some name',
        ]);

        $this->assertDatabaseCount('tours', 1);
        $response->assertRedirect(route('tour.show', Tour::firstOrFail()));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     * @param mixed $key
     * @param mixed $value
     * @param mixed $error
     */
    public function it_validates($key, $value, $error)
    {
        $this->authenticated();


        $response = $this->post(route('tour.store'), [$key => $value]);
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', null, 'The name field is required.'],
            ['name', 'This is a valid name', false],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->post(route('tour.store'), [
            'name' => 'This is the tour name',
        ]);
        $response->assertRedirect(route('login'));
    }
}
