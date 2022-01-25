<?php

namespace Unit\Integrations\Strava\Client\Client;

use Tests\TestCase;

class StravaRequestHandlerTest extends TestCase
{

    /** @test */
    public function request_makes_a_request_with_an_available_client(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function request_updates_rate_limits_on_successful_response(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function request_marks_client_as_limited_on_429_exception(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function request_throws_an_exception_that_isnt_a_429(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function request_tries_with_other_available_clients_if_first_client_throws_exception(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function decodeResponse_decodes_a_response(){
        $this->markTestIncomplete();

    }

    /** @test */
    public function getGuzzleClient_returns_the_guzzle_client(){
        $this->markTestIncomplete();

    }

}

