<?php

namespace App\Contracts\Services;

use App\Models\Rating;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RatingServiceInterface
{
    public function getRatingById(int $id): ?Rating;
    public function getUserRatingForProduct(int $userId, int $productId): ?Rating;
    public function getProductRatings(int $productId, int $perPage = 10): LengthAwarePaginator;
    public function getUserRatings(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function createRating(array $data, array $images = []): Rating;
    public function updateRating(int $id, array $data, array $images = []): Rating;
    public function deleteRating(int $id): bool;
    public function getProductRatingStats(int $productId): array;
    public function canUserRateProduct(int $userId, int $productId): bool;
}