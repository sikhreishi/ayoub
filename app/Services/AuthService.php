<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\IAuthService;
use App\Models\DriverAvailability;
use App\Models\DeviceToken;

use App\Models\Trip;
use App\Models\Wallet;



class AuthService implements IAuthService
{

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create the user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'avatar' => $data['avatar'] ?? null,
                'country_id' => $data['country_id'] ?? 1,
                'city_id' => $data['city_id'] ?? 1
            ]);

            // Create the address if provided
            if (isset($data['street'], $data['city_id'], $data['district_id'])) {
                $user->addresses()->create([
                    'street' => $data['street'],
                    'city_id' => $data['city_id'],
                    'district_id' => $data['district_id'],
                    'label' => $data['label'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                    'type' => 'home', // Default type
                ]);
            }

            // Assign role ('user' or 'driver')
            $user->assignRole($data['role']);

            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->balance = $data['initial_balance'] ?? 0;
            $wallet->save();


            // if ($data['role'] === 'driver' && isset($data['driver_profile'])) {
            //     $driverProfile = $user->driverProfile()->create([
            //         'id_card_front' => $data['driver_profile']['id_card_front'] ?? null,
            //         'id_card_back' => $data['driver_profile']['id_card_back'] ?? null,
            //         'license_front' => $data['driver_profile']['license_front'] ?? null,
            //         'license_back' => $data['driver_profile']['license_back'] ?? null,
            //         'vehicle_license_front' => $data['driver_profile']['vehicle_license_front'] ?? null,
            //         'vehicle_license_back' => $data['driver_profile']['vehicle_license_back'] ?? null,
            //         'interior_front_seats' => $data['driver_profile']['interior_front_seats'] ?? null,
            //         'interior_back_seats' => $data['driver_profile']['interior_back_seats'] ?? null,
            //         'exterior_back_side' => $data['driver_profile']['exterior_back_side'] ?? null,
            //         'exterior_front_side' => $data['driver_profile']['exterior_front_side'] ?? null,
            //     ]);


            //     DriverAvailability::create([
            //         'driver_id' => $user->id,
            //         'latitude' => 0.0,
            //         'longitude' => 0.0,
            //         'geohash' => '',
            //         'is_available' => false, // Default to available
            //         'last_ping' => now(),
            //     ]);


            //     if (isset($data['vehicle'])) {
            //         $vehicleData = $data['vehicle'];

            //         $vehicle = $driverProfile->vehicle()->create([
            //             'driver_profile_id' => $driverProfile->id,
            //             'make' => $vehicleData['make'] ?? null,
            //             'model' => $vehicleData['model'] ?? null,
            //             'year' => $vehicleData['year'] ?? null,
            //             'color' => $vehicleData['color'] ?? null,
            //             'license_plate' => $vehicleData['license_plate'] ?? null,
            //             'vehicle_type_id' => $vehicleData['vehicle_type_id'],  // <- Use this FK here
            //             'seats' => $vehicleData['seats'] ?? 4,
            //             'image_url' => $vehicleData['image_url'] ?? null,
            //         ]);
            //         $driverProfile->update(['vehicle_id' => $vehicle->id]);
            //     }
            // }



            $otp = $this->createOtpCode($user, 'phone', 10);

            // Store the device token
            $this->storeDeviceToken($data['token'], $data['platform'] ?? 'web', $user);


            return $user;
        });
    }

    public function completeDriverRegistration(User $user, array $data): void
    {

        $otpVerification = OTPVerification::where('user_id', $user->id)
            ->where('type', 'phone')
            ->first();
        if ($otpVerification->verified === false) {
            // OTP is not verified
            throw ValidationException::withMessages(['otp' => 'OTP is not verified']);
        }
        DB::transaction(function () use ($user, $data) {
            if ($user->hasRole('driver') && isset($data['driver_profile'])) {
                $driverProfile = $user->driverProfile()->create([
                    'id_card_front' => $data['driver_profile']['id_card_front'] ?? null,
                    'id_card_back' => $data['driver_profile']['id_card_back'] ?? null,
                    'license_front' => $data['driver_profile']['license_front'] ?? null,
                    'license_back' => $data['driver_profile']['license_back'] ?? null,
                    'vehicle_license_front' => $data['driver_profile']['vehicle_license_front'] ?? null,
                    'vehicle_license_back' => $data['driver_profile']['vehicle_license_back'] ?? null,
                    'interior_front_seats' => $data['driver_profile']['interior_front_seats'] ?? null,
                    'interior_back_seats' => $data['driver_profile']['interior_back_seats'] ?? null,
                    'exterior_back_side' => $data['driver_profile']['exterior_back_side'] ?? null,
                    'exterior_front_side' => $data['driver_profile']['exterior_front_side'] ?? null,
                    'registration_complete' => true,
                ]);

                DriverAvailability::create([
                    'driver_id' => $user->id,
                    'latitude' => 0.0,
                    'longitude' => 0.0,
                    'geohash' => '',
                    'is_available' => false, // Default to available
                    'last_ping' => now(),
                ]);


                if (isset($data['vehicle'])) {
                    $vehicleData = $data['vehicle'];

                    $vehicle = $driverProfile->vehicle()->create([
                        'driver_profile_id' => $driverProfile->id,
                        'make' => $vehicleData['make'] ?? null,
                        'model' => $vehicleData['model'] ?? null,
                        'year' => $vehicleData['year'] ?? null,
                        'color' => $vehicleData['color'] ?? null,
                        'license_plate' => $vehicleData['license_plate'] ?? null,
                        'vehicle_type_id' => $vehicleData['vehicle_type_id'],  // <- Use this FK here
                        'seats' => $vehicleData['seats'] ?? 4,
                        'image_url' => $vehicleData['image_url'] ?? null,
                    ]);
                    $driverProfile->update(['vehicle_id' => $vehicle->id]);

                }
            }

        });
    }

    public function login(array $data): User
    {
        $phone = $data['phone'];
        $password = $data['password'];
        $role = $data['role'] ?? 'user';

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages(['phone' => ucfirst($role) . ' not found']);
        }

        if ($role === 'driver') {
            if (!Hash::check($password, $user->password)) {
                throw ValidationException::withMessages(['password' => 'Invalid credentials']);
            }

            if (!$user->hasRole('driver')) {
                throw ValidationException::withMessages(['role' => 'Invalid role']);
            }

            $otpVerification = OTPVerification::where('user_id', $user->id)
                ->where('type', 'phone')
                ->first();

            if (!$otpVerification || $otpVerification->verified === false) {
                throw ValidationException::withMessages(['otp' => 'OTP is not verified']);
            }

            if (!$user->driverProfile) {
                throw ValidationException::withMessages(['driver' => 'Driver profile not found']);
            }

            $this->storeDeviceToken($data['token'], $data['platform'] ?? 'web', $user);
        } elseif ($role === 'user') {
            $this->createOtpCode($user, 'phone', 10);
            // TODO: Send OTP via SMS or notification
        } else {
            throw ValidationException::withMessages(['role' => 'Invalid role']);
        }

        return $user;
    }



    public function verifyOTP(User $user, array $data): bool
    {
        $otpCode = $data['otp'];

        $otpVerification = OTPVerification::where('user_id', $user->id)
            ->where('type', 'phone')
            ->first();

        if (
            !$otpVerification
            || $otpVerification->code != $otpCode
            || ($otpVerification->expires_at && $otpVerification->expires_at->isPast())
            || $otpVerification->attempts >= 5 // Maximum attempts reached
        ) {
            return false;
        }
        $user->is_online = true;
        $user->save();

        if ($user->hasRole('driver')) {
            // Check if the driver has an active trip
            $activeTrip = Trip::where('driver_id', $user->id)
                ->whereIn('status', ['accepted', 'in_progress'])
                ->first();



            // If the driver is not in any active trip, set availability to true
            $driverAvailability = DriverAvailability::where('driver_id', $user->id)->first();
            if ($driverAvailability) {
                if (!$activeTrip) {
                    $driverAvailability->is_available = true; // Driver is available if not in an active trip
                }
                $driverAvailability->save();
            }


        }

        $otpVerification->code = ''; // Clear the OTP code
        $otpVerification->verified = true;
        $otpVerification->attempts = 0;
        $otpVerification->save();

        // Store the device token
        $this->storeDeviceToken($data['token'], $data['platform'] ?? 'web', $user);


        // Optionally regenerate OTP code or delete OTP record here
        return true;
    }


    public function forgetPassword(string $phone): bool
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return false;
        }

        $otp = $this->createOtpCode($user, 'phone', 10);


        // TODO: Send OTP via SMS or notification here

        return true;
    }

    /**
     * Reset password after OTP verification.
     */
    public function resetPassword(User $user, string $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }


    public function incrementOtpAttempts(User $user)
    {
        $otpVerification = OTPVerification::where('user_id', $user->id)
            ->where('type', 'phone')
            ->first();

        if ($otpVerification) {
            $otpVerification->increment('attempts');
        }
    }

    public function getOtpRecord(User $user): ?OTPVerification
    {
        return OTPVerification::where('user_id', $user->id)
            ->where('type', 'phone')
            ->first();
    }


    private function createOtpCode(User $user, string $type = 'phone', int $ttlMinutes = 10): int
    {
        $otp = $this->generateOtpCode();

        $otp = "123456";
        OTPVerification::updateOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            [
                'code' => $otp,
                'expires_at' => now()->addMinutes($ttlMinutes),
                'verified' => false,
                'attempts' => 0,
            ]
        );

        // Optional: send SMS or notification here

        return $otp;
    }

    private function generateOtpCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function storeDeviceToken(string $token, string $platform = 'web', $user)
    {
        // Check if the token is available
        $isTokenAvailable = $this->checkDeviceTokenExists($token, $user);

        if (!$isTokenAvailable) {
            throw new \Exception('Token already exists for another user.');
        }

        // If the token is available, store or update it
        DeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token' => $token,
            ],
            [
                'platform' => $platform,
            ]
        );

        return true;
    }


    public function checkDeviceTokenExists(string $token, $user): bool
    {
        $userId = $user ? $user->id : Auth::id();

        if (!$userId) {
            return false;
        }

        // Check if the token already exists for another user
        $existingToken = DeviceToken::where('token', $token)->first();

        if ($existingToken && $existingToken->user_id !== $userId) {
            // Token exists for another user
            return false; // Token is already associated with another user
        }

        // Token is either not found or it's the same user
        return true; // Token is available for this user
    }

}
