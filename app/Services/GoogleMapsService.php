<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService
{
    public function getTripDetails(
        float $originLat,
        float $originLng,
        float $destinationLat,
        float $destinationLng
    ): array {
        // Validate coordinates
        if (
            $originLat < -90 || $originLat > 90 ||
            $originLng < -180 || $originLng > 180 ||
            $destinationLat < -90 || $destinationLat > 90 ||
            $destinationLng < -180 || $destinationLng > 180
        ) {
            return ['error' => 'INVALID_COORDINATES'];
        }

        $origin = "{$originLat},{$originLng}";
        $destination = "{$destinationLat},{$destinationLng}";

        try {
            $response = Http::timeout(10)
                ->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'origins' => $origin,
                    'destinations' => $destination,
                    'mode' => 'driving',
                    'departure_time' => 'now',
                    'key' => config('services.google_maps.api_key'),
                ]);

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                return ['error' => $data['status']];
            }

            if (
                !isset($data['rows'][0]['elements'][0]) ||
                $data['rows'][0]['elements'][0]['status'] !== 'OK'
            ) {
                return ['error' => $data['rows'][0]['elements'][0]['status'] ?? 'UNKNOWN_ERROR'];
            }

            $element = $data['rows'][0]['elements'][0];

            $duration = $element['duration_in_traffic'] ?? $element['duration'];

            return [
                'distance_meters' => $element['distance']['value'],
                'distance_text' => $element['distance']['text'],
                'duration_seconds' => $duration['value'],
                'duration_text' => $duration['text'],
            ];
        } catch (\Exception $e) {
            return ['error' => 'REQUEST_FAILED', 'message' => $e->getMessage()];
        }
    }
}
