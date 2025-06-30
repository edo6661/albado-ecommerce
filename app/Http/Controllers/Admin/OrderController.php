<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\OrderServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\OrderCannotBeCancelledException;
use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        
        $filters = [
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'user_id' => $request->get('user_id'),
            'search' => $request->get('search'),
        ];

        $orders = $this->orderService->getPaginatedOrders($perPage, array_filter($filters));
        
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

        return view('admin.orders.index', compact('orders', 'statusOptions', 'userOptions', 'filters'));
    }

    public function show(int $id): View
    {
        try {
            $order = $this->orderService->getOrderById($id);
            return view('admin.orders.show', compact('order'));
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        try {
            $order = $this->orderService->getOrderById($id);
            
            $statusOptions = collect(OrderStatus::cases())->mapWithKeys(function ($status) {
                return [$status->value => $status->label()];
            })->all();

            return view('admin.orders.edit', compact('order', 'statusOptions'));
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, int $id): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            
            $this->orderService->updateOrder($id, $validatedData);
            
            return redirect()->route('admin.orders.index')->with('success', 'Order berhasil diperbarui.');
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui order: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): RedirectResponse
    {
        try {
            $validatedData = $request->validated();
            
            $this->orderService->updateOrderStatus($id, $validatedData['status']);
            
            return redirect()->route('admin.orders.index', $id)->with('success', 'Status order berhasil diperbarui.');
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status order: ' . $e->getMessage()]);
        }
    }

    public function cancel(int $id): RedirectResponse
    {
        try {
            $this->orderService->cancelOrder($id);
            
            return redirect()->route('admin.orders.show', $id)->with('success', 'Order berhasil dibatalkan.');
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (OrderCannotBeCancelledException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membatalkan order: ' . $e->getMessage()]);
        }
    }
     public function exportPdf(Request $request)
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'user_id' => $request->get('user_id'),
                'search' => $request->get('search'),
            ];

            // Get all orders without pagination for export
            $orders = $this->orderService->getFilteredOrders(array_filter($filters));
            
            // Tambahkan data tambahan untuk laporan
            $exportData = [
                'orders' => $orders,
                'filters' => $filters,
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
            
            // Pastikan hanya mengembalikan PDF download
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport PDF: ' . $e->getMessage());
        }
    }
}