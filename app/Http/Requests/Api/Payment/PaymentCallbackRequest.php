<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PaymentCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'transaction_status' => 'required|string|in:settlement,pending,failure,cancel,expire,deny',
            'midtrans_result' => 'nullable|array',
            'fraud_status' => 'nullable|string',
            'status_message' => 'nullable|string',
            'gross_amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'transaction_time' => 'nullable|string',
            'settlement_time' => 'nullable|string',
            'payment_type' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Order ID harus diisi',
            'order_id.integer' => 'Order ID harus berupa angka',
            'order_id.exists' => 'Order tidak ditemukan',
            'transaction_status.required' => 'Status transaksi harus diisi',
            'transaction_status.in' => 'Status transaksi tidak valid',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi callback gagal',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
