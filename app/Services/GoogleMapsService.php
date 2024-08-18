<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    protected Client $client;
    protected int $cacheTime;

    public function __construct()
    {
        $this->client = new Client();
        $this->cacheTime = 3600; //seconds | 1 hour
//        $this->cacheTime = 600; //seconds | 10 min
    }

    public function getDistance($origin, $destination)
    {
        $cacheKey = $this->generateCacheKey($origin, $destination);

        // check if cache data exists
        $cachedDistance = Cache::get($cacheKey);

        if ($cachedDistance !== null) {
            return $cachedDistance;
        }

        // Fetch the distance from the API
        $distance = $this->fetchDistanceFromApi($origin, $destination);

        // Store distance
        Cache::put($cacheKey, $distance, $this->cacheTime);

        return $distance;
    }

    private function fetchDistanceFromApi($origin, $destination)
    {
        $response = $this->client->get('https://maps.googleapis.com/maps/api/directions/json', [
            'query' => [
                'origin'      => $origin,
                'destination' => $destination,
                'key'         => env('GOOGLE_MAPS_API_KEY'),
                'units'       => 'metric',
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['status'] == 'OK')
        {
            $routes = $data['routes'];
            if (count($routes) > 0)
            {
                $legs = $routes[0]['legs'];
                if (count($legs) > 0)
                {
                    return $legs[0]['distance']['value'] / 1000; // Distance in kilometers
                }
            }
        }

        return 0;
    }

    private function generateCacheKey($origin, $destination)
    {
        return 'google_maps_distance_' . md5($origin . '|' . $destination);
    }
}
