<?php

namespace App\Http\Requests\Api\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($productId)
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('products')->ignore($productId)
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_images' => 'sometimes|array',
            'delete_images.*' => 'integer|exists:product_images,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk harus diisi',
            'name.string' => 'Nama produk harus berupa teks',
            'name.max' => 'Nama produk maksimal 255 karakter',
            'name.unique' => 'Nama produk sudah digunakan',
            'slug.required' => 'Slug produk harus diisi',
            'slug.unique' => 'Slug produk sudah digunakan',
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh kurang dari 0',
            'discount_price.numeric' => 'Harga diskon harus berupa angka',
            'discount_price.min' => 'Harga diskon tidak boleh kurang dari 0',
            'discount_price.lt' => 'Harga diskon harus kurang dari harga normal',
            'stock.required' => 'Stok harus diisi',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok tidak boleh kurang dari 0',
            'images.array' => 'Format gambar tidak valid',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Gambar harus berformat: jpeg, png, jpg, gif, webp',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
            'delete_images.array' => 'Format delete images tidak valid',
            'delete_images.*.integer' => 'ID gambar harus berupa angka',
            'delete_images.*.exists' => 'Gambar tidak ditemukan',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
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
