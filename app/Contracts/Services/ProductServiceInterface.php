<?php

namespace App\Contracts\Services;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface ProductServiceInterface
{
    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator;
    public function getProductById(int $id): ?Product;
    public function createProduct(array $data, array $images = []): Product;
    public function updateProduct(int $id, array $data, array $images = []): Product;
    public function deleteProduct(int $id): bool;
    public function deleteMultipleProducts(array $ids): int;
}