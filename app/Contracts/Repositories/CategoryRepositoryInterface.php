<?php
namespace App\Contracts\Repositories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    public function getAllCursorPaginated(int $perPage = 15, ?int $cursor = null): Collection;
    public function getFilteredPaginated(array $filters = [], int $perPage = 15, ?int $cursor = null): Collection;
    public function create(array $data): Category;
    public function update(Category $category, array $data): bool;
    public function delete(Category $category): bool;
    public function getCategoryStatistics(): array;
    public function getRecentCategories(int $limit = 10): Collection;
    public function getFilteredCategories(array $filters = []): Collection; // Deprecated
    public function getCategoryHasManyProducts(int $limit = 10): Collection;
    public function getCategoryBySlug(string $slug): ?Category;
}
