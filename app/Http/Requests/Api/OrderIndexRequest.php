<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
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
            'per_page' => 'integer|min:1|max:50',
            'cursor' => 'integer|min:1',
        ];
    }

    /**
     * Get validated data with proper type conversion.
     */
    public function getValidatedData(): array
    {
        return $this->validated();
    }
}