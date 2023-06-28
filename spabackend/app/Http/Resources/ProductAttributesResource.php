<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        // return [
        //     'type' => 'product_attributes',
        //     'id' => (string) $this->id,
        //     'product_id' => (string) $this->product->id,
        //     'attributes' => [
        //         'name' => $this->name,
        //         'value' => $this->value,
        //         'highlight' => $this->highlight,
        //         'important' => $this->important,
        //     ],
        // ];

        return [
            'id' => (string) $this->id,
            'product_id' => (string) $this->product_id,    
            'name' => $this->name,
            'value' => $this->value,
            'highlight' => $this->highlight,
            'important' => $this->important,
        ];
    }
}

