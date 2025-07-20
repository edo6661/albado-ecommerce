<?php

namespace App\Http\Requests\Api\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BulkDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan user adalah admin
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id'
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'ID kategori harus diisi',
            'ids.array' => 'Format ID tidak valid',
            'ids.min' => 'Minimal pilih 1 kategori untuk dihapus',
            'ids.*.integer' => 'ID kategori harus berupa angka',
            'ids.*.exists' => 'Kategori tidak ditemukan',
        ];
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

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403)
        );
    }
}