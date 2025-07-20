<?php
// app/Http/Requests/Api/Admin/Transaction/UpdateTransactionStatusRequest.php

namespace App\Http\Requests\Api\Admin\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\TransactionStatus;

class UpdateTransactionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:' . implode(',', TransactionStatus::values()),
            'status_message' => 'sometimes|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status transaksi harus diisi',
            'status.in' => 'Status transaksi tidak valid',
            'status_message.max' => 'Pesan status maksimal 500 karakter',
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
