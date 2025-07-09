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

    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllPaginated($perPage);
    }

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
                // Hapus gambar lama jika ada
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

            // Cek jika masih ada produk yang menggunakan kategori ini
            if ($category->products()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus kategori yang masih memiliki produk.');
            }

            // Hapus gambar jika ada
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
                    
                    // Skip jika masih ada produk
                    if ($category->products()->count() > 0) {
                        continue;
                    }
                    
                    // Hapus gambar jika ada
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
}

