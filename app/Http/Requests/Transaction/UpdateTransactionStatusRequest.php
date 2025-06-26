<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TransactionStatus;

class UpdateTransactionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:' . implode(',', TransactionStatus::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status transaksi wajib diisi.',
            'status.in' => 'Status transaksi tidak valid.',
        ];
    }
}
