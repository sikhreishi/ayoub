<?php
namespace App\Http\Controllers\Api\Driver\Trip;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use Illuminate\Support\Facades\Validator;
use App\Services\Firebase\FirebaseService;
use App\Models\Vehicle\VehicleType;
use Carbon\Carbon;
use App\Services\Location\DistanceService;
use App\Models\DriverAvailability;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use App\Models\TripHistory;
use App\Services\TripCostService;

class DriverTripController extends Controller
{
    protected $firebase;
    protected $distanceService;
    protected $tripCostService;

    public function __construct(FirebaseService $firebaseService, DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
        $this->firebase = $firebaseService;
        $this->tripCostService = new TripCostService();
    }


    public function acceptTrip(Request $request, $tripId)
    {
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $trip = Trip::findOrFail($tripId);

            if (!auth()->user()->hasRole('driver')) {
                return response()->json(['message' => 'Unauthorized, not a driver.'], 403);
            }

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
          

                $tripCost = $this->tripCostService->calculateTripCost($trip->vehicle_type_id, $trip->estimated_fare);

            if ($wallet->balance < $tripCost) {
                return response()->json(['message' => 'You must have at least 10% of the trip cost in your wallet to accept the trip.'], 422);
            }

            $activeTrip = Trip::where('driver_id', auth()->user()->id)
                ->whereIn('status', ['accepted', 'in-progress'])
                ->first();

            if ($activeTrip) {
                return response()->json([
                    'message' => 'You are already assigned to another active trip.'
                ], 400);
            }

            if ($trip->status !== 'pending') {
                return response()->json(['message' => 'Trip is no longer available to accept.'], 400);
            }

            $driverId = auth()->user()->id;
            $driverInfo = $this->firebase->getDriverLocationFromFirebase($driverId);

                $trip->driver_accept_lat = $driverInfo['lat'];
                $trip->driver_accept_lng = $driverInfo['lng'];
                $trip->accepted_at = Carbon::now();

            $trip->status = 'accepted';

            $trip->driver_id = $driverId;
            $trip->save();

            $driverAvailability = DriverAvailability::where('driver_id', auth()->user()->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = false;
                $driverAvailability->save();
            }

            $this->firebase->storeTripInFirebase($trip->id, 'accepted');

            return response()->json([
                'message' => 'Trip accepted successfully.',
                'trip' => $trip
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error accepting the trip: ' . $e->getMessage()], 500);
        }
    }

    public function startTrip(Request $request, $tripId)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $trip = Trip::findOrFail($tripId);

            if (!auth()->user()->hasRole('driver')) {
                return response()->json(['message' => 'Unauthorized, not a driver.'], 403);
            }

            if ($trip->status !== 'accepted') {
                return response()->json(['message' => 'Trip cannot be started.'], 400);
            }

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $tripCost = $this->tripCostService->calculateTripCost($trip->vehicle_type_id, $trip->estimated_fare);

            if ($wallet->balance < $tripCost) {
                return response()->json(['message' => 'Insufficient balance to start the trip.'], 422);
            }

            $wallet->balance -= $tripCost;
            $wallet->save();

            $trip->status = 'in_progress';
            $trip->started_at = Carbon::now();
            $trip->save();

            $driverAvailability = DriverAvailability::where('driver_id', auth()->user()->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = false;
                $driverAvailability->save();
            }
            $this->firebase->storeTripInFirebase($trip->id, 'in_progress');

            DB::commit();

            return response()->json([
                'message' => 'Trip started successfully.',
                'trip' => $trip
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error starting the trip: ' . $e->getMessage()], 500);
        }
    }

    public function cancelTrip(Request $request, $tripId)
    {
        DB::beginTransaction();
        try {
            $trip = Trip::findOrFail($tripId);

            if (!auth()->user()->hasRole('driver')) {
                return response()->json(['message' => 'Unauthorized, not a driver.'], 403);
            }

            if ($trip->status !== 'accepted') {
                return response()->json(['message' => 'Trip cannot be canceled at this stage.'], 400);
            }

            $trip->status = 'cancelled';
            $trip->cancelled_by = 'driver';
            $trip->cancelled_at = now();
            $trip->save();

            TripHistory::create([
                'trip_id' => $trip->id,
                'user_id' => $trip->user_id,
            ]);

            $driverAvailability = DriverAvailability::where('driver_id', auth()->user()->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = true;
                $driverAvailability->save();
            }

            $this->firebase->storeTripInFirebase($trip->id, 'cancelled');

            DB::commit();

            return response()->json([
                'message' => 'Trip cancelled successfully.',
                'trip' => $trip
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error canceling the trip: ' . $e->getMessage()], 500);
        }
    }



    public function endTrip(Request $request, $tripId)
    {
        $validator = Validator::make($request->all(), [
            'final_fare' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $trip = Trip::findOrFail($tripId);

            if (!auth()->user()->hasRole('driver')) {
                return response()->json(['message' => 'Unauthorized, not a driver.'], 403);
            }

            if ($trip->status !== 'in_progress') {
                return response()->json(['message' => 'Trip cannot be ended at this stage.'], 400);
            }

            $vehicleType = VehicleType::findOrFail($trip->vehicle_type_id);
            $startLat = $trip->pickup_lat;
            $startLong = $trip->pickup_lng;
            $endLat = $trip->dropoff_lat;
            $endLong = $trip->dropoff_lng;

            $distance = $this->distanceService->haversine($startLat, $startLong, $endLat, $endLong);
            $currentHour = Carbon::now()->hour;
            $isNightTime = ($currentHour >= 18 || $currentHour < 6);

            $perKmRate = $isNightTime ? $vehicleType->night_per_km_rate : $vehicleType->day_per_km_rate;
            $perMinuteRate = $isNightTime ? $vehicleType->night_per_minute_rate : $vehicleType->day_per_minute_rate;

            $tripDuration = 10;

            $totalBeforeDiscount = $vehicleType->start_fare + ($distance * $perKmRate) + ($tripDuration * $perMinuteRate);

            $promoCodeDiscount = 0;
            if ($trip->promo_code && $trip->promo_code === "DISCOUNT10") {
                $promoCodeDiscount = 10;
            }

            $finalFare = $totalBeforeDiscount - ($totalBeforeDiscount * $promoCodeDiscount) / 100;
            $finalFare = $trip->estimated_fare; 
            $trip->status = 'completed';
            $trip->completed_at = Carbon::now();
            $trip->final_fare = $finalFare;
            $trip->save();

            TripHistory::create([
                'trip_id' => $trip->id,
                'user_id' => $trip->user_id,
            ]);

            $driverAvailability = DriverAvailability::where('driver_id', auth()->user()->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = true;
                $driverAvailability->save();
            }

            $this->firebase->storeTripInFirebase($trip->id, 'completed');

            DB::commit();

            return response()->json([
                'message' => 'Trip ended successfully.',
                'trip' => $trip,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Error ending the trip: ' . $e->getMessage()], 500);
        }
    }


    public function getDriverTrip(Request $request, $tripId)
    {
        try {
            $trip = Trip::where('id', $tripId)
                ->where('driver_id', auth()->id())
                ->first();

            if (!$trip) {
                return response()->json(['message' => 'Trip not found or not assigned to the driver.'], 404);
            }

            return response()->json([
                'message' => 'Trip fetched successfully.',
                'trip' => $trip
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching the trip: ' . $e->getMessage()], 500);
        }
    }


}
