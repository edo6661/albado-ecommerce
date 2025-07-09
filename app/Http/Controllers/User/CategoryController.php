<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService,
        protected ProductServiceInterface $productService
    ) {}

    public function index(): View
    {
        $categories = $this->categoryService->getCategoryHasManyProducts(50);
        
        return view('user.categories.index', compact('categories'));
    }

    public function show(string $slug, Request $request): View
    {
        $category = $this->categoryService->getCategoryBySlug($slug);
        
        if (!$category) {
            abort(404);
        }

        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'latest');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $filters = [
            'is_active' => true,
            'category_id' => $category->id,
            'search' => $search,
            'sort_by' => $sortBy,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ];

        $products = $this->productService->getFilteredPaginatedProducts($filters, $perPage, $page);

        return view('user.categories.show', compact('category', 'products', 'search', 'sortBy', 'minPrice', 'maxPrice'));
    }
}