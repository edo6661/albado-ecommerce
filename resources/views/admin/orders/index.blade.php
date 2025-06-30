<x-layouts.plain-app>
    <x-slot:title>Data Pesanan</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="orderManager()">
        <div class="max-w-7xl mx-auto">
            <x-shared.features.order.order-header 
                :total-orders="$orders->total()"
            />
            
            <x-shared.features.order.order-filters 
                :status-options="$statusOptions"
                :user-options="$userOptions"
                :filters="$filters"
            />
            
            <x-shared.features.order.order-table 
                :orders="$orders"
            />
        </div>
        
        <x-shared.features.order.order-modals
            :status-options="$statusOptions"
        />
    </div>
    
    <script>
        function orderManager() {
            return {
                selectedOrders: [],
                selectAll: false,
                searchQuery: '{{ $filters['search'] ?? '' }}',
                selectedStatus: '{{ $filters['status'] ?? '' }}',
                selectedUser: '{{ $filters['user_id'] ?? '' }}',
                dateFrom: '{{ $filters['date_from'] ?? '' }}',
                dateTo: '{{ $filters['date_to'] ?? '' }}',
                filteredOrders: [],
                
                showDeleteModal: false,
                showBulkDeleteModal: false,
                showStatusModal: false,
                isLoading: false,
                
                orderToDelete: { id: null, number: '' },
                orderToUpdateStatus: { id: null, number: '', currentStatus: '' },
                newStatus: '',
                
                init() {
                    this.initializeOrderData();
                },
                
                initializeOrderData() {
                    const orders = @json($orders->items());
                    this.allOrders = orders.map(order => ({
                        id: order.id,
                        order_number: order.order_number,
                        user_name: order.user.name,
                        status: order.status,
                        total: order.total
                    }));
                },
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedOrders = this.allOrders.map(o => o.id);
                    } else {
                        this.selectedOrders = [];
                    }
                },
                
                $watch: {
                    selectedOrders(newVal) {
                        this.selectAll = newVal.length > 0 && newVal.length === this.allOrders.length;
                    }
                },
                
                confirmDelete(orderId, orderNumber) {
                    this.orderToDelete = { id: orderId, number: orderNumber };
                    this.showDeleteModal = true;
                },
                
                confirmStatusUpdate(orderId, orderNumber, currentStatus) {
                    this.orderToUpdateStatus = { id: orderId, number: orderNumber, currentStatus: currentStatus };
                    this.newStatus = currentStatus;
                    this.showStatusModal = true;
                },
                
                async deleteOrder() {
                    this.isLoading = true;
                    this.showDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/orders/${this.orderToDelete.id}`;
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
                        console.error('Error deleting order:', error);
                        alert('Terjadi kesalahan saat menghapus pesanan.');
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
                        form.action = `/admin/orders/${this.orderToUpdateStatus.id}/status`;
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
                    if (this.selectedUser) params.append('user_id', this.selectedUser);
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
                        'confirmed': 'bg-blue-100 text-blue-800',
                        'processing': 'bg-purple-100 text-purple-800',
                        'shipped': 'bg-indigo-100 text-indigo-800',
                        'delivered': 'bg-green-100 text-green-800',
                        'cancelled': 'bg-red-100 text-red-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },
                exportToPdf() {
                    this.isLoading = true;
                    
                    const params = new URLSearchParams();
                    if (this.selectedStatus) params.append('status', this.selectedStatus);
                    if (this.selectedUser) params.append('user_id', this.selectedUser);
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    
                    const url = `/admin/orders/export/pdf?${params.toString()}`;
                    
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = url;
                    form.target = '_blank'; 
                    form.style.display = 'none';
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                    
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 1000);
                },
            };
        }
    </script>
</x-layouts.plain-app>