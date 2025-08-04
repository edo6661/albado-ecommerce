<?php
namespace App\Contracts\Services;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
interface CategoryServiceInterface
{
    public function getPaginatedCategories(int $perPage = 15, int $page = 1): LengthAwarePaginator;
    public function getCursorPaginatedCategories(int $perPage = 15, ?int $cursor = null): array;
    public function getFilteredPaginatedCategories(array $filters = [], int $perPage = 15, ?int $cursor = null): array;
    public function getCategoryById(int $id): ?Category;
    public function createCategory(array $data, $image = null): Category;
    public function updateCategory(int $id, array $data, $image = null, bool $deleteImage = false): Category;
    public function deleteCategory(int $id): bool;
    public function deleteMultipleCategories(array $ids): int;
    public function deleteCategoryImage(int $categoryId): bool;
    public function getCategoryStatistics(): array;
    public function getRecentCategories(int $limit = 10): Collection;
    public function getFilteredCategories(array $filters = []): Collection; 
    public function getCategoryHasManyProducts(int $limit = 10): Collection;
    public function getCategoryBySlug(string $slug): ?Category;
    public function getCategoryDetailWithPaginatedProducts(string $slug, int $perPage = 15, ?int $cursor = null): ?Category;

}