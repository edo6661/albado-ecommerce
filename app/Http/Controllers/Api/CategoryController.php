<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryDetailResource;
use App\Http\Resources\PaginatedCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CategoryNotFoundException;
use Illuminate\Validation\Rule;
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:50',  
                'cursor' => 'integer|min:1',           
                'search' => 'string|max:255',
                'has_products' => 'boolean'
            ]);
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;  
            $filters = [];
            if (isset($validated['search'])) {
                $filters['search'] = $validated['search'];
            }
            if (isset($validated['has_products'])) {
                $filters['has_products'] = $validated['has_products'];
            }
            $result = $this->categoryService->getFilteredPaginatedCategories($filters, $perPage, $cursor);
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diambil',
                'data' => CategoryResource::collection($result['data']), 
                'pagination' => [
                    'has_next_page' => $result['has_next_page'], 
                    'next_cursor' => $result['next_cursor'],     
                    'per_page' => $result['per_page'],           
                    'current_cursor' => $cursor                  
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
    public function indexPaginated(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:50',
                'page' => 'integer|min:1'
            ]);
            $perPage = $validated['per_page'] ?? 15;
            $page = $validated['page'] ?? 1;
            $categories = $this->categoryService->getPaginatedCategories($perPage, $page);
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diambil',
                'data' => CategoryResource::collection($categories),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                    'has_next_page' => $categories->hasMorePages(),
                    'has_prev_page' => $categories->currentPage() > 1
                ]
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
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}