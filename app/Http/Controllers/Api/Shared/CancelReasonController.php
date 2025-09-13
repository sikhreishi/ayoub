<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancelReason;

class CancelReasonController extends Controller
{
    public function index()
    {
        $reasons = CancelReason::where('is_active', true)->get(['id', 'reason_en', 'reason_ar']);
        return response()->json($reasons);
    }
}
