<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use App\Services\Firebase\FirebaseService;
use App\Http\Requests\Api\Profile\Driver\DriverUpdateProfileRequest;  // This was correct
use Illuminate\Support\Facades\Hash;

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

    // Step 1: Validate the input data
    $validated = $request->validated();

    // Step 2: Handle Avatar Upload (if provided)
    if ($request->hasFile('avatar')) {
        // Store the avatar file in the 'avatars' folder in 'storage/app/public'
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        // Save the avatar path in the validated data
        $validated['avatar'] = $avatarPath;
    }

    // Step 3: Update the user's profile with validated data
    $user->update($validated);

    return response()->json([
        "message" => "Update is successful",
        "user" => $user, // The updated user data, including the avatar path
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


     public function updatePassword(UserChangePasswordRequest $request)
{
    $user = Auth::user();

    DB::beginTransaction();
    try {
        $user->password = Hash::make($request->input('password'));
        $user->save();

        DB::commit();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
}
