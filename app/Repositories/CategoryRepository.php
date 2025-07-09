<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(protected Category $model) {}

    public function findById(int $id): ?Category
    {
        return $this->model->with(['products'])->find($id);
    }

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['products'])->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function getCategoryStatistics(): array
    {
        $totalCategories = $this->model->count();
        $categoriesWithProducts = $this->model->has('products')->count();
        $categoriesWithoutProducts = $this->model->doesntHave('products')->count();
        
        return [
            'total_categories' => $totalCategories,
            'categories_with_products' => $categoriesWithProducts,
            'categories_without_products' => $categoriesWithoutProducts,
        ];
    }

    public function getRecentCategories(int $limit = 10): Collection
    {
        return $this->model->with(['products'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getCategoryHasManyProducts(int $limit = 10): Collection
    {
        return $this->model->with(['products'])
            ->whereHas('products')
            ->orderBy('')
            ->limit($limit)
            ->get();
    }
    public function getFilteredCategories(array $filters = []): Collection
    {
        $query = $this->model->with(['products'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->get();
    }
}
