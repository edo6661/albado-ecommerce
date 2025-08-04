<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CategoryDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $baseData = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
        if (isset($this->paginated_products)) {
            $productsData = $this->paginated_products;
            return array_merge($baseData, [
                'products' => ProductResource::collection($productsData['data']),
                'pagination' => [
                    'has_next_page' => $productsData['has_next_page'],
                    'next_cursor' => $productsData['next_cursor'] ? (int) $productsData['next_cursor'] : null,
                    'per_page' => (int) $productsData['per_page'],
                ],
            ]);
        }
        return array_merge($baseData, [
            'products_count' => $this->whenLoaded('products', fn() => $this->products->count()),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ]);
    }
}