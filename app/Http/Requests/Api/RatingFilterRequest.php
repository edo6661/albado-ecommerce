<?php
// app/Http/Requests/Api/RatingFilterRequest.php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RatingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:latest,oldest,rating_high,rating_low',
            'rating' => 'nullable|integer|min:1|max:5'
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page harus berupa angka',
            'per_page.min' => 'Per page minimal 1',
            'per_page.max' => 'Maksimal 100 item per halaman',
            'page.integer' => 'Page harus berupa angka',
            'page.min' => 'Page minimal 1',
            'sort_by.in' => 'Kriteria pengurutan tidak valid',
            'rating.integer' => 'Rating harus berupa angka',
            'rating.min' => 'Rating minimal 1',
            'rating.max' => 'Rating maksimal 5'
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'sort_by',
            'rating'
        ]);
    }

    public function hasAnyFilter(): bool
    {
        return $this->hasAny([
            'sort_by',
            'rating'
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