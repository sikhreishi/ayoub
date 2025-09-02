<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();

        $tripData = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,
            'pickup_lat' => $this->pickup_lat,
            'pickup_lng' => $this->pickup_lng,
            'pickup_name' => $this->pickup_name,
            'dropoff_lat' => $this->dropoff_lat,
            'dropoff_lng' => $this->dropoff_lng,
            'dropoff_name' => $this->dropoff_name,
            'requested_at' => $this->requested_at ? $this->requested_at->format('Y-m-d H:i:s') : 'N/A',
            'accepted_at' => $this->accepted_at ? $this->accepted_at->format('Y-m-d H:i:s') : 'N/A',
            'started_at' => $this->started_at ? $this->started_at->format('Y-m-d H:i:s') : 'N/A',
            'completed_at' => $this->completed_at ? $this->completed_at->format('Y-m-d H:i:s') : 'N/A',
            'driver_accept_lat' => $this->driver_accept_lat ?? 'N/A',
            'driver_accept_lng' => $this->driver_accept_lng ?? 'N/A',
            'status' => $this->status ?? 'N/A',
            'estimated_fare' => $this->estimated_fare ?? 'N/A',
            'final_fare' => $this->final_fare ?? 'N/A',
            'payment_status' => $this->payment_status ?? 'N/A',
            'payment_method' => $this->payment_method ?? 'N/A',
            'cancelled_at' => $this->cancelled_at ? $this->cancelled_at->format('Y-m-d H:i:s') : 'N/A',
            'cancelled_by' => $this->cancelled_by ?? 'N/A',
            'vehicle_type_id' => $this->vehicle_type_id ?? 'N/A',

            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : 'N/A',
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];

        if ($user->hasRole('driver')) {
            return array_merge($tripData, [
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'avatar' => $this->user->avatar ? env('APP_URL') . $this->user->avatar : 'N/A',
                ],
            ]);
        }

        return array_merge($tripData, [
            'driver' => $this->driver ? [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'email' => $this->driver->email,
                'avatar' => $this->driver->avatar ? env('APP_URL') . $this->driver->avatar : 'N/A',
            ] : null,
        ]);
    }
}
