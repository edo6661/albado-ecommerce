<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class CalculateShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'address_id' => 'required|integer|exists:addresses,id,user_id,' . Auth::id()
        ];
    }

    public function messages(): array
    {
        return [
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