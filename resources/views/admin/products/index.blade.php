<x-layouts.plain-app>
    <x-slot:title>Data Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="productManager()">
        <div class="max-w-7xl mx-auto">
            <x-shared.features.product.product-header 
                :total-products="$products->total()"
                :create-route="route('admin.products.create')"
            />
            
            <x-shared.features.product.product-filters 
                :category-options="$categoryOptions"
            />
            
            <x-shared.features.product.product-table 
                :products="$products"
                :create-route="route('admin.products.create')"
            />
        </div>
        
        <x-shared.features.product.product-modals />
    </div>
    
    <script>
        function productManager() {
            return {
                
                selectedProducts: [],
                selectAll: false,
                searchQuery: '',
                selectedCategory: '',
                selectedStatus: '',
                filteredProducts: [],
                
                
                showDeleteModal: false,
                showBulkDeleteModal: false,
                showImageModal: false,
                isLoading: false,
                
                
                productToDelete: { id: null, name: '' },
                imagePreview: { src: '', alt: '' },
                
                init() {
                    this.initializeProductData();
                    this.filterProducts();
                },
                
                initializeProductData() {
                    const products = @json($products->items());
                    this.allProducts = products.map(product => ({
                        id: product.id,
                        name: product.name,
                        category: product.category.name,
                        is_active: product.is_active
                    }));
                },
                
                filterProducts() {
                    this.filteredProducts = this.allProducts.filter(product => {
                        const matchesSearch = this.searchQuery === '' || 
                            product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        const matchesCategory = this.selectedCategory === '' || 
                            product.category === this.selectedCategory;
                        
                        const matchesStatus = this.selectedStatus === '' || 
                            (this.selectedStatus === 'active' && product.is_active) ||
                            (this.selectedStatus === 'inactive' && !product.is_active);
                        
                        return matchesSearch && matchesCategory && matchesStatus;
                    });
                },
                
                shouldShowProduct(product) {
                    if (this.searchQuery === '' && this.selectedCategory === '' && this.selectedStatus === '') {
                        return true;
                    }
                    
                    const matchesSearch = this.searchQuery === '' || 
                        product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    const matchesCategory = this.selectedCategory === '' || 
                        product.category === this.selectedCategory;
                    
                    const matchesStatus = this.selectedStatus === '' || 
                        (this.selectedStatus === 'active' && product.is_active) ||
                        (this.selectedStatus === 'inactive' && !product.is_active);
                    
                    return matchesSearch && matchesCategory && matchesStatus;
                },
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedProducts = this.filteredProducts.map(p => p.id);
                    } else {
                        this.selectedProducts = [];
                    }
                },
                
                $watch: {
                    selectedProducts(newVal) {
                        this.selectAll = newVal.length > 0 && newVal.length === this.filteredProducts.length;
                    }
                },
                
                confirmDelete(productId, productName) {
                    this.productToDelete = { id: productId, name: productName };
                    this.showDeleteModal = true;
                },
                
                async deleteProduct() {
                    this.isLoading = true;
                    this.showDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/products/${this.productToDelete.id}`;
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
                        console.error('Error deleting product:', error);
                        alert('Terjadi kesalahan saat menghapus produk.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                async bulkDeleteProducts() {
                    if (this.selectedProducts.length === 0) return;
                    
                    this.isLoading = true;
                    this.showBulkDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/products/bulk-destroy';
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
                        
                        this.selectedProducts.forEach(productId => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = productId;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error bulk deleting products:', error);
                        alert('Terjadi kesalahan saat menghapus produk.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                onClickShowImageModal(src, alt) {
                    this.imagePreview = { src, alt };
                    this.showImageModal = true;
                },
                
                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },
                exportToPdf() {
                    this.isLoading = true;
                    
                    const params = new URLSearchParams();
                    if (this.selectedCategory) params.append('category', this.selectedCategory);
                    if (this.selectedStatus) params.append('status', this.selectedStatus);
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    
                    const url = `/admin/products/export/pdf?${params.toString()}`;
                    
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
            }
        }
    </script>
</x-layouts.plain-app>