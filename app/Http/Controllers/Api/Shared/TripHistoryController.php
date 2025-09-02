<?php


namespace App\Http\Controllers\Api\Shared;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\TripHistoryResource;
use App\Models\Trip;

class TripHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 15);

        $query = Trip::whereIn('status', ['completed', 'cancelled'])->orderBy('requested_at', 'desc');

        if ($user->hasRole('driver')) {
            $query->where('driver_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $history = $query->with(['driver', 'user'])->paginate($limit);

        return response()->json([
            'data' => TripHistoryResource::collection($history),
            'page' => $history->currentPage(),
            'total' => $history->total(),
            'last_page' => $history->lastPage(),
            'next_page_url' => $history->nextPageUrl(),
            'prev_page_url' => $history->previousPageUrl(),
        ]);
    }

}
