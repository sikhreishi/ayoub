<?php

namespace App\Services\Location;

class DistanceService
{

    public function haversine(float $startLat, float $startLong, float $endLat, float $endLong): float
    {
        // Radius of the Earth in kilometers
        $earthRadius = 6371;

        // Convert degrees to radians
        $latDiff = deg2rad($endLat - $startLat);
        $longDiff = deg2rad($endLong - $startLong);

        // Haversine formula
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($startLat)) * cos(deg2rad($endLat)) *
            sin($longDiff / 2) * sin($longDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Return the distance in kilometers
        return $earthRadius * $c;
    }
}
