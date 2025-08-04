<?php
namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CategoryIndexRequest extends FormRequest
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
            'has_products' => 'sometimes|in:true,false,1,0'
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page harus berupa angka',
            'per_page.min' => 'Per page minimal 1',
            'per_page.max' => 'Per page maksimal 50',
            'cursor.integer' => 'Cursor harus berupa angka',
            'cursor.min' => 'Cursor minimal 1',
            'search.string' => 'Search harus berupa text',
            'search.max' => 'Search maksimal 255 karakter',
            'has_products.in' => 'Has products harus true atau false'
        ];
    }

    /**
     * Get validated data with proper type conversion
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        
        // Convert has_products to boolean
        if (isset($validated['has_products'])) {
            $hasProducts = $validated['has_products'];
            $validated['has_products'] = in_array($hasProducts, ['true', '1'], true);
        }

        return $validated;
    }

    /**
     * Get filters array
     */
    public function getFilters(): array
    {
        $validated = $this->getValidatedData();
        $filters = [];

        if (isset($validated['search'])) {
            $filters['search'] = $validated['search'];
        }

        if (isset($validated['has_products'])) {
            $filters['has_products'] = $validated['has_products'];
        }

        return $filters;
    }
}