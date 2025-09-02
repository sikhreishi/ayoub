<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\Location\GeohashService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Location\UpdateLocationRequest;
use Illuminate\Support\Facades\DB;

class UserLocationController extends Controller
{
     protected $geohashService;

    public function __construct(GeohashService $geohashService)
    {
        $this->geohashService = $geohashService;
    }

    public function updateLocation(UpdateLocationRequest $request)
    {
        $user = Auth::user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Validate latitude and longitude
        if (!$latitude || !$longitude) {
            return response()->json([
                'message' => 'Latitude and Longitude are required and must be valid numbers.',
            ], 400);
        }

        // Encode user's location into geohash
        $geoHash = $this->geohashService->encode($latitude, $longitude);

        // Update user and driver availability
        $this->updateUserLocation($user, $latitude, $longitude, $geoHash);

        if ($user->hasRole('driver')) {
            $this->updateDriverAvailability($user, $latitude, $longitude, $geoHash);
        }

        return response()->json([
            'message' => 'Location updated successfully',
            'location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'geohash' => $geoHash,
            ],
        ]);
    }


    // Method to update user location (existing)
    protected function updateUserLocation($user, $lat, $lng, $geoHash)
    {
        $user->current_lat = $lat;
        $user->current_lng = $lng;
        $user->geohash = $geoHash;
        $user->location_updated_at = now();
        $user->is_online = true;

        $user->save();
    }

    // Method to update driver availability (existing)
    protected function updateDriverAvailability($user, $lat, $lng, $geoHash)
    {
        if (!$user->is_online) {
            return;
        }

        $hasActiveTrip = \App\Models\Trip::where('driver_id', $user->id)
            ->whereIn('status', ['accepted', 'started'])
            ->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->exists();

        if ($hasActiveTrip) {
            return;
        }

        DB::table('driver_availability')->updateOrInsert(
            ['driver_id' => $user->id],
            [
                'latitude' => $lat,
                'longitude' => $lng,
                'geohash' => $geoHash,
                'last_ping' => now(),
            ]
        );
    }
}


