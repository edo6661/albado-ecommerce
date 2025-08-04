<?php
namespace App\Services;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Models\Category;
use App\Exceptions\CategoryNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class CategoryService implements CategoryServiceInterface
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}
    public function getCategoryById(int $id): ?Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new CategoryNotFoundException("Kategori dengan ID {$id} tidak ditemukan.");
        }
        return $category;
    }
    public function createCategory(array $data, $image = null): Category
    {
        return DB::transaction(function () use ($data, $image) {
            if ($image) {
                $path = $image->store('categories', 's3');
                $data['image'] = $path;
            }
            return $this->categoryRepository->create($data);
        });
    }
    public function updateCategory(int $id, array $data, $image = null, bool $deleteImage = false): Category
    {
        return DB::transaction(function () use ($id, $data, $image, $deleteImage) {
            $category = $this->getCategoryById($id);
            if ($deleteImage && $category->image) {
                if (Storage::disk('s3')->exists($category->image)) {
                    Storage::disk('s3')->delete($category->image);
                }
                $data['image'] = null;
            }
            if ($image) {
                if ($category->image && Storage::disk('s3')->exists($category->image)) {
                    Storage::disk('s3')->delete($category->image);
                }
                $path = $image->store('categories', 's3');
                $data['image'] = $path;
            }
            $this->categoryRepository->update($category, $data);
            return $category->fresh(['products']);
        });
    }
    public function deleteCategory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = $this->getCategoryById($id);
            if ($category->products()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus kategori yang masih memiliki produk.');
            }
            if ($category->image && Storage::disk('s3')->exists($category->image)) {
                Storage::disk('s3')->delete($category->image);
            }
            return $this->categoryRepository->delete($category);
        });
    }
    public function deleteMultipleCategories(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $deletedCount = 0;
            foreach ($ids as $id) {
                try {
                    $category = $this->getCategoryById($id);
                    if ($category->products()->count() > 0) {
                        continue;
                    }
                    if ($category->image && Storage::disk('s3')->exists($category->image)) {
                        Storage::disk('s3')->delete($category->image);
                    }
                    if ($this->categoryRepository->delete($category)) {
                        $deletedCount++;
                    }
                } catch (CategoryNotFoundException $e) {
                    continue;
                }
            }
            return $deletedCount;
        });
    }
    public function deleteCategoryImage(int $categoryId): bool
    {
        return DB::transaction(function () use ($categoryId) {
            $category = $this->getCategoryById($categoryId);
            if (!$category->image) {
                return false;
            }
            if (Storage::disk('s3')->exists($category->image)) {
                Storage::disk('s3')->delete($category->image);
            }
            return $this->categoryRepository->update($category, ['image' => null]);
        });
    }
    public function getCategoryStatistics(): array
    {
        return $this->categoryRepository->getCategoryStatistics();
    }
    public function getRecentCategories(int $limit = 10): Collection
    {
        return $this->categoryRepository->getRecentCategories($limit);
    }
    public function getFilteredCategories(array $filters = []): Collection
    {
        return $this->categoryRepository->getFilteredCategories($filters);
    }
    public function getCategoryHasManyProducts(int $limit = 10): Collection
    {
        return $this->categoryRepository->getCategoryHasManyProducts($limit);
    }
    public function getCategoryBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->getCategoryBySlug($slug);
    }
    public function getPaginatedCategories(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllPaginated($perPage, $page);
    }
    public function getCursorPaginatedCategories(int $perPage = 15, ?int $cursor = null): array
    {
        $categories = $this->categoryRepository->getAllCursorPaginated($perPage, $cursor);
        $hasNextPage = $categories->count() > $perPage;
        if ($hasNextPage) {
            $categories->pop(); 
        }
        $nextCursor = $hasNextPage && $categories->isNotEmpty() ? $categories->last()->id : null;
        return [
            'data' => $categories,
            'has_next_page' => $hasNextPage,
            'next_cursor' => $nextCursor,
            'per_page' => $perPage
        ];
    }
    public function getFilteredPaginatedCategories(array $filters = [], int $perPage = 15, ?int $cursor = null): array
    {
        $categories = $this->categoryRepository->getFilteredPaginated($filters, $perPage, $cursor);
        $hasNextPage = $categories->count() > $perPage;
        if ($hasNextPage) {
            $categories->pop(); 
        }
        $nextCursor = $hasNextPage && $categories->isNotEmpty() ? $categories->last()->id : null;
        return [
            'data' => $categories,           
            'has_next_page' => $hasNextPage, 
            'next_cursor' => $nextCursor,    
            'per_page' => $perPage,          
            'filters' => $filters            
        ];
    }
     public function getCategoryDetailWithPaginatedProducts(string $slug, int $perPage = 15, ?int $cursor = null): ?Category
    {
        $category = $this->categoryRepository->findBySlug($slug);
        if (!$category) {
            return null;
        }
        $products = $this->categoryRepository->getPaginatedProductsForCategory($category, $perPage, $cursor);
        $hasNextPage = $products->count() > $perPage;
        if ($hasNextPage) {
            $products->pop();
        }
        $nextCursor = $hasNextPage && $products->isNotEmpty() ? $products->last()->id : null;
        $category->paginated_products = [
            'data' => $products,
            'has_next_page' => $hasNextPage,
            'next_cursor' => $nextCursor,
            'per_page' => $perPage
        ];
        return $category;
    }
}
