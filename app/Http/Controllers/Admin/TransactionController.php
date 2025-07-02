<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\TransactionServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\UpdateTransactionStatusRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Exceptions\TransactionNotFoundException;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function __construct(protected TransactionServiceInterface $transactionService)
    {
    }

    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        
        $filters = [
            'status' => $request->get('status'),
            'payment_type' => $request->get('payment_type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'order_id' => $request->get('order_id'),
            'search' => $request->get('search'),
        ];

        $transactions = $this->transactionService->getPaginatedTransactions($perPage, array_filter($filters));
        
        $statusOptions = collect(TransactionStatus::cases())->mapWithKeys(function ($status) {
            return [$status->value => $status->label()];
        })->all();

        $paymentOptions = collect(PaymentType::cases())->mapWithKeys(function ($paymentType) {
            return [$paymentType->value => $paymentType->label()];
        })->all();

        $orderOptions = Order::select('id', 'order_number')
            ->orderBy('created_at', 'desc')
            ->get()
            ->mapWithKeys(function ($order) {
                return [$order->id => $order->order_number];
            })
            ->all();

        return view('admin.transactions.index', compact('transactions', 'statusOptions', 'paymentOptions', 'orderOptions', 'filters'));
    }

    public function show(int $id): View
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            return view('admin.transactions.show', compact('transaction'));
        } catch (TransactionNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            
            $statusOptions = collect(TransactionStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();

            $paymentOptions = collect(PaymentType::cases())->mapWithKeys(function ($paymentType) {
                return [$paymentType->value => $paymentType->label()];
            })->all();

            return view('admin.transactions.edit', compact('transaction', 'statusOptions', 'paymentOptions'));
        } catch (TransactionNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(UpdateTransactionRequest $request, int $id): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            
            $this->transactionService->updateTransaction($id, $validatedData);
            
            return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (TransactionNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(UpdateTransactionStatusRequest $request, int $id): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            
            $this->transactionService->updateTransactionStatus($id, $validatedData['status']);
            
            return redirect()->route('admin.transactions.show', $id)->with('success', 'Status transaksi berhasil diperbarui.');
        } catch (TransactionNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status transaksi: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->transactionService->deleteTransaction($id);
            
            return redirect()->route('admin.transactions.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (TransactionNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus transaksi: ' . $e->getMessage()]);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->transactionService->getTransactionStatistics();
            
            return response()->json(['success' => true, 'data' => $statistics]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'payment_type' => $request->get('payment_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'order_id' => $request->get('order_id'),
            ];

            
            
            
            return response()->json(['success' => true, 'message' => 'Export berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function byOrder(int $orderId): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByOrderId($orderId);
            
            return response()->json(['success' => true, 'data' => $transactions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function byPaymentType(string $paymentType): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByPaymentType($paymentType);
            
            return response()->json(['success' => true, 'data' => $transactions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function exportPdf(Request $request)
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
            return redirect()->back()->with('error', 'Gagal mengexport PDF: ' . $e->getMessage());
        }
    }
}