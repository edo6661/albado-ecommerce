<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Requests\Api\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Api\Admin\Category\UpdateCategoryRequest;
use App\Http\Requests\Api\Admin\Category\BulkDeleteRequest;
use App\Exceptions\CategoryNotFoundException;
use App\Http\Resources\CategoryDetailResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $categoryService)
    {
    }

    /**
     * Display a listing of categories for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $categories = $this->categoryService->getPaginatedCategories($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => CategoryResource::collection($categories->items()),
                'meta' => [
                    'current_page' => $categories->currentPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                    'last_page' => $categories->lastPage(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                ]
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
     * Store a newly created category
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $image = $request->file('image');
            unset($validatedData['image']);
            
            $category = $this->categoryService->createCategory($validatedData, $image);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dibuat',
                'data' => new CategoryDetailResource($category->load(['products']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            
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

    /**
     * Update the specified category
     *
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $image = $request->file('image');
            $deleteImage = $request->get('delete_image', false);
            
            if (array_key_exists('image', $validatedData)) {
                unset($validatedData['image']);
            }
            if (array_key_exists('delete_image', $validatedData)) {
                unset($validatedData['delete_image']);
            }
            
            $category = $this->categoryService->updateCategory($id, $validatedData, $image, $deleteImage);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
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
                'message' => 'Gagal memperbarui kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (CategoryNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete categories
     *
     * @param BulkDeleteRequest $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDeleteRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $deletedCount = $this->categoryService->deleteMultipleCategories($validated['ids']);
            $message = $deletedCount > 0 
                ? "Berhasil menghapus {$deletedCount} kategori."
                : "Tidak ada kategori yang dipilih untuk dihapus.";
                
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted_count' => $deletedCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori secara massal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category image
     *
     * @param int $categoryId
     * @return JsonResponse
     */
    public function deleteImage(int $categoryId): JsonResponse
    {
        try {
            $success = $this->categoryService->deleteCategoryImage($categoryId);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gambar berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category statistics
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->categoryService->getCategoryStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistik kategori berhasil diambil',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered categories for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filtered(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
            ];

            $categories = $this->categoryService->getFilteredCategories(array_filter($filters));
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori terfilter berhasil diambil',
                'data' => CategoryResource::collection($categories),
                'meta' => [
                    'total' => $categories->count(),
                    'filters' => array_filter($filters)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori terfilter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $categories = $this->categoryService->getRecentCategories($limit);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori terbaru berhasil diambil',
                'data' => CategoryResource::collection($categories),
                'meta' => [
                    'total' => $categories->count(),
                    'limit' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori terbaru',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}