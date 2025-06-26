<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
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
            'status' => ['sometimes', 'string', 'in:' . implode(',', TransactionStatus::values())],
            'payment_type' => ['sometimes', 'string', 'in:' . implode(',', PaymentType::values())],
            'fraud_status' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status_message' => ['sometimes', 'nullable', 'string', 'max:255'],
            'settlement_time' => ['sometimes', 'nullable', 'date'],
            'midtrans_response' => ['sometimes', 'nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status transaksi tidak valid.',
            'payment_type.in' => 'Tipe pembayaran tidak valid.',
            'fraud_status.max' => 'Status fraud maksimal 255 karakter.',
            'status_message.max' => 'Pesan status maksimal 255 karakter.',
            'settlement_time.date' => 'Waktu settlement harus berupa tanggal yang valid.',
            'midtrans_response.json' => 'Response midtrans harus berupa JSON yang valid.',
        ];
    }
}