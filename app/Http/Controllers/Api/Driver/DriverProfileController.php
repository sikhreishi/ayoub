<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use App\Http\Requests\api\Profile\Driver\DriverUpdateProfileRequest;
use App\Services\Firebase\FirebaseService;

class DriverProfileController extends Controller
{

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function index()
    {
        //
    }
    public function show(User $user)
    {
        //
    }

    public function getDriverById($id)
    {
        try {
            // Retrieve the driver from the local database
            $driver = User::find($id);

            if (!$driver) {
                return response()->json([
                    'message' => 'Driver not found'
                ], 404);
            }

            $firebaseDriver = $this->firebaseService->getDriverLocationFromFirebase($driver->id);

            if ($firebaseDriver) {

                $driver->current_lat = $firebaseDriver['lat'];
                $driver->current_lng = $firebaseDriver['lng'];
                $driver->geohash = $firebaseDriver['geohash'] ?? null;
                $driver->location_updated_at = now();
                $driver->save();
            }

            return response()->json([
                'message' => 'Driver found successfully',
                'driver' => $driver
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(DriverUpdateProfileRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());
        return response()->json([
            "message" => "update is Success",
            "user" => $user
        ]);
    }
    public function destroy()
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'Account deleted successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
