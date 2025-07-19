<?php
namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort_by' => 'nullable|string|in:latest,oldest,price_asc,price_desc,name_asc,name_desc,rating_high,rating_low',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'featured' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori tidak ditemukan',
            'min_price.numeric' => 'Harga minimum harus berupa angka',
            'max_price.numeric' => 'Harga maksimum harus berupa angka',
            'max_price.gte' => 'Harga maksimum harus lebih besar atau sama dengan harga minimum',
            'sort_by.in' => 'Kriteria pengurutan tidak valid',
            'per_page.max' => 'Maksimal 100 item per halaman',
            'limit.max' => 'Maksimal 50 item'
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'search',
            'category_id', 
            'min_price',
            'max_price',
            'sort_by'
        ]);
    }

    public function hasAnyFilter(): bool
    {
        return $this->hasAny([
            'search',
            'category_id',
            'min_price', 
            'max_price',
            'sort_by'
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}