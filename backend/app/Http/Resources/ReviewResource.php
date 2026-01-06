<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'comment' => $this->comment,
            'ticket' => [
                'id' => $this->ticket->id,
                'title' => $this->ticket->title,
            ],
            'reviewer' => [
                'id' => $this->reviewer->id,
                'name' => $this->reviewer->name,
            ],
            'reviewee' => [
                'id' => $this->reviewee->id,
                'name' => $this->reviewee->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
