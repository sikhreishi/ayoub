<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Wallet\{
    DriverWalletController,
    UserWalletController
};
use App\Http\Controllers\Notification\{
    FcmTokenController, 
    NotificationController
};
use App\Http\Controllers\Api\Shared\{
    ContactUsController,
    TripHistoryController,
    TicketController,
    TripReviewController,
    CancelReasonController
};
use App\Http\Controllers\Api\User\{
    InviteController,
    CouponsController
};


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/get-all-notification/page/{page}', [NotificationController::class, 'getAllNotification']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all',    [NotificationController::class, 'markAllAsRead']);
    Route::put('/update-device-token', [FcmTokenController::class, "store"]);
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);
        return response()->json([
            'token' => $token->plainTextToken
        ]);
    });
});

require __DIR__ . '/apiAuth.php';

Route::post('/broadcast-notification', [NotificationController::class, 'sendToAll']);
Route::post('/send-to-user-notification', [NotificationController::class, 'sendToUser']);
Route::prefix('shared')->middleware('auth:sanctum')->group(function () {
    Route::get('/trip-history', [TripHistoryController::class, 'index']);
    Route::get('/invite-code', [InviteController::class, 'getInviteCode']);
    Route::post('/contact-us', [ContactUsController::class, 'store']);
    Route::get('/trips/{trip}/reviews', [TripReviewController::class, 'show']);
    Route::post('/trips/{trip}/reviews', [TripReviewController::class, 'store']);
    Route::get('/cancel-reasons', [CancelReasonController::class, 'index']);

});

Route::post('/broadcast-notification', [NotificationController::class, 'sendToAll']);
Route::post('/send-to-user-notification', [NotificationController::class, 'sendToUser']);


Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::post('/coupons/apply', [CouponsController::class, 'applyCoupon']);
    Route::post('/coupons/record-usage', [CouponsController::class, 'recordUsage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/categories', [TicketController::class, 'categories']);
        Route::get('/stats', [TicketController::class, 'stats']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::post('/{id}/reply', [TicketController::class, 'reply']);
    });

    Route::prefix('driver/wallet')->middleware('auth:sanctum')->group(function () {
        Route::get('/balance', [DriverWalletController::class, 'getWalletBalance']);
        Route::post('/redeem-code', [DriverWalletController::class, 'redeemCode']);
        Route::get('/transactions', [DriverWalletController::class, 'getTransactionHistory']);
        Route::get('/stats', [DriverWalletController::class, 'getWalletStats']);
    });
});


Route::middleware('auth:sanctum')->prefix('user')->group(function () {

    Route::get('/wallet', [UserWalletController::class, 'show']);
     Route::get('wallet/transactions', [UserWalletController::class, 'transactions']);

});