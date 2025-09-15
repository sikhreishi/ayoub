<?php

namespace App\Http\Controllers\Api\Base;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\IAuthService;
use App\Http\Requests\Api\Auth\VerifyOTPRequest;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ResendOTPRequest;
use App\Models\DriverProfile;
use App\Models\DriverAvailability;
use App\Models\DeviceToken;
use App\Models\Country;

class AuthBaseController
{
    protected $authService;

public function __construct(IAuthService $authService)
{
    $this->authService = $authService;
}

public function verifyOTP(VerifyOTPRequest $request)
{
    $data = $request->validated();
    $user = User::where('phone', $data['phone'])->first();

    if (!$user) {
        return $this->jsonError('User not found', 404);
    }

    // Fetch OTP record
    $otpVerification = OTPVerification::where('user_id', $user->id)
        ->where('type', 'phone')
        ->first();

                  // Store the device token
    if (!$this->authService->checkDeviceTokenExists($data['token'], $user)) {
        return $this->jsonError('Failed to store device token', 500);
    }

    if (!$otpVerification) {
        return $this->jsonError('No OTP record found. Please request a new OTP.', 404);
    }

    if ($otpVerification->attempts >= 5) {
        return $this->jsonError('Maximum OTP attempts reached. Please request a new OTP.', 429);
    }

    $verified = $this->authService->verifyOTP($user, $data);

    if (!$verified) {
        $this->authService->incrementOtpAttempts($user);
        return $this->jsonError('Invalid or expired OTP', 422);
    }

    $DriverProfile = DriverProfile::where('user_id', $user->id)->first();
    $is_driver_verified = $DriverProfile? $DriverProfile->is_driver_verified : false;
    $registration_complete = $DriverProfile ? $DriverProfile->registration_complete : false;

    $token = $user->createToken('api-token')->plainTextToken;
    $country = Country::find($user->country_id);
    $user->country = $country->name_en;
    return $this->jsonSuccess([
        'message' => 'Phone verified',
        'user' => $user,
        'token' => $token,
        'is_driver_verified' => $is_driver_verified,
        'registration_complete' => $registration_complete,

    ]);
}

public function forgetPassword(ForgetPasswordRequest $request)
{
    try {
        $data = $request->validated();

        $sent = $this->authService->forgetPassword($data['phone']);

        if (!$sent) {
            return $this->jsonError('User not found', 404);
        }

        return $this->jsonSuccess(['message' => 'OTP sent to phone']);
    } catch (ValidationException $e) {
        return $this->jsonError('Validation failed', 422, $e->errors());
    } catch (\Exception $e) {
        Log::error('Forget Password Error: '.$e->getMessage());
        return $this->jsonError('Something went wrong', 500, ['error' => $e->getMessage()]);
    }
}

public function logout(Request $request)
{
    try {
        // Get the authenticated user
        $user = $request->user();

        $deviceToken = $request->input('token') ?? $request->header('X-Device-Token');
        if ($deviceToken) {
            DeviceToken::where('user_id', $user->id)
                ->where('token', $deviceToken)
                ->delete();
        }
        

        if ($user->hasRole('driver')) {

            $user->is_online = false;
            $user->save();

            $driverAvailability = DriverAvailability::where('driver_id', $user->id)->first();
            if ($driverAvailability) {
                $driverAvailability->is_available = false;
                $driverAvailability->save();
            }
        }

        $request->user()->currentAccessToken()->delete();

        return $this->jsonSuccess(['message' => 'Logged out successfully']);
    } catch (\Exception $e) {
        Log::error('Logout Error: '.$e->getMessage());
        return $this->jsonError('Something went wrong', 500, ['error' => $e->getMessage()]);
    }
}


public function resetPassword(ResetPasswordRequest $request)
{
    try {
        $data = $request->validated();

        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            return $this->jsonError('User not found', 404);
        }

        // $verified = $this->authService->verifyOTP($user, $data);

        // if (!$verified) {
        //     return $this->jsonError('Invalid OTP', 422);
        // }

        $this->authService->resetPassword($user, $data['password']);

        return $this->jsonSuccess(['message' => 'Password reset successfully']);
    } catch (ValidationException $e) {
        return $this->jsonError('Validation failed', 422, $e->errors());
    } catch (\Exception $e) {
        Log::error('Reset Password Error: '.$e->getMessage());
        return $this->jsonError('Something went wrong', 500, ['error' => $e->getMessage()]);
    }
}

public function resendOTP(ResendOTPRequest $request)
{
    $data = $request->validated();

$sent = $this->authService->forgetPassword($data['phone']);

if (!$sent) {
    return $this->jsonError('User not found', 404);
}

return $this->jsonSuccess([
    'message' => 'OTP resent successfully',
]);
}

protected function jsonError(string $message, int $status = 422, ?array $errors = null)
{
    $response = ['message' => $message];
    if ($errors) {
        $response['errors'] = $errors;
    }
    return response()->json($response, $status);
}

protected function jsonSuccess(array $data, int $status = 200)
{
    return response()->json($data, $status);
}
}