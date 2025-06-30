<?php
namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Exceptions\ProductNotFoundException;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ProductServiceInterface $productService)
    {
    }

    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $products = $this->productService->getPaginatedProducts($perPage);
        $categoryOptions = $products->pluck('category.name')
                            ->unique()
                            ->mapWithKeys(function ($name) {
                                return [$name => $name];
                            })
                            ->all();
        return view('admin.products.index', compact('products', 'categoryOptions'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            $images = $request->file('images', []);
            unset($validatedData['images']); 
            
            $this->productService->createProduct($validatedData, $images);
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal membuat produk: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): View
    {
        try {
            $product = $this->productService->getProductById($id);
            return view('admin.products.show', compact('product'));
        } catch (ProductNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        try {
            $product = $this->productService->getProductById($id);
            $categories = Category::all();
            return view('admin.products.edit', compact('product', 'categories'));
        } catch (ProductNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
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
            
            $this->productService->updateProduct($id, $validatedData, $images, $imagesToDelete);
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (ProductNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->productService->deleteProduct($id);
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (ProductNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:products,id'
            ]);
            
            $deletedCount = $this->productService->deleteMultipleProducts($validated['ids']);
            $message = $deletedCount > 0 
                ? "Berhasil menghapus {$deletedCount} produk."
                : "Tidak ada produk yang dipilih untuk dihapus.";
                
            return redirect()->route('admin.products.index')->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors(['error' => 'Data ID tidak valid.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk secara massal: ' . $e->getMessage()]);
        }
    }

    public function deleteImage(int $productId, int $imageId): JsonResponse
    {
        try {
            $this->productService->deleteProductImage($productId, $imageId);
            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    public function exportPdf(Request $request)
    {
        try {
            $filters = [
                'category' => $request->get('category'),
                'status' => $request->get('status'),
                'search' => $request->get('search'),
            ];

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
            return redirect()->back()->with('error', 'Gagal mengexport PDF: ' . $e->getMessage());
        }
    }
}