<?php

namespace App\Http\Controllers;

use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\TransactionServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected ProductServiceInterface $productService,
        protected OrderServiceInterface $orderService,
        protected TransactionServiceInterface $transactionService
    ) {}

    public function index()
    {
        $userStats = $this->userService->getUserStatistics();
        $productStats = $this->productService->getProductStatistics();
        $orderStats = $this->orderService->getOrderStatistics();
        $transactionStats = $this->transactionService->getTransactionStatistics();

        $recentUsers = $this->userService->getRecentUsers(5);
        $recentProducts = $this->productService->getRecentProducts(5);
        $recentOrders = $this->orderService->getRecentOrders(5);
        $recentTransactions = $this->transactionService->getRecentTransactions(5);

        return view('admin.dashboard', compact(
            'userStats',
            'productStats', 
            'orderStats',
            'transactionStats',
            'recentUsers',
            'recentProducts',
            'recentOrders',
            'recentTransactions'
        ));
    }
}