<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\Api\Product\ProductIndexRequest;
use Illuminate\Support\Facades\Log;
class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}
    public function index(ProductIndexRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            $filters = $request->getFilters();
            if (isset($validated['search'])) {
                $filters['search'] = $validated['search'];
            }
            if (isset($validated['category_id'])) {
                $filters['category_id'] = $validated['category_id'];
            }
            if (isset($validated['min_price'])) {
                $filters['min_price'] = $validated['min_price'];
            }
            if (isset($validated['max_price'])) {
                $filters['max_price'] = $validated['max_price'];
            }
            if (isset($validated['sort_by'])) {
                $filters['sort_by'] = $validated['sort_by'];
            }
            if (isset($validated['is_active'])) {
                $isActive = $validated['is_active'];
                if (is_string($isActive)) {
                    $filters['is_active'] = in_array($isActive, ['true', '1'], true);
                } else {
                    $filters['is_active'] = (bool) $isActive;
                }
            }
            if (isset($validated['in_stock'])) {
                $inStock = $validated['in_stock'];
                if (is_string($inStock)) {
                    $filters['in_stock'] = in_array($inStock, ['true', '1'], true);
                } else {
                    $filters['in_stock'] = (bool) $inStock;
                }
            }
            $result = $this->productService->getFilteredPaginatedProductsWithCursor($filters, $perPage, $cursor);
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diambil',
                'data' => ProductResource::collection($result['data']),
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
                'message' => 'Gagal mengambil data produk',
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
            $products = $this->productService->getRecentProducts($limit);
            return response()->json([
                'success' => true,
                'message' => 'Produk unggulan berhasil diambil',
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk unggulan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $product = $this->productService->getProductBySlug($slug);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Detail produk berhasil diambil',
                'data' => new ProductDetailResource($product)
            ]);
        } catch (ProductNotFoundException $e) {
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
                'message' => 'Gagal mengambil detail produk',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function related(int $id, Request $request): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            $limit = $request->get('limit', 4);
            $relatedProducts = $this->productService->getRelatedProducts(
                $product->id,
                $product->category_id,
                $limit
            );
            return response()->json([
                'success' => true,
                'message' => 'Produk terkait berhasil diambil',
                'data' => ProductResource::collection($relatedProducts)
            ]);
        } catch (ProductNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk terkait',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}