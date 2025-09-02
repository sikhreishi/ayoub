<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'language' => $this->language,
            'avatar' => $this->avatar
                ? asset('storage/' . $this->avatar)
                : null,
            'gender' => $this->gender,
            'city_id' => $this->city_id,
            'country_id' => $this->country_id,
            'is_online' => (bool) $this->is_online,
            'roles' => $this->roles->pluck('name'),
        ];
    }
}
