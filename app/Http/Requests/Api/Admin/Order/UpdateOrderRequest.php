<?php
// app/Http/Requests/Api/Admin/Order/UpdateOrderRequest.php

namespace App\Http\Requests\Api\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|string|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'sometimes|nullable|string|max:1000',
            'shipping_cost' => 'sometimes|numeric|min:0',
            'shipping_address' => 'sometimes|nullable|string|max:500',
            'distance_km' => 'sometimes|nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status harus salah satu dari: pending, processing, shipped, delivered, cancelled',
            'notes.string' => 'Catatan harus berupa teks',
            'notes.max' => 'Catatan maksimal 1000 karakter',
            'shipping_cost.numeric' => 'Biaya pengiriman harus berupa angka',
            'shipping_cost.min' => 'Biaya pengiriman tidak boleh kurang dari 0',
            'shipping_address.string' => 'Alamat pengiriman harus berupa teks',
            'shipping_address.max' => 'Alamat pengiriman maksimal 500 karakter',
            'distance_km.numeric' => 'Jarak harus berupa angka',
            'distance_km.min' => 'Jarak tidak boleh kurang dari 0',
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
