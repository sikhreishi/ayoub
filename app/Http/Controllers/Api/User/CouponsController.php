<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Services\CouponService;
use Illuminate\Support\Facades\{Auth, Log};

class CouponsController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'trip_amount' => 'required|numeric',
        ]);
        $user = Auth::user();
        $coupon = $this->couponService->applyCoupon($user, $request->coupon_code, $request->trip_amount);
        dd($coupon);
        if (isset($coupon['error'])) {
            return response()->json(['error' => $coupon['error']], 400);
        }
        return response()->json($coupon);
    }
    public function recordUsage(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'trip_amount' => 'required|numeric',
        ]);
        $user = Auth::user();
        $coupon = Coupon::findOrFail($request->coupon_id);
        $tripAmount = $request->trip_amount;
        if (!$coupon->isValidFor($user, $tripAmount)) {
            return response()->json([
                'status' => 400,
                'message' => 'The coupon is invalid or expired.'
            ], 400);
        }
        $coupon = $this->couponService->recordUsage($user, $coupon);
        return response()->json();
    }
}
