<?php

namespace Integration\Services\ActivityImport;

use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Tests\TestCase;

class ActivityImporterTest extends TestCase
{

    /** @test */
    public function it_creates_an_activity()
    {
        $user = User::factory()->create();
        $activity = ActivityImporter::for($user)
            ->import();
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($user->id, $activity->user_id);
    }

    /** @test */
    public function it_sets_information_on_the_activity()
    {
        $user = User::factory()->create();
        $activity = ActivityImporter::for($user)
            ->withName('Test Name')
            ->withDescription('Test Description')
            ->linkTo('strava')
            ->linkTo('komoot')
            ->import();
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($user->id, $activity->user_id);
        $this->assertEquals('Test Name', $activity->name);
        $this->assertEquals('Test Description', $activity->description);
        $this->assertEquals(['strava', 'komoot'], $activity->linked_to);
    }

    /** @test */
    public function it_sets_additional_data()
    {
        $activity = ActivityImporter::for(User::factory()->create())
            ->setAdditionalData('test', 'one')
            ->setAdditionalData('test-two', 'two')
            ->import();
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals(['test' => 'one', 'test-two' => 'two'], $activity->getAllAdditionalData()->toArray());
    }

    /** @test */
    public function it_sets_an_array_value_for_additional_data()
    {
        $activity = ActivityImporter::for(User::factory()->create())
            ->pushToAdditionalDataArray('test', 'one')
            ->pushToAdditionalDataArray('test', 'two')
            ->pushToAdditionalDataArray('test-two', 'three')
            ->pushToAdditionalDataArray('test-two', 'four')
            ->import();
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals(
            ['test' => ['one', 'two'], 'test-two' => ['three', 'four']],
            $activity->getAllAdditionalData()->toArray()
        );
    }

    /** @test */
    public function it_can_update_data()
    {
        /** @var Activity $activity */
        $activity = Activity::factory()->create();
        $activity->setAdditionalData('test-single', 'hello');
        $activity->setAdditionalData('test-single-two', 'hello-two');
        $activity->pushToAdditionalDataArray('test', 'one');
        $activity->pushToAdditionalDataArray('test', 'two');
        $activity->pushToAdditionalDataArray('test-two', 'three');
        $activity->pushToAdditionalDataArray('test-two', 'four');

        $this->assertEquals([
            'test-single' => 'hello',
            'test-single-two' => 'hello-two',
            'test' => ['one', 'two'],
            'test-two' => ['three', 'four']
        ], $activity->getAllAdditionalData()->toArray());

        $activity = ActivityImporter::update($activity)
            ->setAdditionalData('test-single-two', 'hello-three')
            ->pushToAdditionalDataArray('test-two', 'five')
            ->pushToAdditionalDataArray('test-two', 'six')
            ->import();
        $this->assertEquals(
            [
                'test-single' => 'hello',
                'test-single-two' => 'hello-three',
                'test' => ['one', 'two'],
                'test-two' => ['three', 'four', 'five', 'six']
            ],
            $activity->getAllAdditionalData()->toArray()
        );
    }

    /** @test */
    public function it_can_remove_data()
    {
        /** @var Activity $activity */
        $activity = Activity::factory()->create();
        $activity->setAdditionalData('test-single', 'hello');
        $activity->setAdditionalData('test-single-two', 'hello-two');
        $activity->pushToAdditionalDataArray('test', 'one');
        $activity->pushToAdditionalDataArray('test', 'two');
        $activity->pushToAdditionalDataArray('test-two', 'three');
        $activity->pushToAdditionalDataArray('test-two', 'four');

        $this->assertEquals([
            'test-single' => 'hello',
            'test-single-two' => 'hello-two',
            'test' => ['one', 'two'],
            'test-two' => ['three', 'four']
        ], $activity->getAllAdditionalData()->toArray());

        $activity = ActivityImporter::update($activity)
            ->unsetAdditionalData('test-single-two')
            ->unsetAdditionalData('test-two')
            ->removeFromAdditionalDataArray('test', 'two')
            ->save();

        $this->assertEquals(
            [
                'test-single' => 'hello',
                'test' => 'one'
            ],
            $activity->getAllAdditionalData()->toArray()
        );
    }

    /*
     * - Set additional data and array and get it
     * - Update single value check it updates
     * - Update array value check it updates
     * - Delete a single value check it updates
     * - Delete an element from an array check it updates
     * - Delete and add an element from an array check it updaets
     * - Mixture of them all
     */
}
