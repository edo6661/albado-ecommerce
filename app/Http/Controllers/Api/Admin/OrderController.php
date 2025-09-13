<?php
// app/Http/Controllers/Api/Admin/OrderController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\OrderServiceInterface;
use App\Http\Requests\Api\Admin\Order\UpdateOrderRequest;
use App\Http\Requests\Api\Admin\Order\UpdateOrderStatusRequest;
use App\Http\Requests\Api\Admin\Order\ExportPdfRequest;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\OrderCannotBeCancelledException;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;
use App\Enums\OrderStatus;
use App\Http\Requests\Api\Admin\Order\OrderIndexRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    /**
     * Display a listing of orders for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(OrderIndexRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            $filters = $validated;

            $result = $this->orderService->getFilteredCursorPaginatedOrders($filters, $perPage, $cursor);

            $statusOptions = collect(OrderStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();

            $userOptions = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function ($user) {
                    return [$user->id => $user->name . ' (' . $user->email . ')'];
                })
                ->all();

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => OrderResource::collection($result['data']),
                'pagination' => [
                    'has_next_page' => (bool) $result['has_next_page'],
                    'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,
                    'per_page' => (int) $result['per_page'],
                    'current_cursor' => $cursor ? (int) $cursor : null,
                ],
                'filters' => [
                    'options' => [
                        'status' => $statusOptions,
                        'users' => $userOptions,
                    ],
                    'applied' => $result['filters'] ?? []
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Admin Order Index Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pesanan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified order
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil diambil',
                'data' => new OrderDetailResource($order)
            ]);
        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order data for edit form
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            
            $statusOptions = collect(OrderStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();

            return response()->json([
                'success' => true,
                'message' => 'Data edit pesanan berhasil diambil',
                'data' => [
                    'order' => new OrderDetailResource($order),
                    'status_options' => $statusOptions
                ]
            ]);
        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data edit pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified order
     *
     * @param UpdateOrderRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            $order = $this->orderService->updateOrder($id, $validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui',
                'data' => new OrderDetailResource($order)
            ]);
        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     *
     * @param UpdateOrderStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            $order = $this->orderService->updateOrderStatus($id, $validatedData['status']);
            
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui',
                'data' => new OrderDetailResource($order)
            ]);
        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->cancelOrder($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'data' => new OrderDetailResource($order)
            ]);
        } catch (OrderNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (OrderCannotBeCancelledException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
 * Export orders to PDF
 *
 * @param ExportPdfRequest $request
 * @return \Illuminate\Http\Response|JsonResponse
 */
    public function exportPdf(ExportPdfRequest $request)
    {
        try {
            $filters = $request->validated();

            // Get all orders without pagination for export
            $orders = $this->orderService->getFilteredOrders(array_filter($filters));
            
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data pesanan untuk diexport'
                ], 404);
            }
            
            // Tambahkan data tambahan untuk laporan
            $exportData = [
                'orders' => $orders,
                'filters' => array_filter($filters),
                'total_orders' => $orders->count(),
                'total_amount' => $orders->sum('total'),
                'export_date' => now()->format('d/m/Y H:i:s'),
                'status_summary' => $orders->groupBy('status')->map->count(),
            ];

            $pdf = Pdf::loadView('admin.orders.export.pdf', $exportData)
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'dpi' => 150,
                        'defaultPaperSize' => 'a4',
                        'chroot' => public_path(),
                    ]);

            $filename = 'laporan-pesanan-' . date('Y-m-d-H-i-s') . '.pdf';
            
            // Return PDF sebagai binary data langsung untuk download
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Access-Control-Expose-Headers', 'Content-Disposition')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengexport PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->orderService->getOrderStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistik pesanan berhasil diambil',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered orders for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filtered(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'user_id' => $request->get('user_id'),
                'search' => $request->get('search'),
            ];

            $orders = $this->orderService->getFilteredOrders(array_filter($filters));
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan terfilter berhasil diambil',
                'data' => OrderResource::collection($orders),
                'meta' => [
                    'total' => $orders->count(),
                    'filters' => array_filter($filters)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pesanan terfilter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users for filter options
     *
     * @return JsonResponse
     */
    public function users(): JsonResponse
    {
        try {
            $users = User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diambil',
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'display_name' => $user->name . ' (' . $user->email . ')'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status options
     *
     * @return JsonResponse
     */
    public function statusOptions(): JsonResponse
    {
        try {
            $statusOptions = collect(OrderStatus::cases())->map(function ($status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label()
                ];
            })->values();
            
            return response()->json([
                'success' => true,
                'message' => 'Opsi status berhasil diambil',
                'data' => $statusOptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil opsi status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}