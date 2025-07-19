<?php
// app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CategoryNotFoundException;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}

    /**
     * Display a listing of categories with products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 8);
            $categories = $this->categoryService->getCategoryHasManyProducts($limit);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diambil',
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryBySlug($slug);
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail kategori berhasil diambil',
                'data' => new CategoryDetailResource($category)
            ]);
        } catch (CategoryNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}