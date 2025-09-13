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

        $statusFilter = $request->get('status');

        $query = Trip::orderBy('requested_at', 'desc');

        $query->whereIn('status', ['completed', 'cancelled']);

        if ($statusFilter && in_array($statusFilter, ['completed', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        if ($user->hasRole('driver')) {
            $query->where('driver_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        $history = $query->with(['driver', 'user'])->paginate($limit);

        return response()->json([
            'data' => TripHistoryResource::collection($history), 
            'page' => $history->currentPage(),  
            'total' => $history->total(),  // Total number of trips
            'last_page' => $history->lastPage(),  // Last page number
            'next_page_url' => $history->nextPageUrl(),  // URL for next page (null if last page)
            'prev_page_url' => $history->previousPageUrl(),  // URL for previous page (null if first page)
        ]);
    }
}
