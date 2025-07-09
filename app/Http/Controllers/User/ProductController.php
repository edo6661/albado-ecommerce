<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\RatingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService,
        protected CategoryServiceInterface $categoryService,
        protected CartServiceInterface $cartService,
        protected RatingServiceInterface $ratingService
    ) {}

   public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $categoryId = $request->get('category');
        $sortBy = $request->get('sort_by', 'latest');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $filters = [
            'is_active' => true,
            'search' => $search,
            'category_id' => $categoryId,
            'sort_by' => $sortBy,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ];

        $products = $this->productService->getFilteredPaginatedProducts($filters, $perPage, $page);
        $categories = $this->categoryService->getCategoryHasManyProducts(50);

        $products->getCollection()->transform(function ($product) {
            try {
                $ratingService = app(RatingServiceInterface::class);
                $product->rating_stats = $ratingService->getProductRatingStats($product->id);
            } catch (\Exception $e) {
                $product->rating_stats = [
                    'average_rating' => 0,
                    'total_ratings' => 0,
                    'rating_distribution' => []
                ];
            }
            return $product;
        });
        

        return view('user.products.index', compact('products', 'categories', 'search', 'categoryId', 'sortBy', 'minPrice', 'maxPrice'));
    }
    public function show(string $slug): View
    {
        $product = $this->productService->getProductBySlug($slug);
        
        if (!$product || !$product->is_active) {
            abort(404);
        }

        $relatedProducts = $this->productService->getRelatedProducts($product->id, $product->category_id, 4);

        
        $ratingStats = $this->ratingService->getProductRatingStats($product->id);
        $ratings = $this->ratingService->getProductRatings($product->id, 5); 
        
        $userRating = null;
        $canUserRate = false;
        
        if (Auth::check()) {
            $userRating = $this->ratingService->getUserRatingForProduct(Auth::id(), $product->id);
            $canUserRate = $this->ratingService->canUserRateProduct(Auth::id(), $product->id);
        }

        return view('user.products.show', compact(
            'product', 
            'relatedProducts', 
            'ratingStats', 
            'ratings', 
            'userRating', 
            'canUserRate'
        ));
    }


}