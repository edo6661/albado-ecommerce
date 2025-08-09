<?php
namespace App\Http\Requests\Api\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'integer|min:1|max:50',
            'cursor' => 'integer|min:1'
        ];
    }

    public function getValidatedData(): array
    {
        return $this->validated();
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page harus berupa angka',
            'per_page.min' => 'Per page minimal adalah 1',
            'per_page.max' => 'Per page maksimal adalah 50',
            'cursor.integer' => 'Cursor harus berupa angka',
            'cursor.min' => 'Cursor minimal adalah 1'
        ];
    }
}