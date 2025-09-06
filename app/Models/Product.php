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

    /**
     * Cek apakah produk tersedia (aktif dan ada stok)
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    /**
     * Cek apakah stok mencukupi untuk quantity tertentu
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Kurangi stok produk
     */
    public function reduceStock(int $quantity): bool
    {
        if (!$this->hasEnoughStock($quantity)) {
            return false;
        }

        $this->decrement('stock', $quantity);
        return true;
    }

    /**
     * Tambah stok produk
     */
    public function addStock(int $quantity): bool
    {
        $this->increment('stock', $quantity);
        return true;
    }

    /**
     * Cek apakah produk stok rendah (kurang dari atau sama dengan 10)
     */
    public function isLowStock(int $threshold = 10): bool
    {
        return $this->stock <= $threshold && $this->stock > 0;
    }

    /**
     * Cek apakah produk out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Get harga aktual (prioritas discount_price)
     */
    public function getActualPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Cek apakah produk sedang diskon
     */
    public function hasDiscountAttribute(): bool
    {
        return !is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    /**
     * Hitung persentase diskon
     */
    public function getDiscountPercentageAttribute(): float
    {
        if (!$this->has_discount) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
    }
}