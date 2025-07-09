<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RatingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating_id',
        'path',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class);
    }

    public function getPathUrlAttribute(): string
    {
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }
        
        return Storage::disk('s3')->url($this->path);
    }
}