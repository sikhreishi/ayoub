<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\Trip\UserTripController;
use App\Http\Controllers\Api\Driver\Trip\DriverTripController;
use App\Http\Controllers\Api\Driver\{
    DriverAuthController,
    DriverProfileController
};
use App\Http\Controllers\Api\User\{
    LocationController,
    UserProfileController,
    UserLocationController,
    UserAuthController
};
use App\Http\Controllers\Api\Shared\{
    TripReviewController,
};

Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('verify-otp', [UserAuthController::class, 'verifyOTP']);
    Route::post('forget-password', [UserAuthController::class, 'forgetPassword']);
    Route::post('resend-otp', [UserAuthController::class, 'resendOTP']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
});

Route::prefix('driver')->group(function () {
    Route::post('register', [DriverAuthController::class, 'register']);
    Route::post('login', [DriverAuthController::class, 'login']);
    Route::post('verify-otp', [DriverAuthController::class, 'verifyOTP']);
    Route::post('forget-password', [DriverAuthController::class, 'forgetPassword']);
    Route::post('resend-otp', [DriverAuthController::class, 'resendOTP']);
    Route::post('reset-password', [DriverAuthController::class, 'resetPassword']);
});


Route::middleware(['auth:sanctum', 'role:user'])->prefix('user')->group(function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::post('/location/update', [LocationController::class, 'store']);
    Route::post('/trip-reviews', [TripReviewController::class, 'store']);
    Route::get('get/{id}', [DriverProfileController::class, 'getDriverById']);

    Route::delete('delete', [UserProfileController::class, 'destroy']);
    Route::put("update", [UserProfileController::class, "update"]);

    Route::prefix('trip')->group(function () {
        Route::post('vehicle-types', [UserTripController::class, 'fetchVehicleTypes']);
        Route::post('request-trip', [UserTripController::class, 'requestTrip']);
        Route::get('{tripId}', [UserTripController::class, 'getTripAndDriverInfo']);
        Route::post('cancel/{tripId}', [UserTripController::class, 'cancelTrip']);
        Route::get('driver/{tripId}', [UserTripController::class, 'getDriverByTripId']);
    });

    Route::post('/update-location', [UserLocationController::class, 'updateLocation']);
    Route::post('/online-status', [UserLocationController::class, 'updateOnlineStatus']);

});

Route::middleware(['auth:sanctum', 'role:driver'])->prefix('driver')->group(function () {
    Route::post('complete-registration', [DriverAuthController::class, 'completeRegistration']);
    Route::post('logout', [DriverAuthController::class, 'logout']);
    Route::post('/trip-reviews', [TripReviewController::class, 'store']);
    Route::get('get/{id}', [DriverProfileController::class, 'getDriverById']);
    Route::delete('delete', [DriverProfileController::class, 'destroy']);
    Route::put("update", [UserProfileController::class, "update"]);

    Route::prefix('trip')->group(function () {
        Route::get('{tripId}', [DriverTripController::class, 'getDriverTrip']);
        Route::post('accept/{tripId}', [DriverTripController::class, 'acceptTrip']);
        Route::post('start/{tripId}', [DriverTripController::class, 'startTrip']);
        Route::post('end/{tripId}', [DriverTripController::class, 'endTrip']);
        Route::post('cancel/{tripId}', [DriverTripController::class, 'cancelTrip']);
    });
});
