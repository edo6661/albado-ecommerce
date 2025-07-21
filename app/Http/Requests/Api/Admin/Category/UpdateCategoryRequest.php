<?php

namespace App\Http\Requests\Api\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan user adalah admin
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId)
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('categories', 'slug')->ignore($categoryId)
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori harus diisi',
            'name.string' => 'Nama kategori harus berupa teks',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'name.unique' => 'Nama kategori sudah digunakan',
            'slug.required' => 'Slug kategori harus diisi',
            'slug.unique' => 'Slug kategori sudah digunakan',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Gambar harus berformat: jpeg, png, jpg, gif, webp',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'delete_image.boolean' => 'Delete image harus berupa boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
            'delete_image' => $this->delete_image ?? false,
        ]);
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