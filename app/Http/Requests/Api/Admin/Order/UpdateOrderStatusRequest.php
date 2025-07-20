<?php
// app/Http/Requests/Api/Admin/Order/UpdateOrderStatusRequest.php

namespace App\Http\Requests\Api\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\OrderStatus;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $statusValues = implode(',', OrderStatus::values());
        
        return [
            'status' => "required|string|in:{$statusValues}",
        ];
    }

    public function messages(): array
    {
        $statusLabels = collect(OrderStatus::cases())
            ->map(fn($status) => $status->label())
            ->implode(', ');
            
        return [
            'status.required' => 'Status harus diisi',
            'status.string' => 'Status harus berupa teks',
            'status.in' => "Status harus salah satu dari: {$statusLabels}",
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