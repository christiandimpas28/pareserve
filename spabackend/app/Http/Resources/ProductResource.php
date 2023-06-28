<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
        //     'type' => 'products',
        //     'id' => (string) $this->id,
        //     'attributes' => [
        //         'name' => $this->name,
        //         'slug' => $this->slug,
        //         'description' => $this->description,
        //         'max_guest' => $this->max_guest,
        //         'photos' => $this->photos,
        //         'enabled' => $this->enabled,
        //         'rate' => $this->rate,
        //         'discount' => $this->discount,
        //         'listing_category_id' => $this->listing_category_id,
        //         'listings' => [
        //             'id' => (string) $this->listingCategory->id,
        //             'name' => $this->listingCategory->name,
        //             'category' => $this->listingCategory->category,
        //             'merchant_id' => $this->listingCategory->merchant_id,
        //         ]
        //     ]
        // ];

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'max_guest' => $this->max_guest,
            'photos' => $this->photos,
            'enabled' => $this->enabled,
            'rate' => $this->rate,
            'discount' => $this->discount,
            'listing_category_id' => $this->listing_category_id,
            'product_attributes' => ProductAttributesResource::collection($this->productAttributes),
        ];
    }
}
