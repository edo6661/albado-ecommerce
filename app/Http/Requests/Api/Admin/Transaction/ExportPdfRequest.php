<?php
// app/Http/Requests/Api/Admin/Transaction/ExportPdfRequest.php

namespace App\Http\Requests\Api\Admin\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;

class ExportPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|nullable|string|in:' . implode(',', TransactionStatus::values()),
            'payment_type' => 'sometimes|nullable|string|in:' . implode(',', PaymentType::values()),
            'date_from' => 'sometimes|nullable|date|before_or_equal:date_to',
            'date_to' => 'sometimes|nullable|date|after_or_equal:date_from|before_or_equal:today',
            'order_id' => 'sometimes|nullable|integer|exists:orders,id',
            'search' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status transaksi tidak valid',
            'payment_type.in' => 'Tipe pembayaran tidak valid',
            'date_from.date' => 'Tanggal dari harus berformat tanggal yang valid',
            'date_from.before_or_equal' => 'Tanggal dari harus sebelum atau sama dengan tanggal sampai',
            'date_to.date' => 'Tanggal sampai harus berformat tanggal yang valid',
            'date_to.after_or_equal' => 'Tanggal sampai harus setelah atau sama dengan tanggal dari',
            'date_to.before_or_equal' => 'Tanggal sampai tidak boleh melebihi hari ini',
            'order_id.integer' => 'ID order harus berupa angka',
            'order_id.exists' => 'Order tidak ditemukan',
            'search.max' => 'Kata kunci pencarian maksimal 255 karakter',
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