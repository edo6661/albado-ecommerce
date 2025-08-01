<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedCategoryResource extends ResourceCollection
{
    protected $pagination;
    protected $filters;

    public function __construct($resource, $pagination = [], $filters = [])
    {
        parent::__construct($resource);
        $this->pagination = $pagination;
        $this->filters = $filters;
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => CategoryResource::collection($this->collection),
            'pagination' => $this->pagination,
            'filters' => $this->filters
        ];
    }

    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Data berhasil diambil'
        ];
    }
}