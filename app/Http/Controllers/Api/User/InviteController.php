<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function getInviteCode(Request $request)
    {
        $user = $request->user();
        if (!$user->invite_code) {
            do {
                $code = strtoupper(Str::random(6));
            } while (\App\Models\User::where('invite_code', $code)->exists());
            $user->invite_code = $code;
            $user->save();
        }
        return response()->json([
            'invite_code' => $user->invite_code
        ]);
    }
}
