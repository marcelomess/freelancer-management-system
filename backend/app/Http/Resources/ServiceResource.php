<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'price' => $this->price,
            'delivery_time' => $this->delivery_time,
            'status' => $this->status,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'freelancer' => [
                'id' => $this->freelancer->id,
                'name' => $this->freelancer->user->name,
                'title' => $this->freelancer->title,
                'hourly_rate' => $this->freelancer->hourly_rate,
                'average_rating' => $this->freelancer->averageRating(),
            ],
            'images' => $this->getMedia('portfolio')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'medium' => $media->getUrl('medium'),
                ];
            }),
            'average_rating' => $this->averageRating(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
