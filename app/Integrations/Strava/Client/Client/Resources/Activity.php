<?php

namespace App\Integrations\Strava\Client\Client\Resources;

use App\Integrations\Strava\Client\Client\Resource;

class Activity extends Resource
{
    public function getActivity(int $activityId)
    {
        $response = $this->request->request('GET', 'activities/' . $activityId, [
            'query' => [
                'include_all_efforts' => true,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }

    public function getActivities(int $page = 1)
    {
        $response = $this->request->request('GET', 'athlete/activities', [
            'query' =>  [
                'page' => $page,
                'per_page' => 50,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }

    public function getPhotos(int $activityId)
    {
        $response = $this->request->request('GET', 'activities/' . $activityId . '/photos', [
            'query' => [
                'photo_source' => true,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }

    public function getComments(int $activityId, int $page = 1)
    {
        $response = $this->request->request('GET', 'activities/' . $activityId . '/comments', [
            'query' => [
                'page' => $page,
                'per_page' => 200,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }

    public function getKudos(int $activityId, int $page = 1)
    {
        $response = $this->request->request('GET', 'activities/' . $activityId . '/kudos', [
            'query' => [
                'page' => $page,
                'per_page' => 200,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }

    public function getActivityStream($activityId)
    {
        $response = $this->request->request('GET', 'activities/' . $activityId . '/streams', [
            'query' => [
                'keys' => 'time,altitude,heartrate,cadence,watts,temp,moving,latlng,distance,velocity_smooth',
                'key_by_type' => false,
            ],
        ]);

        return $this->request->decodeResponse($response);
    }
}
