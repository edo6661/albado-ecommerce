<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Contracts\Services\TransactionServiceInterface;
use App\Http\Requests\Api\Admin\Transaction\UpdateTransactionRequest;
use App\Http\Requests\Api\Admin\Transaction\UpdateTransactionStatusRequest;
use App\Http\Requests\Api\Admin\Transaction\ExportPdfRequest;
use App\Exceptions\TransactionNotFoundException;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransactionDetailResource;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;
use App\Http\Requests\Api\Admin\Transaction\TransactionIndexRequest;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class TransactionController extends Controller
{
    public function __construct(protected TransactionServiceInterface $transactionService)
    {
    }
    /**
     * Display a listing of transactions for admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(TransactionIndexRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            $filters = $request->except(['per_page', 'cursor']);
            $result = $this->transactionService->getFilteredCursorPaginatedTransactions($filters, $perPage, $cursor);
            $statusOptions = collect(TransactionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all();
            $paymentOptions = collect(PaymentType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])->all();
            $orderOptions = Order::select('id', 'order_number')->orderBy('created_at', 'desc')->limit(50)->get()->pluck('order_number', 'id')->all();
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diambil',
                'data' => TransactionResource::collection($result['data']),
                'pagination' => [
                    'has_next_page' => (bool) $result['has_next_page'],
                    'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,
                    'per_page' => (int) $result['per_page'],
                    'current_cursor' => $cursor ? (int) $cursor : null
                ],
                'filters' => $result['filters'] ?? [],
                'options' => [ 
                    'status' => $statusOptions,
                    'payment_type' => $paymentOptions,
                    'order' => $orderOptions,
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
                'message' => 'Gagal mengambil data transaksi',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    /**
     * Display the specified transaction
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail transaksi berhasil diambil',
                'data' => new TransactionDetailResource($transaction)
            ]);
        } catch (TransactionNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get transaction for editing
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            $statusOptions = collect(TransactionStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();
            $paymentOptions = collect(PaymentType::cases())->mapWithKeys(function ($paymentType) {
                return [$paymentType->value => $paymentType->label()];
            })->all();
            return response()->json([
                'success' => true,
                'message' => 'Data edit transaksi berhasil diambil',
                'data' => [
                    'transaction' => new TransactionDetailResource($transaction),
                    'status_options' => $statusOptions,
                    'payment_options' => $paymentOptions
                ]
            ]);
        } catch (TransactionNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data edit transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update the specified transaction
     *
     * @param UpdateTransactionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $transaction = $this->transactionService->updateTransaction($id, $validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => new TransactionDetailResource($transaction)
            ]);
        } catch (TransactionNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update transaction status
     *
     * @param UpdateTransactionStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(UpdateTransactionStatusRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $transaction = $this->transactionService->updateTransactionStatus($id, $validatedData['status']);
            return response()->json([
                'success' => true,
                'message' => 'Status transaksi berhasil diperbarui',
                'data' => new TransactionDetailResource($transaction)
            ]);
        } catch (TransactionNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified transaction
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->transactionService->deleteTransaction($id);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (TransactionNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get transaction statistics
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->transactionService->getTransactionStatistics();
            return response()->json([
                'success' => true,
                'message' => 'Statistik transaksi berhasil diambil',
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get filtered transactions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filtered(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'payment_type' => $request->get('payment_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'order_id' => $request->get('order_id'),
                'search' => $request->get('search'),
            ];
            $transactions = $this->transactionService->getFilteredTransactions(array_filter($filters));
            return response()->json([
                'success' => true,
                'message' => 'Transaksi terfilter berhasil diambil',
                'data' => TransactionResource::collection($transactions),
                'meta' => [
                    'total' => $transactions->count(),
                    'filters' => array_filter($filters),
                    'total_amount' => $transactions->where('status', 'settlement')->sum('gross_amount')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil transaksi terfilter',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get transactions by order
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function byOrder(int $orderId): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByOrderId($orderId);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berdasarkan order berhasil diambil',
                'data' => TransactionResource::collection($transactions)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil transaksi berdasarkan order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get transactions by payment type
     *
     * @param string $paymentType
     * @return JsonResponse
     */
    public function byPaymentType(string $paymentType): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByPaymentType($paymentType);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berdasarkan tipe pembayaran berhasil diambil',
                'data' => TransactionResource::collection($transactions)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil transaksi berdasarkan tipe pembayaran',
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
            $statusOptions = collect(TransactionStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();
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
    /**
     * Export transactions to PDF
     *
     * @param ExportPdfRequest $request
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function exportPdf(ExportPdfRequest $request)
    {
        try {
            $filters = $request->validated();
            $transactions = $this->transactionService->getFilteredTransactions(array_filter($filters));
            $exportData = [
                'transactions' => $transactions,
                'filters' => $filters,
                'total_transactions' => $transactions->count(),
                'total_amount' => $transactions->where('status', 'settlement')->sum('gross_amount'),
                'export_date' => now()->format('d/m/Y H:i:s'),
                'status_summary' => $transactions->groupBy('status')->map->count(),
                'payment_type_summary' => $transactions->groupBy('payment_type')->map->count(),
            ];
            $pdf = Pdf::loadView('admin.transactions.export.pdf', $exportData)
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'dpi' => 150,
                        'defaultPaperSize' => 'a4',
                        'chroot' => public_path(),
                    ]);
            $filename = 'laporan-transaksi-' . date('Y-m-d-H-i-s') . '.pdf';
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
}