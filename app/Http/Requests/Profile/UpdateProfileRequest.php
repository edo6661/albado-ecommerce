<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        
        $rules = [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'nullable', 
                'email', 
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['sometimes', 'nullable', 'string', 'min:8'],
            
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.min' => 'Konfirmasi password minimal 8 karakter.',
            'avatar.image' => 'Avatar harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran Avatar maksimal 2MB.',
        ];
    }
    protected function prepareForValidation(): void
    {
        if ($this->filled('password') && empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }
}