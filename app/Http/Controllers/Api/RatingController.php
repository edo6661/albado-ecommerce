<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\RatingServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Http\Resources\RatingResource;
use App\Http\Resources\RatingDetailResource;
use App\Http\Requests\Api\StoreRatingRequest;
use App\Http\Requests\Api\UpdateRatingRequest;
use App\Http\Requests\Api\RatingFilterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct(
        protected RatingServiceInterface $ratingService,
        protected ProductServiceInterface $productService
    ) {}

    /**
     * Display a listing of user's ratings
     *
     * @param RatingFilterRequest $request
     * @return JsonResponse
     */
    public function index(RatingFilterRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 10;
            $cursor = $validated['cursor'] ?? null;

            $result = $this->ratingService->getUserRatingsWithCursor(Auth::id(), $perPage, $cursor);

            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil diambil',
                'data' => RatingResource::collection($result['data']),
                'pagination' => [
                    'has_next_page' => (bool) $result['has_next_page'],
                    'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,
                    'per_page' => (int) $result['per_page'],
                    'current_cursor' => $cursor ? (int) $cursor : null
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
                'message' => 'Gagal mengambil data rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created rating
     *
     * @param StoreRatingRequest $request
     * @return JsonResponse
     */
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
                ], 422);
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
                'message' => 'Rating berhasil ditambahkan',
                'data' => new RatingDetailResource($rating)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified rating
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $rating = $this->ratingService->getRatingById($id);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail rating berhasil diambil',
                'data' => new RatingDetailResource($rating)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified rating
     *
     * @param UpdateRatingRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRatingRequest $request, int $id): JsonResponse
    {
        try {
            $rating = $this->ratingService->getRatingById($id);

            if (!$rating || $rating->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan'
                ], 404);
            }

            $data = $request->validated();
            $images = $request->file('images', []);

            $updatedRating = $this->ratingService->updateRating($id, $data, $images);

            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil diperbarui',
                'data' => new RatingDetailResource($updatedRating)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified rating
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $rating = $this->ratingService->getRatingById($id);

            if (!$rating || $rating->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan'
                ], 404);
            }

            $this->ratingService->deleteRating($id);

            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ratings for a specific product
     *
     * @param int $productId
     * @param RatingFilterRequest $request
     * @return JsonResponse
     */
    public function productRatings(int $productId, RatingFilterRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 10;
            $cursor = $validated['cursor'] ?? null;

            $result = $this->ratingService->getProductRatingsWithCursor($productId, $perPage, $cursor);

            return response()->json([
                'success' => true,
                'message' => 'Rating produk berhasil diambil',
                'data' => [
                    'ratings' => RatingResource::collection($result['data']),
                    'pagination' => [
                        'has_next_page' => (bool) $result['has_next_page'],
                        'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,
                        'per_page' => (int) $result['per_page'],
                        'current_cursor' => $cursor ? (int) $cursor : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil rating produk',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check if user can rate a product
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkRatingEligibility(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        try {
            $productId = $request->get('product_id');
            $userId = Auth::id();

            $canRate = $this->ratingService->canUserRateProduct($userId, $productId);
            $hasRated = $this->ratingService->hasUserRatedProduct($userId, $productId);

            $existingRating = null;
            if ($hasRated) {
                $existingRating = $this->ratingService->getUserRatingForProduct($userId, $productId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status rating berhasil diambil',
                'data' => [
                    'can_rate' => $canRate && !$hasRated,
                    'has_rated' => $hasRated,
                    'existing_rating' => $existingRating ? new RatingResource($existingRating) : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status rating',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's rating for a specific product
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function userProductRating(int $productId): JsonResponse
    {
        try {
            $rating = $this->ratingService->getUserRatingForProduct(Auth::id(), $productId);

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rating pengguna berhasil diambil',
                'data' => new RatingDetailResource($rating)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil rating pengguna',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}