<?php

// CategoryController.php
namespace App\Http\Controllers\Admin;

use App\Contracts\Services\CategoryServiceInterface;
use App\Http\Controllers\Controller;
use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $categoryService)
    {
    }

    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $categories = $this->categoryService->getPaginatedCategories($perPage);
        
        return view('admin.category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.category.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            $image = $request->file('image');
            unset($validatedData['image']); 
            
            $this->categoryService->createCategory($validatedData, $image);
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal membuat kategori: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): View
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            return view('admin.category.show', compact('category'));
        } catch (CategoryNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            return view('admin.category.edit', compact('category'));
        } catch (CategoryNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
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
            
            $this->categoryService->updateCategory($id, $validatedData, $image, $deleteImage);
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
        } catch (CategoryNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui kategori: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->categoryService->deleteCategory($id);
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (CategoryNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus kategori: ' . $e->getMessage()]);
        }
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:categories,id'
            ]);
            
            $deletedCount = $this->categoryService->deleteMultipleCategories($validated['ids']);
            $message = $deletedCount > 0 
                ? "Berhasil menghapus {$deletedCount} kategori."
                : "Tidak ada kategori yang dipilih untuk dihapus.";
                
            return redirect()->route('admin.categories.index')->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors(['error' => 'Data ID tidak valid.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus kategori secara massal: ' . $e->getMessage()]);
        }
    }

    public function deleteImage(int $categoryId): JsonResponse
    {
        try {
            $this->categoryService->deleteCategoryImage($categoryId);
            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}