<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\Api\CategoryIndexRequest;
use App\Http\Requests\Api\CategoryShowRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}
    public function index(CategoryIndexRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            $filters = $request->getFilters();
            if (isset($validated['search'])) {
                $filters['search'] = $validated['search'];
            }
            if (isset($validated['has_products'])) {
                $hasProducts = $validated['has_products'];
                if (is_string($hasProducts)) {
                    $filters['has_products'] = in_array($hasProducts, ['true', '1'], true);
                } else {
                    $filters['has_products'] = (bool) $hasProducts;
                }
            }
            $result = $this->categoryService->getFilteredPaginatedCategories($filters, $perPage, $cursor);
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diambil',
                'data' => CategoryResource::collection($result['data']), 
                'pagination' => [
                    'has_next_page' => (bool) $result['has_next_page'], 
                    'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,     
                    'per_page' => (int) $result['per_page'],           
                    'current_cursor' => $cursor ? (int) $cursor : null  
                ],
                'filters' => $result['filters'] ?? []           
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function featured(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'limit' => 'integer|min:1|max:20'
            ]);
            $limit = $validated['limit'] ?? 8;
            $categories = $this->categoryService->getCategoryHasManyProducts($limit);
            return response()->json([
                'success' => true,
                'message' => 'Kategori unggulan berhasil diambil',
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori unggulan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function show(CategoryShowRequest $request, string $slug): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            $result =             $category = $this->categoryService->getCategoryDetailWithPaginatedProducts($slug, $perPage, $cursor);
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kategori',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}