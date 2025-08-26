<?php

namespace App\Http\Requests\Api\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Asumsikan admin yang mengakses sudah diautentikasi
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:50',
            'cursor' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|max:255',
            'status' => ['sometimes', 'string', Rule::in(OrderStatus::values())],
            'date_from' => 'sometimes|date_format:Y-m-d',
            'date_to' => 'sometimes|date_format:Y-m-d|after_or_equal:date_from',
            'user_id' => 'sometimes|integer|exists:users,id',
        ];
    }
}