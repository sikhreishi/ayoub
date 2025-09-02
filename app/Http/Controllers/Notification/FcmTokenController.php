<?php

namespace App\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Models\DeviceToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
//     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
//     $table->string('token')->unique();
//     $table->enum('platform', ['android', 'ios', 'web']);

    public function store(Request $request)
    {

        

        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string',

        ]);
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }
        DeviceToken::updateOrCreate(
            [
                'user_id' => $userId,
                'token' => $request->token,
            ],[
                'platform' => $request->platform ?? 'web',
            ]
        );
        return response()->json([
            'status' => true,
            'message' => 'Device token saved successfully.',
        ]);
    }
}
