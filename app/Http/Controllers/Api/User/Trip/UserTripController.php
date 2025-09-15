<?php

namespace App\Http\Controllers\Api\User\Trip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Location\GeohashService;
use App\Services\Location\DistanceService;
use App\Services\TripCostService; 
use App\Models\DriverAvailability;
use App\Models\Trip;
use App\Models\Vehicle\VehicleType;
use Carbon\Carbon;
use GPBMetadata\Google\Api\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Validator;
use App\Services\Firebase\FirebaseService;
use App\Models\Vehicle\Vehicle;
use App\Models\DriverProfile;
use App\Http\Resources\TripResource;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use App\Models\TripHistory;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use App\Services\Firebase\FirebaseInstantNotificationService;
use App\Http\Requests\Api\CancelTripRequest;
use App\Models\CancelReason;

class UserTripController extends Controller
{
    protected $geohashService;
    protected $distanceService;
    protected $firebase;
    protected $tripCostService;
    protected $FirebaseInstantNotificationService;

    public function __construct(GeohashService $geohashService, DistanceService $distanceService, FirebaseService $firebaseService,FirebaseInstantNotificationService $firebaseInstantNotificationService)
    {
        $this->geohashService = $geohashService;
        $this->distanceService = $distanceService;
        $this->firebase = $firebaseService;
        $this->tripCostService = new TripCostService();
        $this->FirebaseInstantNotificationService = $firebaseInstantNotificationService;

    }


    public function fetchVehicleTypes(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'start_latitude' => 'required|numeric|between:-90,90',
            'start_longitude' => 'required|numeric|between:-180,180',
            'end_latitude' => 'required|numeric|between:-90,90',
            'end_longitude' => 'required|numeric|between:-180,180',
            // 'promo_code' => 'nullable|string|in:DISCOUNT10,DISCOUNT20',
            'promo_code' => 'nullable|string',
            'ETA' => 'nullable|integer|min:1',
            'distance_km' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $startLat = $request->start_latitude;
        $startLong = $request->start_longitude;
        $endLat = $request->end_latitude;
        $endLong = $request->end_longitude;

        // Validate that all necessary location data is provided
        if (!$startLat || !$startLong || !$endLat || !$endLong) {
            return response()->json([
                'message' => 'Start and end locations (latitude and longitude) are required.',
            ], 400);
        }

        $distance = $request->distance_km ?? $this->calculateDistance($startLat, $startLong, $endLat, $endLong);
        $vehicleTypes = VehicleType::where('is_active', true)
            ->get([
                'id',
                'name',
                'icon_url',
                'start_fare',
                'day_per_km_rate',
                'night_per_km_rate',
                'day_per_minute_rate',
                'night_per_minute_rate'
            ])
            ->map(function ($type) use ($request, $distance) {

                if ($type->icon_url) {
                    $type->icon_url = asset($type->icon_url);
                }

                $currentHour = Carbon::now()->hour;
                $isNightTime = ($currentHour >= 18 || $currentHour < 6);

                $perKmRate = $isNightTime ? $type->night_per_km_rate : $type->day_per_km_rate;
                $perMinuteRate = $isNightTime ? $type->night_per_minute_rate : $type->day_per_minute_rate;

                // $promoCodeDiscount = 0;
                
                // if ($request->promo_code && $request->promo_code === "DISCOUNT10") {
                //     $promoCodeDiscount = 10;
                // }

                $ETA = $request->ETA ?? 10; // Default to 10 minutes if no ETA is provided
    
                // $totalBeforeDiscount = $type->start_fare + ($distance * $perKmRate) + ($ETA * $perMinuteRate);
                // $total = $totalBeforeDiscount - ($totalBeforeDiscount * $promoCodeDiscount) / 100;

                $promoApplied = false;
                $couponCode   = $request->promo_code;

                $totalBeforeDiscount = $type->start_fare + ($distance * $perKmRate) + ($ETA * $perMinuteRate);

                $discountedTotal = $totalBeforeDiscount;

                if (!empty($couponCode)) {
                    $coupon = Coupon::where('code', $couponCode)->first();
                    $user   = $request->user() ?? Auth::user(); 

                    if ($coupon && $user && $coupon->isValidFor($user, $totalBeforeDiscount)) {
                        $discountedTotal = $coupon->applyDiscount($totalBeforeDiscount);
                        $promoApplied = true;
                    }
                }

                $total = $discountedTotal;
                
                return [
                    'id'     => $type->id,
                    'name'   => $type->name,
                    'avatar' => $type->icon_url,
                    'price'  => $total,
                    'meta'   => [
                        'base_price'     => $totalBeforeDiscount,
                        'coupon_applied' => $promoApplied,
                        'coupon_code'    => $couponCode ?? null,
                    ],
                ];
            });

        return response()->json([
            'status' => true,
            'vehicle_types' => $vehicleTypes,
        ]);
    }


    public function requestTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_latitude' => 'required|numeric|between:-90,90',
            'start_longitude' => 'required|numeric|between:-180,180',
            'end_latitude' => 'required|numeric|between:-90,90',
            'end_longitude' => 'required|numeric|between:-180,180',
            'pickup_name' => 'required|string|max:255',
            'dropoff_name' => 'required|string|max:255',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'estimated_fare' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:cash,card,wallet',

            'distance_meters' => 'nullable|numeric|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'distance_text' => 'nullable|string|max:100',
            'duration_seconds' => 'nullable|numeric|min:0',
            'duration_min' => 'nullable|numeric|min:0',
            'duration_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userLat = $request->start_latitude;
        $userLong = $request->start_longitude;
        $endLat = $request->end_latitude;
        $endLong = $request->end_longitude;
        $pickupName = $request->pickup_name;
        $dropoffName = $request->dropoff_name;
        $vehicleTypeId = $request->vehicle_type_id;

        if (!$userLat || !$userLong || !$endLat || !$endLong) {
            return response()->json([
                'message' => 'Start and end locations (latitude and longitude) are required.',
            ], 400);
        }

        $this->cancelPendingTrip();

        try {
            $userGeohash = $this->geohashService->encode($userLat, $userLong);
            $drivers = $this->getNearbyDrivers($userGeohash, $vehicleTypeId);

            $filteredDrivers = [];
            foreach ($drivers as $driver) {
                $wallet = Wallet::where('user_id', $driver['driver_id'])->first();

                $tripCost = $this->tripCostService->calculateTripCost($vehicleTypeId, $request->estimated_fare);


                if ($wallet && $wallet->balance > $tripCost) {
                    $filteredDrivers[] = $driver;
                }
            }
                
            $tripId = $this->createTrip($userLat, $userLong, $endLat, $endLong, $vehicleTypeId);
            $this->firebase->storeTripInFirebase($tripId, 'pending');

            foreach ($filteredDrivers as $driver) {
                $this->sendDriverNotification($driver, $tripId, $pickupName, $dropoffName);
            }

            return response()->json([
                'message' => 'Trip request sent to available drivers.',
                'status' => true,
                'trip_id' => $tripId
            ]);
        } catch (\Exception $e) {
            \Log::error("Trip request failed: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Internal server error'], 500);
        }
    }


    public function cancelTrip(CancelTripRequest $request, $tripId)
    {


        DB::beginTransaction();
        try {
            $trip = Trip::findOrFail($tripId);

            if ($trip->user_id !== auth()->user()->id) {
                return response()->json(['message' => 'Unauthorized, not the user who created the trip.'], 403);
            }

            if ($trip->status !== 'accepted' && $trip->status !== 'pending') {
                return response()->json(['message' => 'Trip cannot be cancelled at this stage.'], 400);
            }


            $oldStatus = $trip->status;
            $trip->status = 'cancelled';
            $trip->cancelled_by = 'user';
            $trip->cancel_reason_id = $request->cancel_reason_id;
            $trip->cancel_reason_note = $request->cancel_reason_note;
            $trip->cancelled_at = now();
            $trip->save();

            TripHistory::create([
                'trip_id' => $trip->id,
                'user_id' => auth()->user()->id,
            ]);

            if ($oldStatus === 'accepted' && $trip->driver_id) {
                $driverAvailability = DriverAvailability::where('driver_id', $trip->driver_id)->first();
                if ($driverAvailability) {
                    $driverAvailability->is_available = true;
                    $driverAvailability->save();
                }

                // NOTIFICATION: Send cancellation to driver
                $this->FirebaseInstantNotificationService->sendTripCancelledByUser(
                    $trip->driver_id,
                    $trip->id,
                    auth()->user()->name
                );
            }

                $this->firebase->storeTripInFirebase($trip->id, 'cancelled');

            DB::commit();

            return response()->json([
                'message' => 'Trip cancelled successfully.',
                'trip_id' => $trip->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling the trip: ' . $e->getMessage()], 500);
        }
    }


    private function getNearbyDrivers($userGeohash, $vehicleTypeId)
    {
        try {

            $drivers = $this->firebase->getDriversByGeohashPrefix($userGeohash);

            if (empty($drivers)) {
                \Log::info("No drivers found for geohash: " . $userGeohash);
                return [];
            }


            $availableDrivers = [];
            if ($drivers) {
                foreach ($drivers as $driverId => $driverData) {



                    // Check if driver exists in database
                    $driverProfile = DriverProfile::where('user_id', $driverId)->first();
                    if (!$driverProfile) {
                        \Log::warning("Driver {$driverId} found in Firebase but not in database. Removing from Firebase.");
                        $this->firebase->deleteDriverRecord($driverId); // Make sure you implement deleteDriverRecord in FirebaseService
                        continue;
                    }


                    $vehicle = Vehicle::where('driver_profile_id', $driverProfile->id)->first();
                    if (!$vehicle) {
                        \Log::warning("Driver {$driverId} has no vehicle. Removing from Firebase.");
                        $this->firebase->deleteDriverRecord($driverId);
                        continue;
                    }


                    \Log::info("Checking driver: " . $driverId . " for vehicle type: " . $vehicleTypeId);

                    if (
                        isset(
                        $driverData['geohash'],
                        $driverData['lat'],
                        $driverData['long']
                    )
                    ) {

                        if (
                            $driverProfile->is_driver_verified
                            && $driverProfile->driver->is_online
                            && $this->isDriverAvailable($driverId)
                            && $vehicle->vehicle_type_id == $vehicleTypeId
                        ) {

                            $availableDrivers[] = [
                                'driver_id' => $driverId,
                                'geohash' => $driverData['geohash'],
                                'lat' => $driverData['lat'],
                                'long' => $driverData['long']
                            ];
                        }
                    }
                }
            }
            \Log::info("Found " . count($availableDrivers) . " available drivers for geohash: " . $userGeohash);

            return $availableDrivers;

        } catch (\Exception $e) {
            \Log::error("Firebase error: " . $e->getMessage());
            return [];
        }
    }

    private function isDriverAvailable($driverId)
    {
        $driverAvailability = DriverAvailability::where('driver_id', $driverId)->first();
        if (!$driverAvailability) {
            \Log::info("No availability record found for driver: " . $driverId);
            return true;
        }
        return $driverAvailability->is_available;
    }

    private function createTrip($userLat, $userLong, $endLat, $endLong, $vehicleTypeId)
    {
        $pickupName = request()->pickup_name;
        $dropoffName = request()->dropoff_name;
        $estimatedFare = request()->estimated_fare;
        $paymentMethod = request()->payment_method ?? 'cash';


        $userId = auth()->id(); 

        \Log::info("Creating trip with user_id: $userId, pickup_name: $pickupName, dropoff_name: $dropoffName");

        $trip = new Trip();
        $trip->user_id = $userId; 
        $trip->pickup_lat = $userLat;
        $trip->pickup_lng = $userLong;
        $trip->dropoff_lat = $endLat;
        $trip->dropoff_lng = $endLong;
        $trip->pickup_name = $pickupName;
        $trip->dropoff_name = $dropoffName;
        $trip->estimated_fare = $estimatedFare;
        $trip->vehicle_type_id = $vehicleTypeId;
        $trip->payment_method = $paymentMethod; 
        $trip->status = 'pending';
        $trip->save();

        $trip->tripDetails()->create([
            'distance_meters' => request()->distance_meters,
            'distance_km' => request()->distance_km,
            'distance_text' => request()->distance_text,
            'duration_seconds' => request()->duration_seconds,
            'duration_min' => request()->duration_min,
            'duration_text' => request()->duration_text,
        ]);

        \Log::info("Trip created successfully: " . json_encode($trip));


        return $trip->id;
    }

     private function sendDriverNotification($driver, $tripId, $pickupName, $dropoffName)
    {
        try {
            // Use the new notification service
            $this->FirebaseInstantNotificationService->sendTripRequestToDriver(
                $driver['driver_id'],
                $tripId,
                $pickupName,
                $dropoffName
            );

        } catch (\Exception $e) {
            \Log::error("Error sending Firebase notification to driver: " . $e->getMessage());
        }
    }


    protected function calculateGeohashDistance($userGeoHash, $driverGeoHash)
    {
        $user = $this->geohashService->decode($userGeoHash);
        $driver = $this->geohashService->decode($driverGeoHash);

        return $this->distanceService->haversine(
            $user['latitude'],
            $user['longitude'],
            $driver['latitude'],
            $driver['longitude']
        );
    }


    protected function calculateDistance($startLat, $startLong, $endLat, $endLong)
    {
        $earthRadius = 6371;

        $latDiff = deg2rad($endLat - $startLat);
        $longDiff = deg2rad($endLong - $startLong);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($startLat)) * cos(deg2rad($endLat)) *
            sin($longDiff / 2) * sin($longDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in kilometers
        return $earthRadius * $c;
    }


    private function cancelPendingTrip()
    {
        $userId = auth()->id();

        $pendingTrip = Trip::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->first();

        if ($pendingTrip) {
            $pendingTrip->status = 'cancelled';
            $pendingTrip->cancelled_by = 'user';
            $pendingTrip->cancelled_at = now();
            $pendingTrip->save();

            \Log::info("Cancelled the previous pending trip with ID: " . $pendingTrip->id);
        }
    }

    public function getTripAndDriverInfo($tripId)
    {
        try {
            $trip = Trip::with(['driver.driverProfile', 'driver.driverProfile.vehicle'])->findOrFail($tripId);

            if ($trip->user_id != auth()->id()) {
                return response()->json(['message' => 'Unauthorized access to this trip.'], 403);
            }
            return new TripResource($trip);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching the trip: ' . $e->getMessage()], 500);
        }
    }



    public function getDriverByTripId($tripId)
    {
        try {
            $trip = Trip::where('user_id', auth()->id()) // Ensure the user is the one who created the trip
                ->findOrFail($tripId);

            // Get the driver associated with the trip
            $driver = $trip->driver;

            return response()->json([
                'message' => 'Driver fetched successfully.',
                'driver' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching the driver: ' . $e->getMessage()], 500);
        }
    }
}
