<?php
// app/Http/Controllers/Api/Admin/ProductController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Requests\Api\Admin\Product\StoreProductRequest;
use App\Http\Requests\Api\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Api\Admin\Product\BulkDeleteRequest;
use App\Http\Requests\Api\Admin\Product\ExportPdfRequest;
use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductServiceInterface $productService)
    {
    }

    /**
     * Display a listing of products for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getPaginatedProducts($perPage);
            
            $categoryOptions = $products->pluck('category.name')
                                ->unique()
                                ->mapWithKeys(function ($name) {
                                    return [$name => $name];
                                })
                                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => ProductResource::collection($products->items()),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'category_options' => $categoryOptions
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
     * Get categories for create/edit form
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Category::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug
                    ];
                })
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
     * Store a newly created product
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $images = $request->file('images', []);
            unset($validatedData['images']);
            
            $product = $this->productService->createProduct($validatedData, $images);
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'data' => new ProductDetailResource($product->load(['category', 'images']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            
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
     * Update the specified product
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $images = $request->file('images', []);
            $imagesToDelete = $request->get('delete_images', []);
            
            if (array_key_exists('images', $validatedData)) {
                unset($validatedData['images']);
            }
            if (array_key_exists('delete_images', $validatedData)) {
                unset($validatedData['delete_images']);
            }
            
            $product = $this->productService->updateProduct($id, $validatedData, $images);
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
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
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (ProductNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete products
     *
     * @param BulkDeleteRequest $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDeleteRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $deletedCount = $this->productService->deleteMultipleProducts($validated['ids']);
            $message = $deletedCount > 0 
                ? "Berhasil menghapus {$deletedCount} produk."
                : "Tidak ada produk yang dipilih untuk dihapus.";
                
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
                'message' => 'Gagal menghapus produk secara massal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product image
     *
     * @param int $productId
     * @param int $imageId
     * @return JsonResponse
     */
    public function deleteImage(int $productId, int $imageId): JsonResponse
    {
        try {
            $success = $this->productService->deleteProductImage($productId, $imageId);
            
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
     * Export products to PDF
     *
     * @param ExportPdfRequest $request
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function exportPdf(ExportPdfRequest $request)
    {
        try {
            $filters = $request->validated();

            $products = $this->productService->getFilteredProducts(array_filter($filters));
            
            $exportData = [
                'products' => $products,
                'filters' => $filters,
                'total_products' => $products->count(),
                'total_value' => $products->sum(function($product) {
                    return $product->price * $product->stock;
                }),
                'export_date' => now()->format('d/m/Y H:i:s'),
                'category_summary' => $products->groupBy('category.name')->map->count(),
                'status_summary' => [
                    'active' => $products->where('is_active', true)->count(),
                    'inactive' => $products->where('is_active', false)->count(),
                ],
                'stock_summary' => [
                    'in_stock' => $products->where('stock', '>', 0)->count(),
                    'out_of_stock' => $products->where('stock', 0)->count(),
                    'low_stock' => $products->where('stock', '<=', 10)->where('stock', '>', 0)->count(),
                ],
            ];

            $pdf = Pdf::loadView('admin.products.export.pdf', $exportData)
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'dpi' => 150,
                        'defaultPaperSize' => 'a4',
                        'chroot' => public_path(),
                    ]);

            $filename = 'laporan-produk-' . date('Y-m-d-H-i-s') . '.pdf';
            
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengexport PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product statistics
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->productService->getProductStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistik produk berhasil diambil',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered products for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filtered(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->get('category'),
                'status' => $request->get('status'),
                'search' => $request->get('search'),
            ];

            $products = $this->productService->getFilteredProducts(array_filter($filters));
            
            return response()->json([
                'success' => true,
                'message' => 'Produk terfilter berhasil diambil',
                'data' => ProductResource::collection($products),
                'meta' => [
                    'total' => $products->count(),
                    'filters' => array_filter($filters)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk terfilter',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}