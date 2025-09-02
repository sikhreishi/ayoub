<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\TripReviewRequest;
use App\Models\TripReview;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Shared\TripsReviewsResource;

class TripReviewController extends Controller
{
    public function show(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);

        $reviews = Auth::user()
            ->tripReviews()
            ->where('trip_id', $trip->id)
            ->with(['user', 'trip'])
            ->get();

        return TripsReviewsResource::collection($reviews);
    }
    public function store(TripReviewRequest $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $user = Auth::user();
        $data = $request->validated();  // Get validated data

        // Check if 'rating' and 'comment' exist in $data
        if (!isset($data['rating']) || !isset($data['comment'])) {
            return response()->json(['message' => 'Invalid data'], 400);
        }
        // Create the review
        $review = TripReview::create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'is_driver' => $user->hasRole('user') ? 0 : 1,  // Determine if the user is a passenger (user) or driver
            'comment' => $data['comment'],
            'rating' => $data['rating'],
        ]);

        // If the user is a passenger (user), update the user_note with the comment
        if ($user->hasRole('user')) {
            $trip->TripDetails()->update([
                'user_note' => $data['comment'], // Store the comment in user_note
            ]);
        } else {
            // If the user is a driver, update the driver_note with the comment
            $trip->TripDetails()->update([
                'driver_note' => $data['comment'], // Store the comment in driver_note
            ]);
        }

        return response()->json([
            'message' => 'Thank you for your feedback!',
            'data' => $review
        ], 201);
    }


}
