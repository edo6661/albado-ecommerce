<x-layouts.plain-app>
    <x-slot:title>Data Transaksi</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="transactionManager()">
        <div class="max-w-7xl mx-auto">
            <x-shared.features.transaction.transaction-header 
                :total-transactions="$transactions->total()"
            />
            
            <x-shared.features.transaction.transaction-filters 
                :status-options="$statusOptions"
                :payment-options="$paymentOptions"
                :order-options="$orderOptions"
                :filters="$filters"
            />
            
            <x-shared.features.transaction.transaction-table 
                :transactions="$transactions"
            />
        </div>
        
        <x-shared.features.transaction.transaction-modals
            :status-options="$statusOptions"
        />
    </div>
    
    <script>
        function transactionManager() {
            return {
                selectedTransactions: [],
                selectAll: false,
                searchQuery: '{{ $filters['search'] ?? '' }}',
                selectedStatus: '{{ $filters['status'] ?? '' }}',
                selectedPaymentType: '{{ $filters['payment_type'] ?? '' }}',
                selectedOrder: '{{ $filters['order_id'] ?? '' }}',
                dateFrom: '{{ $filters['date_from'] ?? '' }}',
                dateTo: '{{ $filters['date_to'] ?? '' }}',
                filteredTransactions: [],
                
                showDeleteModal: false,
                showBulkDeleteModal: false,
                showStatusModal: false,
                isLoading: false,
                
                transactionToDelete: { id: null, transactionId: '' },
                transactionToUpdateStatus: { id: null, transactionId: '', currentStatus: '' },
                newStatus: '',
                
                init() {
                    this.initializeTransactionData();
                },
                
                initializeTransactionData() {
                    const transactions = @json($transactions->items());
                    this.allTransactions = transactions.map(transaction => ({
                        id: transaction.id,
                        transaction_id: transaction.transaction_id,
                        order_number: transaction.order.order_number,
                        user_name: transaction.order.user.name,
                        status: transaction.status,
                        payment_type: transaction.payment_type,
                        gross_amount: transaction.gross_amount
                    }));
                },
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedTransactions = this.allTransactions.map(t => t.id);
                    } else {
                        this.selectedTransactions = [];
                    }
                },
                
                $watch: {
                    selectedTransactions(newVal) {
                        this.selectAll = newVal.length > 0 && newVal.length === this.allTransactions.length;
                    }
                },
                
                confirmDelete(transactionId, transactionIdValue) {
                    this.transactionToDelete = { id: transactionId, transactionId: transactionIdValue };
                    this.showDeleteModal = true;
                },
                
                confirmStatusUpdate(transactionId, transactionIdValue, currentStatus) {
                    this.transactionToUpdateStatus = { id: transactionId, transactionId: transactionIdValue, currentStatus: currentStatus };
                    this.newStatus = currentStatus;
                    this.showStatusModal = true;
                },
                
                async deleteTransaction() {
                    this.isLoading = true;
                    this.showDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/transactions/${this.transactionToDelete.id}`;
                        form.style.display = 'none';
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.getAttribute('content');
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error deleting transaction:', error);
                        alert('Terjadi kesalahan saat menghapus transaksi.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                async updateStatus() {
                    this.isLoading = true;
                    this.showStatusModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/transactions/${this.transactionToUpdateStatus.id}/status`;
                        form.style.display = 'none';
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.getAttribute('content');
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PATCH';
                        form.appendChild(methodInput);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = this.newStatus;
                        form.appendChild(statusInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error updating status:', error);
                        alert('Terjadi kesalahan saat memperbarui status.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                applyFilters() {
                    const params = new URLSearchParams();
                    
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    if (this.selectedStatus) params.append('status', this.selectedStatus);
                    if (this.selectedPaymentType) params.append('payment_type', this.selectedPaymentType);
                    if (this.selectedOrder) params.append('order_id', this.selectedOrder);
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);
                    
                    window.location.href = window.location.pathname + '?' + params.toString();
                },
                
                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },
                
                getStatusBadgeClass(status) {
                    const statusClasses = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'settlement': 'bg-green-100 text-green-800',
                        'capture': 'bg-blue-100 text-blue-800',
                        'deny': 'bg-red-100 text-red-800',
                        'cancel': 'bg-gray-100 text-gray-800',
                        'expire': 'bg-red-100 text-red-800',
                        'failure': 'bg-red-100 text-red-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },
                
                getPaymentTypeBadgeClass(paymentType) {
                    const paymentClasses = {
                        'credit_card': 'bg-blue-100 text-blue-800',
                        'bank_transfer': 'bg-green-100 text-green-800',
                        'echannel': 'bg-purple-100 text-purple-800',
                        'gopay': 'bg-emerald-100 text-emerald-800',
                        'shopeepay': 'bg-orange-100 text-orange-800',
                        'qris': 'bg-indigo-100 text-indigo-800'
                    };
                    return paymentClasses[paymentType] || 'bg-gray-100 text-gray-800';
                }
            }
        }
    </script>
</x-layouts.plain-app>