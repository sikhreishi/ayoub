<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\Base\AuthBaseController;
use App\Services\Interfaces\IAuthService;
use App\Http\Requests\Api\Auth\UserRegisterRequest;
use App\Http\Requests\Api\Auth\UserLoginRequest;


class UserAuthController extends AuthBaseController
{
    protected $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(UserRegisterRequest  $request)
    {
        try {

            $data = $request->validated();
            $data['role'] = 'user';

            $user = $this->authService->register($data);

            return response()->json([
                'message' => 'Registered successfully. OTP sent to phone.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Register Error: '.$e->getMessage());
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(UserLoginRequest  $request)
    {
        try {
            $data = $request->validated();
            $data['role'] = 'user';

            $user = $this->authService->login($data);
            //  $token = $user->createToken('api-token')->plainTextToken;

            return $this->jsonSuccess([
                'message' => 'OTP sent to your phone.',
                // 'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return $this->jsonError('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Login Error: '.$e->getMessage());
            return $this->jsonError('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }


}
