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
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
            
            return redirect()->route('admin.orders.show', $id)->with('success', 'Status order berhasil diperbarui.');
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

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            
            return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus.');
        } catch (OrderNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus order: ' . $e->getMessage()]);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->orderService->getOrderStatistics();
            
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
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'user_id' => $request->get('user_id'),
            ];

            // Logic untuk export data bisa ditambahkan di sini
            // Misalnya menggunakan Excel export atau CSV
            
            return response()->json(['success' => true, 'message' => 'Export berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}