<?php

namespace App\Http\Requests\Api\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'cursor' => 'sometimes|nullable|integer|min:1',
            'search' => 'sometimes|nullable|string|max:255',
            'has_products' => 'sometimes|in:true,false,1,0',
            // Tambahkan aturan filter lain jika ada, misal 'sort_by', 'sort_direction'
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function getValidatedData(): array
    {
        return $this->validated();
    }

    /**
     * Get filters from the request.
     *
     * @return array
     */
    public function getFilters(): array
    {
        $validated = $this->getValidatedData();
        $filters = [];

        if (isset($validated['search'])) {
            $filters['search'] = $validated['search'];
        }
        
        if (isset($validated['has_products'])) {
            $hasProducts = $validated['has_products'];
            if (is_string($hasProducts)) {
                $filters['has_products'] = in_array($hasProducts, ['true', '1'], true);
            } else {
                $filters['has_products'] = (bool) $hasProducts;
            }
        }
        
        // Tambahkan logika untuk filter lain di sini
        
        return $filters;
    }
}