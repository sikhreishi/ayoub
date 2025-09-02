<?php

namespace App\Services\Location;

class GeohashService
{
    protected $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';

    public function encode(float $latitude, float $longitude, int $precision = 9): string
    {
        $latRange = [-90.0, 90.0];
        $lngRange = [-180.0, 180.0];
        $geohash = '';
        $isEvenBit = true;
        $bit = 0;
        $ch = 0;

        while (strlen($geohash) < $precision) {
            if ($isEvenBit) {
                $mid = ($lngRange[0] + $lngRange[1]) / 2;
                if ($longitude > $mid) {
                    $ch |= 1 << (4 - $bit);
                    $lngRange[0] = $mid;
                } else {
                    $lngRange[1] = $mid;
                }
            } else {
                $mid = ($latRange[0] + $latRange[1]) / 2;
                if ($latitude > $mid) {
                    $ch |= 1 << (4 - $bit);
                    $latRange[0] = $mid;
                } else {
                    $latRange[1] = $mid;
                }
            }

            $isEvenBit = !$isEvenBit;

            if (++$bit == 5) {
                $geohash .= $this->base32[$ch];
                $bit = 0;
                $ch = 0;
            }
        }

        return $geohash;
    }


    public function decode(string $geohash): array
    {

        if (empty($geohash)) {
            throw new \InvalidArgumentException("Geohash cannot be empty");
        }

        $isEvenBit = true;
        $latRange = [-90.0, 90.0];
        $lngRange = [-180.0, 180.0];

        for ($i = 0; $i < strlen($geohash); $i++) {
            $char = $geohash[$i];
            $index = strpos($this->base32, $char);

            if ($index === false) {
                throw new \InvalidArgumentException("Invalid geohash character: $char");
            }

            for ($j = 0; $j < 5; $j++) {
                $mask = 1 << (4 - $j);
                $isSet = ($index & $mask) !== 0;

                if ($isEvenBit) {
                    // Longitude bit
                    $mid = ($lngRange[0] + $lngRange[1]) / 2;
                    if ($isSet) {
                        $lngRange[0] = $mid;
                    } else {
                        $lngRange[1] = $mid;
                    }
                } else {
                    // Latitude bit
                    $mid = ($latRange[0] + $latRange[1]) / 2;
                    if ($isSet) {
                        $latRange[0] = $mid;
                    } else {
                        $latRange[1] = $mid;
                    }
                }

                $isEvenBit = !$isEvenBit;
            }
        }

        $latitude = ($latRange[0] + $latRange[1]) / 2;
        $longitude = ($lngRange[0] + $lngRange[1]) / 2;

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'bounds' => [
                'lat' => $latRange,
                'lng' => $lngRange,
            ]
        ];
    }
}
