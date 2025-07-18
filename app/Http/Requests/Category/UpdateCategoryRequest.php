<?php

namespace App\Http\Requests\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'required|string|unique:categories,slug,' . $categoryId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
            'slug.unique' => 'Slug kategori sudah ada.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.'
        ];
    }
}
