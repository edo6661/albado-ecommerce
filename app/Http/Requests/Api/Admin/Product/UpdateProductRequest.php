<?php

namespace App\Http\Requests\Api\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        
        $currentProduct = Product::find($productId);
        
        $rules = [
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_images' => 'sometimes|array',
            'delete_images.*' => 'integer|exists:product_images,id',
        ];

        
        if ($this->has('name') && $currentProduct && $this->get('name') !== $currentProduct->name) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($productId)
            ];
        } elseif ($this->has('name')) {
            $rules['name'] = ['sometimes', 'string', 'max:255'];
        }

        if ($this->has('slug') && $currentProduct && $this->get('slug') !== $currentProduct->slug) {
            $rules['slug'] = [
                'required',
                'string',
                Rule::unique('products')->ignore($productId)
            ];
        } elseif ($this->has('slug')) {
            $rules['slug'] = ['sometimes', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nama produk sudah digunakan',
            'slug.unique' => 'Slug produk sudah digunakan',
            'category_id.exists' => 'Kategori tidak valid',
            'price.min' => 'Harga tidak boleh kurang dari 0',
            'discount_price.lt' => 'Harga diskon harus lebih kecil dari harga normal',
            'stock.min' => 'Stok tidak boleh kurang dari 0',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}