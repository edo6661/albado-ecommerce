<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Requests\Api\ProductFilterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ProductNotFoundException;

class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}

    /**
     * Display a listing of products
     *
     * @param ProductFilterRequest $request
     * @return JsonResponse
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 12);
            $page = $request->get('page', 1);
            
            if ($request->hasAnyFilter()) {
                $filters = $request->getFilters();
                $products = $this->productService->getFilteredPaginatedProducts($filters, $perPage, $page);
            } else {
                // Get featured products for home page
                if ($request->get('featured')) {
                    $limit = $request->get('limit', 8);
                    $products = $this->productService->getRecentProducts($limit);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Produk unggulan berhasil diambil',
                        'data' => ProductResource::collection($products)
                    ]);
                }
                
                $products = $this->productService->getPaginatedActiveProducts($perPage, $page);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diambil',
                'data' => ProductResource::collection($products->items()),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get related products
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
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
                'error' => $e->getMessage()
            ], 500);
        }
    }
}