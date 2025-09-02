<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripsReviewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'is_driver' => $this->is_driver,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],

            'trip' => [
                'id' => $this->trip?->id,
                'pickup_location' => $this->trip?->pickup_location,
                'dropoff_location' => $this->trip?->dropoff_location,
            ],

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
