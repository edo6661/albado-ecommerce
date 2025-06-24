<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Exceptions\ProductNotFoundException;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('admin.products.index', compact('products'));
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
            if (array_key_exists('images', $validatedData)) {
                unset($validatedData['images']);
            }

            $this->productService->updateProduct($id, $validatedData, $images);

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
}