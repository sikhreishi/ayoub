<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        // Validate input
        $data = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
            'state' => 'required|in:idle,pre-trip,on-trip',
            'trip_id' => 'nullable|exists:trips,id',
            'type' => 'required|in:0,1', // ✅ Fixed here
        ]);

        $type = (int) $data['type']; // ✅ Parse to integer

        // Update user live position in users table
        $user->update([
            'current_lat' => $data['lat'],
            'current_lng' => $data['lng'],
            'location_updated_at' => now(),
        ]);

        // Update driver_availability if driver
        if ($type === 1) {
            DB::table('driver_availability')->updateOrInsert(
                ['driver_id' => $user->id],
                [
                    'latitude' => $data['lat'],
                    'longitude' => $data['lng'],
                    'geohash' => '', // ✅ TODO: Replace with geohash logic
                    'last_ping' => now(),
                ]
            );
        }

        // Insert into locations table
        DB::table('locations')->insert([
            'trip_id' => $data['trip_id'],
            'type' => $type,
            'user_id' => $user->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'speed' => $data['speed'],
            'recorded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'location saved']);
    }


}
