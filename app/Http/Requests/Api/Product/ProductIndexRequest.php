<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array    
    {
        return [
            'per_page' => 'integer|min:1|max:50',
            'cursor' => 'integer|min:1',
            'search' => 'string|max:255',
            'category_id' => 'integer|exists:categories,id',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0|gte:min_price',
            'sort_by' => 'string|in:latest,oldest,price_asc,price_desc,name_asc,name_desc,rating_high,rating_low',
            'is_active' => 'sometimes|in:true,false,1,0',
            'in_stock' => 'sometimes|in:true,false,1,0'
        ];
    }

    public function getValidatedData(): array
    {
        return $this->validated();
    }

    public function getFilters(): array
    {
        $filters = [];
        
        // Add available filters based on validation rules
        $filterKeys = [
            'search',
            'category_id', 
            'min_price',
            'max_price',
            'sort_by',
            'is_active',
            'in_stock'
        ];

        foreach ($filterKeys as $key) {
            if ($this->has($key) && !is_null($this->get($key))) {
                $filters[$key] = $this->get($key);
            }
        }

        return $filters;
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page harus berupa angka',
            'per_page.min' => 'Per page minimal 1',
            'per_page.max' => 'Per page maksimal 50',
            'cursor.integer' => 'Cursor harus berupa angka',
            'cursor.min' => 'Cursor minimal 1',
            'search.string' => 'Pencarian harus berupa teks',
            'search.max' => 'Pencarian maksimal 255 karakter',
            'category_id.integer' => 'ID kategori harus berupa angka',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'min_price.numeric' => 'Harga minimum harus berupa angka',
            'min_price.min' => 'Harga minimum tidak boleh negatif',
            'max_price.numeric' => 'Harga maksimum harus berupa angka',
            'max_price.min' => 'Harga maksimum tidak boleh negatif',
            'max_price.gte' => 'Harga maksimum harus lebih besar atau sama dengan harga minimum',
            'sort_by.in' => 'Urutan tidak valid',
            'is_active.in' => 'Status aktif harus true, false, 1, atau 0',
            'in_stock.in' => 'Status stok harus true, false, 1, atau 0'
        ];
    }
}