<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $model) {}

    public function findById(int $id): ?Product
    {
        return $this->model->with(['category', 'images'])->find($id);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['category', 'images'])->orderBy('created_at', 'desc')->paginate($perPage);
    }
    public function getProductBySlug(string $slug): ?Product
    {
        return $this->model->with(['category', 'images'])->where('slug', $slug)->first();
    }
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): Collection
    {
        return $this->model->with(['category', 'images'])
            ->where('id', '!=', $productId)
            ->where('category_id', $categoryId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function create(array $data): Product
    {
        return $this->model->create(
            $data
        );
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
    public function getProductStatistics(): array
    {
        $totalProducts = $this->model->count();
        $activeProducts = $this->model->where('is_active', true)->count();
        $inactiveProducts = $this->model->where('is_active', false)->count();
        $outOfStockProducts = $this->model->where('stock', 0)->count();
        $lowStockProducts = $this->model->where('stock', '<=', 10)->where('stock', '>', 0)->count();
        
        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'inactive_products' => $inactiveProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'low_stock_products' => $lowStockProducts,
        ];
    }

    public function getRecentProducts(int $limit = 10): Collection
    {
        return $this->model->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getFilteredProducts(array $filters = []): Collection
    {
        $query = $this->model->with(['category', 'images'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('name', $filters['category']);
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('category', function ($categoryQuery) use ($filters) {
                    $categoryQuery->where('name', 'like', '%' . $filters['search'] . '%');
                });
            });
        }

        return $query->get();
    }
    public function getPaginatedActiveProducts(int $perPage = 12, int $page = 1): LengthAwarePaginator
    {
        return $this->model->with(['category', 'images'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
        public function getFilteredPaginatedProducts(array $filters, int $perPage = 12, int $page = 1): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'images'])
            ->where('is_active', true);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        
        $query->reorder();

        $sortBy = $filters['sort_by'] ?? 'latest';

        switch ($sortBy) {
            case 'price_low':
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

}