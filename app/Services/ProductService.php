<?php

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ProductService implements ProductServiceInterface
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getAllPaginated($perPage);
    }

    public function getProductById(int $id): ?Product
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            throw new ProductNotFoundException("Produk dengan ID {$id} tidak ditemukan.");
        }
        return $product;
    }

    public function createProduct(array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($data, $images) {
            
            $product = $this->productRepository->create($data);

            
            if (!empty($images)) {
                $imageData = [];
                foreach ($images as $index => $imageFile) {
                    $path = $imageFile->store('products', 's3');
                    $imageData[] = [
                        'product_id' => $product->id,
                        'path' => $path,
                        'order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                $product->images()->insert($imageData);
            }

            return $product;
        });
    }

    public function updateProduct(int $id, array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($id, $data, $images) {
            $product = $this->getProductById($id);
            
            $this->productRepository->update($product, $data);
            
            
            if (!empty($images)) {
                
                foreach ($product->images as $oldImage) {
                    Storage::disk('s3')->delete($oldImage->path); 
                    $oldImage->delete();
                }
                
                $imageData = [];
                foreach ($images as $index => $imageFile) {
                    $path = $imageFile->store('products', 's3'); 
                    $imageData[] = [
                        'product_id' => $product->id,
                        'path' => $path,
                        'order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $product->images()->insert($imageData);
            }
            
            return $product->fresh(['category', 'images']);
        });
    }

    public function deleteProduct(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = $this->getProductById($id);

            
            foreach ($product->images as $image) {
                if (Storage::disk('s3')->exists($image->path)) {
                    Storage::disk('s3')->delete($image->path);
                }
            }
            
            return $this->productRepository->delete($product);
        });
    }

    
    public function deleteMultipleProducts(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $deletedCount = 0;
            
            foreach ($ids as $id) {
                try {
                    $product = $this->getProductById($id);
                    
                    
                    foreach ($product->images as $image) {
                        if (Storage::disk('s3')->exists($image->path)) {
                            Storage::disk('s3')->delete($image->path);
                        }
                    }
                    
                    if ($this->productRepository->delete($product)) {
                        $deletedCount++;
                    }
                } catch (ProductNotFoundException $e) {
                    
                    continue;
                }
            }
            
            return $deletedCount;
        });
    }
    public function deleteProductImage(int $productId, int $imageId): bool
    {
        return DB::transaction(function () use ($productId, $imageId) {
            $product = $this->getProductById($productId);
            $image = $product->images()->find($imageId);
            
            if (!$image) {
                return false;
            }
            
            if (Storage::disk('s3')->exists($image->path)) {
                Storage::disk('s3')->delete($image->path);
            }
            
            return $image->delete();
        });
    }
    public function getProductStatistics(): array
    {
        return $this->productRepository->getProductStatistics();
    }

    public function getRecentProducts(int $limit = 10): Collection
    {
        return $this->productRepository->getRecentProducts($limit);
    }
    public function getFilteredProducts(array $filters = []): Collection
    {
        return $this->productRepository->getFilteredProducts($filters);
    }
    public function getPaginatedActiveProducts(int $perPage = 12, int $page = 1): LengthAwarePaginator
    {
        return $this->productRepository->getPaginatedActiveProducts($perPage, $page);
    }
}