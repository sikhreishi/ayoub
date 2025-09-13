<?php
namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverProfile;
use App\Models\User;
use App\Services\AudiLogsService;
use App\Services\Firebase\FirebaseService;
use Illuminate\Support\Facades\Log;

class DriverProfileController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

  public function verify(Request $request, $id)
    {
        $request->validate([
            'is_driver_verified' => 'required|boolean',
            'verification_note' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        if (!$user->hasRole('driver') || !$user->driverProfile) {
            return back()->withErrors(['driver' => 'Driver profile not found.']);
        }

        $old = $user->driverProfile->toArray();

        // Update the verification status and note
        $user->driverProfile->update([
            'is_driver_verified' => $request->is_driver_verified,
            'verification_note' => $request->verification_note,
        ]);

        $now = $user->driverProfile->toArray();

        // If the driver is verified, initialize their record in Firebase
        if ($request->is_driver_verified) {
            $this->firebase->initializeDriverRecord($user->id);
            if($user->is_online && !$user->trips()->where('status', ['accepted','in_progress'])->exists()){
              $user->driverAvailability()->updateOrCreate(
                  ['driver_id' => $user->id],
                  ['is_available' => true]
              );
            }
        } else {
            // If the driver is unverified, delete their record from Firebase
            $this->firebase->deleteDriverRecord($user->id);

            $this->sendDriverUnverifiedNotification($user);
             
              $user->driverAvailability()->updateOrCreate(
                  ['driver_id' => $user->id],
                  ['is_available' => false]
              );
        }

        // Log the update to the driver profile
        AudiLogsService::storeLog('update', 'profile->driverProfile', $user->driverProfile->id, $old, $now);

        return back()->with('status', 'Driver verification updated successfully.');
    }


    private function sendDriverUnverifiedNotification(User $user)
    {
        try {
            // Fetch the Firebase token for the driver from the DeviceToken model
            $deviceToken = $user->deviceTokens()->where('platform', 'firebase')->first();

            if ($deviceToken) {
                $firebaseToken = $deviceToken->token;  // Get the Firebase token from the device token record

                // Send the notification if the Firebase token exists
                $title = "Driver Verification Status";
                $body = "Your driver profile has been unverified. Please log out and contact support if you need assistance.";
                $data = [
                    'action' => 'logout', // Action to trigger on mobile (e.g., logout session)
                ];

                // Send the notification using the FirebaseService
                $this->firebase->sendNotification($firebaseToken, $title, $body, $data);
            } else {
                Log::error("No Firebase token found for driver {$user->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error sending unverified notification to driver {$user->id}: " . $e->getMessage());
        }
    }
}
