<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->user->name,
                'email' => $this->client->user->email,
            ],
            'service' => $this->when($this->service, [
                'id' => $this->service?->id,
                'title' => $this->service?->title,
                'price' => $this->service?->price,
            ]),
            'freelancer' => $this->when($this->freelancer_id, [
                'id' => $this->freelancer?->id,
                'name' => $this->freelancer?->user->name,
                'title' => $this->freelancer?->title,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
