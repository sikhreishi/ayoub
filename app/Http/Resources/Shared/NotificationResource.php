<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'title_ar' => $this->title_ar,
            'body_ar' => $this->body_ar,
            'title_en' => $this->title_en,
            'body_en' => $this->body_en,
            'image' => $this->image ? asset($this->image) : null,
            'read' => $this->read,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'avatar' => $this->user->avatar,
            ],
            'sender' => [
                'name' => $this->sender->name,
                'email' => $this->sender->email,
                'phone' => $this->sender->phone,
                'avatar' => $this->sender->avatar,
                'roles' => $this->sender->roles->pluck('name')->toArray(),
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
