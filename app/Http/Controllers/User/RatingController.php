<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Contracts\Services\RatingServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Requests\RatingRequest\StoreRatingRequest;
use App\Http\Requests\RatingRequest\UpdateRatingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class RatingController extends Controller
{
    public function __construct(
        protected RatingServiceInterface $ratingService,
        protected ProductServiceInterface $productService
    ) {}
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 10);
        $ratings = $this->ratingService->getUserRatings(Auth::id(), $perPage);
        return view('user.ratings.index', compact('ratings'));
    }
    public function create(int $productId): View
    {
        $product = $this->productService->getProductById($productId);
        if (!$this->ratingService->canUserRateProduct(Auth::id(), $productId)) {
            abort(403, 'Anda tidak dapat memberikan rating untuk produk ini.');
        }
        return view('user.ratings.create', compact('product'));
    }
    public function store(StoreRatingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $images = $request->file('images', []);
            if ($this->ratingService->hasUserRatedProduct(Auth::id(), $data['product_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah pernah memberikan rating untuk produk ini.'
                ], 403);
            }
            if (!$this->ratingService->canUserRateProduct(Auth::id(), $data['product_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa memberikan rating untuk produk yang sudah Anda beli dan terima.'
                ], 403);
            }
            $rating = $this->ratingService->createRating($data, $images);
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil ditambahkan.',
                'data' => $rating
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show(int $id): View
    {
        $rating = $this->ratingService->getRatingById($id);
        if (!$rating || $rating->user_id !== Auth::id()) {
            abort(404);
        }
        return view('user.ratings.show', compact('rating'));
    }
    public function edit(int $id): View
    {
        $rating = $this->ratingService->getRatingById($id);
        if (!$rating || $rating->user_id !== Auth::id()) {
            abort(404);
        }
        return view('user.ratings.edit', compact('rating'));
    }
    public function update(UpdateRatingRequest $request, int $id): JsonResponse
    {
        try {
            $rating = $this->ratingService->getRatingById($id);
            if (!$rating || $rating->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan.'
                ], 404);
            }
            $data = $request->validated();
            $images = $request->file('images', []);
            $updatedRating = $this->ratingService->updateRating($id, $data, $images);
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil diperbarui.',
                'data' => $updatedRating
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $rating = $this->ratingService->getRatingById($id);
            if (!$rating || $rating->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan.'
                ], 404);
            }
            $this->ratingService->deleteRating($id);
            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function productRatings(int $productId): JsonResponse
    {
        try {
            $perPage = request()->get('per_page', 10);
            $ratings = $this->ratingService->getProductRatings($productId, $perPage);
            $stats = $this->ratingService->getProductRatingStats($productId);
            return response()->json([
                'success' => true,
                'data' => [
                    'ratings' => $ratings,
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}