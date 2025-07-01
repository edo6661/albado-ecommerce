<x-layouts.plain-app>
    <x-slot:title>Data Kategori</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="categoryManager()">
        <div class="max-w-7xl mx-auto">
            <x-shared.features.category.category-header 
                :total-categories="$categories->total()"
                :create-route="route('admin.categories.create')"
            />
            
            <x-shared.features.category.category-filters />
            
            <x-shared.features.category.category-table 
                :categories="$categories"
                :create-route="route('admin.categories.create')"
            />
        </div>
        
        <x-shared.features.category.category-modals />
    </div>
    
    <script>
        function categoryManager() {
            return {
                
                selectedCategories: [],
                selectAll: false,
                searchQuery: '',
                filteredCategories: [],
                
                
                showDeleteModal: false,
                showBulkDeleteModal: false,
                showImageModal: false,
                isLoading: false,
                
                
                categoryToDelete: { id: null, name: '' },
                imagePreview: { src: '', alt: '' },
                
                init() {
                    this.initializeCategoryData();
                    this.filterCategories();
                },
                
                initializeCategoryData() {
                    const categories = @json($categories->items());
                    this.allCategories = categories.map(category => ({
                        id: category.id,
                        name: category.name,
                        slug: category.slug,
                        products_count: category.products_count || 0
                    }));
                },
                
                filterCategories() {
                    this.filteredCategories = this.allCategories.filter(category => {
                        const matchesSearch = this.searchQuery === '' || 
                            category.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            category.slug.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        return matchesSearch;
                    });
                },
                
                shouldShowCategory(category) {
                    if (this.searchQuery === '') {
                        return true;
                    }
                    
                    const matchesSearch = this.searchQuery === '' || 
                        category.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        category.slug.toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    return matchesSearch;
                },
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedCategories = this.filteredCategories.map(c => c.id);
                    } else {
                        this.selectedCategories = [];
                    }
                },
                
                $watch: {
                    selectedCategories(newVal) {
                        this.selectAll = newVal.length > 0 && newVal.length === this.filteredCategories.length;
                    }
                },
                
                confirmDelete(categoryId, categoryName) {
                    this.categoryToDelete = { id: categoryId, name: categoryName };
                    this.showDeleteModal = true;
                },
                
                async deleteCategory() {
                    this.isLoading = true;
                    this.showDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/categories/${this.categoryToDelete.id}`;
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
                        console.error('Error deleting category:', error);
                        alert('Terjadi kesalahan saat menghapus kategori.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                async bulkDeleteCategories() {
                    if (this.selectedCategories.length === 0) return;
                    
                    this.isLoading = true;
                    this.showBulkDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/categories/bulk-destroy';
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
                        
                        this.selectedCategories.forEach(categoryId => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = categoryId;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error bulk deleting categories:', error);
                        alert('Terjadi kesalahan saat menghapus kategori.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                onClickShowImageModal(src, alt) {
                    this.imagePreview = { src, alt };
                    this.showImageModal = true;
                },
                
            }
        }
    </script>
</x-layouts.plain-app>