<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'freelancer_id',
        'category_id',
        'title',
        'description',
        'price',
        'delivery_time',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'delivery_time' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portfolio')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10);

                $this->addMediaConversion('medium')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10);
            });
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Query Scopes
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->whereHas('freelancer', function ($q) use ($minRating) {
            $q->whereHas('reviews', function ($r) use ($minRating) {
                $r->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function averageRating(): float
    {
        return $this->freelancer->averageRating();
    }
}
