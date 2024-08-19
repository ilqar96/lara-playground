<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected Client $client;
    protected int $cacheTime;

    public function __construct()
    {
        $this->client = new Client();
//        $this->cacheTime = 3600; //seconds | 1 hour
        $this->cacheTime = 600; //seconds | 10 min
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function getDistance($origin, $destination): float|int
    {
        $cacheKey = $this->generateCacheKey($origin, $destination);

        // check if cache data exists
        $cachedDistance = Cache::get($cacheKey);

        if ($cachedDistance !== null) {
            return $cachedDistance;
        }

        try {
            // Fetch the distance from the API
            $distance = $this->fetchDistanceFromApi($origin, $destination);
            // Store distance cache
            Cache::put($cacheKey, $distance, $this->cacheTime);
            return $distance;
        } catch (Exception $e) {
            throw new Exception("Failed to fetch distance from Google Maps API.");
        }
    }

    /**
     * @throws Exception|GuzzleException
     */
    private function fetchDistanceFromApi($origin, $destination): float|int
    {
        try {
            $response = $this->client->get('https://maps.googleapis.com/maps/api/directions/json', [
                'query' => [
                    'origin'      => $origin,
                    'destination' => $destination,
                    'key'         => env('GOOGLE_MAPS_API_KEY'),
                    'units'       => 'metric',
                ]
            ]);
        } catch (Exception $e) {
            Log::error('GOOGLE_MAP_API request failed: ' . $e->getMessage());
            throw new Exception('Google Maps API request failed: ' . $e->getMessage());
        }


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

        Log::error('GOOGLE_MAP_API Invalid response: '.json_encode($data));
        throw new Exception("Invalid response from Google Maps API.");
    }

    private function generateCacheKey($origin, $destination)
    {
        return 'google_maps_distance_' . md5($origin . '|' . $destination);
    }
}
