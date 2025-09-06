<?php
namespace App\Repositories;
use App\Contracts\Repositories\RatingRepositoryInterface;
use App\Models\Rating;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RatingRepository implements RatingRepositoryInterface
{
    public function __construct(protected Rating $model) {}

    public function findById(int $id): ?Rating
    {
        return $this->model->with(['user', 'product', 'images'])->find($id);
    }

    public function findByUserAndProduct(int $userId, int $productId): ?Rating
    {
        return $this->model->with(['user', 'product', 'images'])
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function getProductRatingsPaginated(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['user', 'images'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getProductRatingsCursorPaginated(int $productId, int $perPage = 10, ?int $cursor = null): Collection
    {
        $query = $this->model->with(['user', 'images'])
            ->where('product_id', $productId)
            ->orderBy('id', 'desc');

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        return $query->limit($perPage + 1)->get();
    }

    public function getUserRatingsPaginated(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['product', 'images'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUserRatingsCursorPaginated(int $userId, int $perPage = 10, ?int $cursor = null): Collection
    {
        $query = $this->model->with(['product', 'images'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc'); 

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        return $query->limit($perPage + 1)->get();
    }

    public function create(array $data): Rating
    {
        return $this->model->create($data);
    }

    public function update(Rating $rating, array $data): bool
    {
        return $rating->update($data);
    }

    public function delete(Rating $rating): bool
    {
        return $rating->delete();
    }

    public function getProductRatingStats(int $productId): array
    {
        $stats = [];
        $totalRatings = $this->model->where('product_id', $productId)->count();

        for ($i = 1; $i <= 5; $i++) {
            $count = $this->model->where('product_id', $productId)
                ->where('rating', $i)
                ->count();
            $stats[$i] = [
                'count' => $count,
                'percentage' => $totalRatings > 0 ? round(($count / $totalRatings) * 100, 1) : 0
            ];
        }

        $averageRating = $this->model->where('product_id', $productId)->avg('rating');

        return [
            'average_rating' => $averageRating ? round($averageRating, 1) : 0,
            'total_ratings' => $totalRatings,
            'rating_distribution' => $stats
        ];
    }

    public function getUserRatingStats(int $userId): array
    {
        $stats = [];
        $totalRatings = $this->model->where('user_id', $userId)->count();

        for ($i = 1; $i <= 5; $i++) {
            $count = $this->model->where('user_id', $userId)
                ->where('rating', $i)
                ->count();
            $stats[$i] = [
                'count' => $count,
                'percentage' => $totalRatings > 0 ? round(($count / $totalRatings) * 100, 1) : 0
            ];
        }

        $averageRating = $this->model->where('user_id', $userId)->avg('rating');

        return [
            'average_rating' => $averageRating ? round($averageRating, 1) : 0,
            'total_ratings' => $totalRatings,
            'rating_distribution' => $stats
        ];
    }
}