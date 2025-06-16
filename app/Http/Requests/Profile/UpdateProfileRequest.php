<?php


namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'avatar' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.string' => 'Avatar harus berupa teks.',
            'avatar.max' => 'Avatar maksimal 255 karakter.',
        ];
    }
}