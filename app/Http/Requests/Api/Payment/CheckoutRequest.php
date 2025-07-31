<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'integer|exists:cart_items,id',
            'address_id' => 'required|integer|exists:addresses,id,user_id,' . Auth::id(),
        ];
    }

    public function messages(): array
    {
        return [
            'selected_items.required' => 'Item yang dipilih harus diisi',
            'selected_items.array' => 'Item yang dipilih harus berupa array',
            'selected_items.min' => 'Minimal harus memilih 1 item',
            'selected_items.*.integer' => 'ID item harus berupa angka',
            'selected_items.*.exists' => 'Item yang dipilih tidak valid',
            'address_id.required' => 'Alamat pengiriman harus dipilih',
            'address_id.integer' => 'ID alamat harus berupa angka',
            'address_id.exists' => 'Alamat pengiriman tidak valid atau bukan milik Anda',
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
                'message' => 'Unauthorized. Login required.'
            ], 401)
        );
    }
}