<?php
namespace App\Contracts\Repositories;

use App\Models\Rating;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RatingRepositoryInterface
{
    public function findById(int $id): ?Rating;
    public function findByUserAndProduct(int $userId, int $productId): ?Rating;
    public function getProductRatingsPaginated(int $productId, int $perPage = 10): LengthAwarePaginator;
    public function getProductRatingsCursorPaginated(int $productId, int $perPage = 10, ?int $cursor = null): Collection;
    public function getUserRatingsPaginated(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function getUserRatingsCursorPaginated(int $userId, int $perPage = 10, ?int $cursor = null): Collection;
    public function create(array $data): Rating;
    public function update(Rating $rating, array $data): bool;
    public function delete(Rating $rating): bool;
    public function getProductRatingStats(int $productId): array;
    public function getUserRatingStats(int $userId): array;
}