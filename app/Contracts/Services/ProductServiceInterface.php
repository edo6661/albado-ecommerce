<?php

namespace App\Contracts\Services;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator;
    public function getProductById(int $id): ?Product;
    public function createProduct(array $data, array $images = []): Product;
    public function updateProduct(int $id, array $data, array $images = []): Product;
    public function deleteProduct(int $id): bool;
    public function deleteMultipleProducts(array $ids): int;
    public function deleteProductImage(int $productId, int $imageId): bool;
    public function getProductStatistics(): array;
    public function getRecentProducts(int $limit = 10): Collection;
    public function getFilteredProducts(array $filters = []): Collection;
}