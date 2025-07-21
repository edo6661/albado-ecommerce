<?php
// app/Http/Requests/Api/Admin/Order/ExportPdfRequest.php

namespace App\Http\Requests\Api\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\OrderStatus;

class ExportPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusValues = implode(',', OrderStatus::values());
        
        return [
            'status' => "sometimes|string|in:{$statusValues}",
            'date_from' => 'sometimes|date|before_or_equal:date_to',
            'date_to' => 'sometimes|date|after_or_equal:date_from|before_or_equal:today',
            'user_id' => 'sometimes|exists:users,id',
            'search' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        $statusLabels = collect(OrderStatus::cases())
            ->map(fn($status) => $status->label())
            ->implode(', ');
            
        return [
            'status.in' => "Status harus salah satu dari: {$statusLabels}",
            'date_from.date' => 'Tanggal mulai harus berupa tanggal yang valid',
            'date_from.before_or_equal' => 'Tanggal mulai harus sebelum atau sama dengan tanggal akhir',
            'date_to.date' => 'Tanggal akhir harus berupa tanggal yang valid',
            'date_to.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'date_to.before_or_equal' => 'Tanggal akhir tidak boleh melebihi hari ini',
            'user_id.exists' => 'Pengguna tidak ditemukan',
            'search.string' => 'Pencarian harus berupa teks',
            'search.max' => 'Pencarian maksimal 255 karakter',
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