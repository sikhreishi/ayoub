<?php

namespace App\Services;

use App\Models\{User, Coupon};

class CouponService
{
    public function applyCoupon(User $user, string $code, float $tripAmount): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['error' => 'Coupon not found.',"status" => 404];
        }

        if (!$coupon->isValidFor($user, $tripAmount)) {
            return ['error' => 'The coupon is invalid or expired.',"status" => 400];
        }

        $newAmount = $coupon->applyDiscount($tripAmount);

        return [
            'success' => true,
            'original_amount' => $tripAmount,
            'discounted_amount' => $newAmount,
            'coupon_id' => $coupon->id,
        ];
    }
    public function recordUsage(User $user, Coupon $coupon): void
    {
        $userCoupon = $coupon->users()->where('user_id', $user->id)->first();
        if ($userCoupon) {
            $userCoupon->pivot->increment('uses');
        } else {
            $coupon->users()->attach($user->id, ['uses' => 1]);
        }
    }
}
