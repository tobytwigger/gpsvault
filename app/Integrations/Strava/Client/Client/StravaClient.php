<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Client\Models\StravaActivity;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use GuzzleHttp\Client;

class StravaClient
{

    protected ?Client $client = null;

    public function __construct(private string $authToken, private int $userId, private ConnectionLog $log)
    {
    }

    protected function client(): Client
    {
        if($this->client === null) {
            $this->client = new Client([
                'base_uri' => 'https://www.strava.com/api/v3/',
            ]);
        }
        return $this->client;
    }

    protected function request(string $method, string $uri, array $options = []): \Psr\Http\Message\ResponseInterface
    {
        $this->log->debug(sprintf('Making a %s request to %s', $method, $uri));

        try {
            return $this->client()->request($method, $uri, array_merge([
                'headers' => array_merge([
                    'Authorization' => sprintf('Bearer %s', $this->authToken)
                ], $options['headers'] ?? [])
            ], $options));
        } catch (\Exception $e) {
            $this->log->error(sprintf('Request failed with code %d: %s', $e->getCode(), $e->getMessage()));
            throw $e;
        }
    }

    public function getActivities(int $page = 1)
    {
        $this->log->startRequest();

        $this->log->debug(sprintf('About to get user activities, page %d', $page));

        $response = $this->request('GET', 'athlete/activities', [
            'query' =>  [
                'page' => $page,
                'per_page' => 50
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved user activities, page %d', $page));

        $this->log->endRequest();

        return $content;
    }

//    /**
//     * @param int $clubId
//     * @param int $page
//     * @return StravaActivity[]|array
//     * @throws \Exception
//     */
//    public function getClubActivityPage(int $clubId, int $page): array
//    {
//        $this->log->startRequest();
//
//        $this->log->debug(sprintf('About to get activities for club %d, page %d', $clubId, $page));
//
//        $response = $this->request('GET', sprintf('clubs/%s/activities', $clubId), [
//            'query' =>  [
//                'page' => $page,
//                'per_page' => 300
//            ]
//        ]);
//
//        $content = json_decode(
//            $response->getBody()->getContents(),
//            true
//        );
//
//        $result = array_map(function(array $parameters) {
//            return StravaActivity::make(
//                $this->userId,
//                $parameters['name'] ?? null,
//                $parameters['distance'] ?? 0.0,
//                $parameters['total_elevation_gain'] ?? 0.0,
//                $parameters['moving_time'] ?? 0,
//                $parameters['elapsed_time'] ?? 0,
//                $parameters['type'] ?? 'Other'
//            );
//        }, $content);
//
//        $this->log->info(sprintf('Retrieved activities for club %d, page %d', $clubId, $page));
//
//        $this->log->endRequest();
//
//        return $result;
//    }
//
//    /**
//     * @param int $clubId
//     * @return array|StravaActivity[]
//     */
//    public function getClubActivities(int $clubId): array
//    {
//        $this->log->startRequest();
//
//        $this->log->debug(sprintf('About to get all club activities'));
//
//        $activities = [];
//        $page = 1;
//
//        do {
//            $activityPage = $this->getClubActivityPage($clubId, $page);
//            if(count($activityPage) > 0) {
//                array_push($activities, ...$activityPage);
//                $page++;
//            }
//        } while (count($activityPage) > 0);
//
//        $this->log->info(sprintf('Retrieved all activities for club %d from Strava.', $clubId));
//
//        $this->log->endRequest();
//
//        return $activities;
//    }

}
