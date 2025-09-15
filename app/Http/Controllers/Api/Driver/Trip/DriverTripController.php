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
use App\Services\Firebase\FirebaseInstantNotificationService;
use App\Models\User;

class DriverTripController extends Controller
{
    protected $firebase;
    protected $distanceService;
    protected $tripCostService;
    protected $FirebaseInstantNotificationService;

    public function __construct(FirebaseService $firebaseService, DistanceService $distanceService,FirebaseInstantNotificationService $firebaseInstantNotificationService)
    {
        $this->distanceService = $distanceService;
        $this->firebase = $firebaseService;
        $this->tripCostService = new TripCostService();
        $this->FirebaseInstantNotificationService = $firebaseInstantNotificationService;

    }


    public function acceptTrip(Request $request, $tripId)
    {

        try {
            $trip = Trip::findOrFail($tripId);

            if (!auth()->user()->hasRole('driver')) {
                return response()->json(['message' => 'Unauthorized, not a driver.'], 403);
            }
            $user = auth()->user()->load('driverAvailability','driverProfile');

            if (!$user->driverProfile || !$user->driverProfile->is_driver_verified) {
                return response()->json(['message' => 'driver not verified'], 403);
            }

            if (!$user->driverAvailability || !$user->driverAvailability->is_available) {
                return response()->json(['message' => 'driver not available'], 400);
            }

            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
          

                $tripCost = $this->tripCostService->calculateTripCost($trip->vehicle_type_id, $trip->estimated_fare);

            if ($wallet->balance < $tripCost) {
                return response()->json(['message' => 'You must have at least 10% of the trip cost in your wallet to accept the trip.'], 422);
            }

            $activeTrip = Trip::where('driver_id', auth()->user()->id)
                ->whereIn('status', ['accepted', 'in_progress'])
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

                // NOTIFICATION: Send to driver (ALL devices)
            $passenger = User::find($trip->user_id);
            $this->FirebaseInstantNotificationService->sendTripAcceptedToDriver(
                auth()->id(), // User ID instead of token
                $trip->id, 
                $passenger->name ?? 'Passenger'
            );

            // NOTIFICATION: Send to passenger (ALL devices)
            $driver = auth()->user()->load('driverProfile');
            $vehicleInfo = $driver->driverProfile->vehicle ? $driver->driverProfile->vehicle->make . ' ' . $driver->vehicle->driverProfile->model : 'vehicle';
            $this->FirebaseInstantNotificationService->sendTripAcceptedToPassenger(
                $trip->user_id, // Passenger user ID
                $trip->id, 
                $driver->name, 
                $vehicleInfo
            );

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
            
             // NOTIFICATION: Wallet deduction to driver (ALL devices)
            $this->FirebaseInstantNotificationService->sendWalletUpdateNotification(
                auth()->id(), // User ID instead of token
                $tripCost, 
                'deduction', 
                $trip->id
            );

            $trip->status = 'in_progress';
            $trip->started_at = Carbon::now();
            $trip->save();

            $driverAvailability = DriverAvailability::where('driver_id', auth()->user()->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = false;
                $driverAvailability->save();
            }
            $this->firebase->storeTripInFirebase($trip->id, 'in_progress');
 // NOTIFICATION: Trip started to driver (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripStartedToDriver(
                auth()->id(), // User ID instead of token
                $trip->id, 
                $tripCost
            );

            // NOTIFICATION: Trip started to passenger (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripStartedToPassenger(
                $trip->user_id, // Passenger user ID
                $trip->id, 
                auth()->user()->name
            );

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

            // NOTIFICATION: Trip cancellation to driver (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripCancelledByDriver(
                auth()->id(), // User ID instead of token
                $trip->id, 
                'You'
            );

            // NOTIFICATION: Trip cancellation to passenger (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripCancelledByDriver(
                $trip->user_id, // Passenger user ID
                $trip->id, 
                'Driver'
            );

                 // NOTIFICATION: Refund if applicable
            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $tripCost = $this->tripCostService->calculateTripCost($trip->vehicle_type_id, $trip->estimated_fare);
            
            // Check if deduction was made and refund if needed
            $deductionTransaction = $wallet->transactions()
                ->where('description', 'like', '%Trip start deduction for Trip ID: ' . $trip->id . '%')
                ->first();
            
            if ($deductionTransaction) {
                $wallet->balance += $tripCost;
                $wallet->transactions()->create([
                    'amount' => $tripCost,
                    'transaction_type' => 'refund',
                    'description' => 'Refund for cancelled Trip ID: ' . $trip->id,
                ]);
                $wallet->save();

                // NOTIFICATION: Refund to driver (ALL devices)
                $this->FirebaseInstantNotificationService->sendTripCancellationRefund(
                    auth()->id(), // User ID instead of token
                    $trip->id, 
                    $tripCost
                );
            }
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
 // Calculate driver earnings (you might want to adjust this logic)
            $driverEarnings = $finalFare * 0.8; // 80% to driver, 20% to platform

            // NOTIFICATION: Trip completion to driver (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripCompletedToDriver(
                auth()->id(), // User ID instead of token
                $trip->id, 
                $finalFare, 
                $driverEarnings
            );

            // NOTIFICATION: Trip completion to passenger (ALL devices)
            $this->FirebaseInstantNotificationService->sendTripCompletedToPassenger(
                $trip->user_id, // Passenger user ID
                $trip->id, 
                $finalFare, 
                auth()->user()->name
            );

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
