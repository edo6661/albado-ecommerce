<?php

namespace App\Services;

use App\Contracts\Repositories\RatingRepositoryInterface;
use App\Contracts\Services\RatingServiceInterface;
use App\Models\Rating;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RatingService implements RatingServiceInterface
{
    public function __construct(protected RatingRepositoryInterface $ratingRepository) {}

    public function getRatingById(int $id): ?Rating
    {
        return $this->ratingRepository->findById($id);
    }

    public function getUserRatingForProduct(int $userId, int $productId): ?Rating
    {
        return $this->ratingRepository->findByUserAndProduct($userId, $productId);
    }

    public function getProductRatings(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->ratingRepository->getProductRatingsPaginated($productId, $perPage);
    }

    public function getUserRatings(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->ratingRepository->getUserRatingsPaginated($userId, $perPage);
    }

    public function createRating(array $data, array $images = []): Rating
    {
        return DB::transaction(function () use ($data, $images) {
            $rating = $this->ratingRepository->create($data);

            if (!empty($images)) {
                $imageData = [];
                foreach ($images as $index => $imageFile) {
                    $path = $imageFile->store('ratings', 's3');
                    $imageData[] = [
                        'rating_id' => $rating->id,
                        'path' => $path,
                        'order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                $rating->images()->insert($imageData);
            }

            return $rating->fresh(['user', 'product', 'images']);
        });
    }

    public function updateRating(int $id, array $data, array $images = []): Rating
    {
        return DB::transaction(function () use ($id, $data, $images) {
            $rating = $this->getRatingById($id);
            
            if (!$rating) {
                throw new \Exception("Rating dengan ID {$id} tidak ditemukan.");
            }
            
            $this->ratingRepository->update($rating, $data);
            
            if (!empty($images)) {
                
                foreach ($rating->images as $oldImage) {
                    Storage::disk('s3')->delete($oldImage->path);
                    $oldImage->delete();
                }
                
                
                $imageData = [];
                foreach ($images as $index => $imageFile) {
                    $path = $imageFile->store('ratings', 's3');
                    $imageData[] = [
                        'rating_id' => $rating->id,
                        'path' => $path,
                        'order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $rating->images()->insert($imageData);
            }
            
            return $rating->fresh(['user', 'product', 'images']);
        });
    }

    public function deleteRating(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $rating = $this->getRatingById($id);
            
            if (!$rating) {
                throw new \Exception("Rating dengan ID {$id} tidak ditemukan.");
            }

            
            foreach ($rating->images as $image) {
                if (Storage::disk('s3')->exists($image->path)) {
                    Storage::disk('s3')->delete($image->path);
                }
            }
            
            return $this->ratingRepository->delete($rating);
        });
    }

    public function getProductRatingStats(int $productId): array
    {
        return $this->ratingRepository->getProductRatingStats($productId);
    }

    public function canUserRateProduct(int $userId, int $productId): bool
    {
        
        $existingRating = $this->getUserRatingForProduct($userId, $productId);
        if ($existingRating) {
            return false;
        }
        $hasPurchased = OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'delivered'); 
        })->where('product_id', $productId)->exists();

        return $hasPurchased;
    }
    public function hasUserRatedProduct(int $userId, int $productId): bool
    {
        return $this->ratingRepository->findByUserAndProduct($userId, $productId) !== null;
    }
}