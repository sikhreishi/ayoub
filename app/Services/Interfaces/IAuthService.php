<?php

namespace App\Services\Interfaces;

use App\Models\User;
use App\Models\OTPVerification;

interface IAuthService
{
    public function register(array $data): User;
    public function completeDriverRegistration(User $user, array $data): void;
    public function login(array $data): User;
    public function verifyOTP(User $user, array $data): bool;
    public function forgetPassword(string $phone): bool;
    public function resetPassword(User $user, string $password);
    public function incrementOtpAttempts(User $user);
    public function getOtpRecord(User $user): ?OTPVerification;
    public function checkDeviceTokenExists(string $token, User $user): bool;

}
