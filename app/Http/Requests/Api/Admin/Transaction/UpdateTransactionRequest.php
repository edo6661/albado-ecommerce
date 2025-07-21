<?php
// app/Http/Requests/Api/Admin/Transaction/UpdateTransactionRequest.php

namespace App\Http\Requests\Api\Admin\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|string|in:' . implode(',', TransactionStatus::values()),
            'payment_type' => 'sometimes|string|in:' . implode(',', PaymentType::values()),
            'fraud_status' => 'sometimes|nullable|string',
            'status_message' => 'sometimes|nullable|string',
            'settlement_time' => 'sometimes|nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status transaksi tidak valid',
            'payment_type.in' => 'Tipe pembayaran tidak valid',
            'settlement_time.date' => 'Waktu settlement harus berformat tanggal yang valid',
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
