<?php

namespace App\Http\Controllers\Admin\Trips;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use Yajra\DataTables\DataTables;
use App\Services\Firebase\FirebaseService;

class TripController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function index(Request $request)
    {
        $status = $request->input('status');
        return view('admin.trips.index', compact('status'));
    }
    public function getTripData()
    {
        $trips = Trip::with(['user', 'driver']);
        return DataTables::of($trips)
            ->addColumn('user_name', function ($trip) {
                return $trip->user ? $trip->user->name : '-';
            })
            ->addColumn('driver_name', function ($trip) {
                return $trip->driver ? $trip->driver->name : '-';
            })

            ->make(true);
    }
    public function getTripsByStatus(Request $request)
    {
        $status = $request->input('status');
        $trips = Trip::with(['user', 'driver'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            });

        return DataTables::of($trips)
            ->addColumn('user_name', function ($trip) {
                return $trip->user ? $trip->user->name : '-';
            })
            ->addColumn('driver_name', function ($trip) {
                return $trip->driver ? $trip->driver->name : '-';
            })
            ->addColumn('requested_at', function ($trip) {
                return $trip->requested_at ? $trip->requested_at->format('d-m-Y H:i:s') : '-';
            })
            ->addColumn('completed_at', function ($trip) {
                return $trip->completed_at ? $trip->completed_at->format('d-m-Y H:i:s') : '-';
            })
            ->addColumn('actions', function ($trip) {
                $actions = '';

                // Show driver location button for in_progress trips with assigned driver
                if ($trip->status === 'in_progress' && $trip->driver_id) {
                    $actions .= '<button class="btn btn-sm btn-info me-1 show-driver-location"
                                    data-trip-id="' . $trip->id . '"
                                    data-bs-toggle="modal"
                                    data-bs-target="#driverLocationModal">
                                    <i class="material-icons-outlined">location_on</i> Driver Location
                                </button>';
                }

                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function showTripDetails()
    {
        $originLat = 40.7128;
        $originLng = -74.0060;
        $destinationLat = 40.730610;
        $destinationLng = -73.935242;
        $details = $this->firebaseService->getTripDetails($originLat, $originLng, $destinationLat, $destinationLng);
        return response()->json($details);
    }
    public function getDriverLocation($tripId)
    {
        try {
            $trip = Trip::with(['driver'])->findOrFail($tripId);

            if ($trip->status !== 'in_progress') {
                return response()->json(['error' => 'Trip is not in progress status'], 400);
            }

            if (!$trip->driver_id) {
                return response()->json(['error' => 'No driver assigned to this trip'], 400);
            }

            // Get driver location from Firebase
            $driverLocation = $this->firebaseService->getDriverLocationFromFirebase($trip->driver_id);

            if (!$driverLocation) {
                return response()->json(['error' => 'Driver location not found'], 404);
            }

            return response()->json([
                'success' => true,
                'driver' => [
                    'id' => $trip->driver->id,
                    'name' => $trip->driver->name,
                    'phone' => $trip->driver->phone,
                    'location' => $driverLocation
                ],
                'trip' => [
                    'id' => $trip->id,
                    'pickup_lat' => $trip->pickup_lat,
                    'pickup_lng' => $trip->pickup_lng,
                    'pickup_name' => $trip->pickup_name,
                    'dropoff_lat' => $trip->dropoff_lat,
                    'dropoff_lng' => $trip->dropoff_lng,
                    'dropoff_name' => $trip->dropoff_name,
                    'status' => $trip->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching driver location: ' . $e->getMessage()], 500);
        }
    }
}
