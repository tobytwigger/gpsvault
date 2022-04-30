<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\AdditionalData;
use Tests\TestCase;

class AdditionalDataTest extends TestCase
{

    /**
     * @test
     * @dataProvider storableData
     * @param mixed $data
     */
    public function it_can_store_a_variety_of_data($data)
    {
        /** @var Activity $activity */
        $activity = Activity::factory()->create();
        $activity->setAdditionalData('data', $data);
        $additionalData = AdditionalData::factory()->create(['key' => 'data', 'value' => $data, 'additional_data_type' => 'dummy', 'additional_data_id' => 1]);

        $this->assertEquals($data, AdditionalData::find($additionalData->id)->value);
    }

    public function storableData()
    {
        return [
            ['string'],
            [555],
            [['some' => 'array']],
            [['another', 'array']],
            [null],
            [false],
        ];
    }
}
