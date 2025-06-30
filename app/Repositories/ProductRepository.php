<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Utils\StringHelper;
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
}