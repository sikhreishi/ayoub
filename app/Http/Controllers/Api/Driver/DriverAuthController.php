<?php

namespace App\Http\Controllers\Api\Driver;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\Base\AuthBaseController;
use App\Services\Interfaces\IAuthService;
use App\Http\Requests\Api\Auth\DriverRegisterRequest;
use App\Http\Requests\Api\Auth\DriverLoginRequest;
use App\Http\Requests\Api\Auth\DriverCompleteRegisterRequest;
use App\Models\DriverProfile;
use App\Models\Vehicle\VehicleType;
use Exception;

class DriverAuthController extends AuthBaseController
{
    protected $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function getCarType()
    {
        $types = VehicleType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'icon_url'])
            ->map(fn ($t) => [
                'id'       => $t->id,
                'name'     => $t->name,
                'icon_url' => $t->icon_url ? asset($t->icon_url) : null,
            ])
            ->values();

        return response()->json($types);
    }

    public function register(DriverRegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $data['role'] = 'driver';
            $user = $this->authService->register($data);
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Registered successfully. OTP sent to phone.',
                'user_id' => $user->id,
                'token' => $token
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function completeRegistration(DriverCompleteRegisterRequest $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }
            if (!$user->hasRole('driver') || $user->driverProfile) {
                return response()->json([
                    'message' => 'Invalid registration step or already completed'
                ], 400);
            }


            $data = $request->validated();

            $data['driver_profile'] = $data['driver_profile'] ?? [];
            $data['vehicle'] = $data['vehicle'] ?? [];

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if ($request->hasFile('driver_profile.id_card_front')) {
                $data['driver_profile']['id_card_front'] = $request->file('driver_profile.id_card_front')->store('driver_profiles/id_card', 'public');
            }
            if ($request->hasFile('driver_profile.id_card_back')) {
                $data['driver_profile']['id_card_back'] = $request->file('driver_profile.id_card_back')->store('driver_profiles/id_card', 'public');
            }
            if ($request->hasFile('driver_profile.license_front')) {
                $data['driver_profile']['license_front'] = $request->file('driver_profile.license_front')->store('driver_profiles/license', 'public');
            }
            if ($request->hasFile('driver_profile.license_back')) {
                $data['driver_profile']['license_back'] = $request->file('driver_profile.license_back')->store('driver_profiles/license', 'public');
            }
            if ($request->hasFile('driver_profile.vehicle_license_front')) {
                $data['driver_profile']['vehicle_license_front'] = $request->file('driver_profile.vehicle_license_front')->store('driver_profiles/vehicle_license', 'public');
            }
            if ($request->hasFile('driver_profile.vehicle_license_back')) {
                $data['driver_profile']['vehicle_license_back'] = $request->file('driver_profile.vehicle_license_back')->store('driver_profiles/vehicle_license', 'public');
            }

            if ($request->hasFile('driver_profile.interior_front_seats')) {
                $data['driver_profile']['interior_front_seats'] = $request->file('driver_profile.interior_front_seats')->store('driver_profiles/in_vehicle', 'public');
            }
            if ($request->hasFile('driver_profile.interior_back_seats')) {
                $data['driver_profile']['interior_back_seats'] = $request->file('driver_profile.interior_back_seats')->store('driver_profiles/in_vehicle', 'public');
            }

            if ($request->hasFile('driver_profile.exterior_front_side')) {
                $data['driver_profile']['exterior_front_side'] = $request->file('driver_profile.exterior_front_side')->store('driver_profiles/out_vehicle', 'public');
            }
            if ($request->hasFile('driver_profile.exterior_back_side')) {
                $data['driver_profile']['exterior_back_side'] = $request->file('driver_profile.exterior_back_side')->store('driver_profiles/out_vehicle', 'public');
            }

            if ($request->hasFile('vehicle.image_url')) {
                $data['vehicle']['image_url'] = $request->file('vehicle.image_url')->store('vehicles/images', 'public');
            }

            $this->authService->completeDriverRegistration($user, $data);

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Registration completed successfully. Waiting for admin approval.',
                'user_id' => $user->id
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Complete Register Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getVehicleTypes()
    {
        try {
            $vehicleTypes = VehicleType::where('is_active', true)
                ->select('id', 'name', 'icon_url')
                ->get();

            return response()->json([
                'vehicle_types' => $vehicleTypes
            ], 200);

        } catch (Exception $e) {
            Log::error('Get Vehicle Types Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch vehicle types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function login(DriverLoginRequest $request)
    {
        try {
            $data = $request->validated();
            $data['role'] = 'driver';

            $user = $this->authService->login($data);

            if (!$user) {
                return $this->jsonError('Invalid credentials', 401, ['error' => 'Phone or password is incorrect']);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            $DriverProfile = DriverProfile::where('user_id', $user->id)->first();
            $is_driver_verified = $DriverProfile ? $DriverProfile->is_driver_verified : false;
            $registration_complete = $DriverProfile ? $DriverProfile->registration_complete : false;

            return $this->jsonSuccess([
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'is_driver_verified' => $is_driver_verified,
                'registration_complete' => $registration_complete
            ]);
        } catch (ValidationException $e) {
            return $this->jsonError('Validation failed', 422, $e->errors());
        } catch (Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return $this->jsonError('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }



}
