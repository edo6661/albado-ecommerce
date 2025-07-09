<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'discount_price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
    
    public function getAverageRatingAttribute(): float
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function getRatingStatsAttribute(): array
    {
        $stats = [];
        for ($i = 1; $i <= 5; $i++) {
            $stats[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $stats;
    }
}
