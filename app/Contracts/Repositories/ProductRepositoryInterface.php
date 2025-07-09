<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(Product $product, array $data): bool;
    public function delete(Product $product): bool;
    public function getProductStatistics(): array;
    public function getRecentProducts(int $limit = 10): Collection;
    public function getFilteredProducts(array $filters = []): Collection;
    public function getPaginatedActiveProducts(int $perPage = 12, int $page = 1): LengthAwarePaginator;
    public function getFilteredPaginatedProducts(array $filters, int $perPage = 12, int $page = 1): LengthAwarePaginator;
    public function getProductBySlug(string $slug): ?Product;
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): Collection;
}