<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'listing_photos' => $this->listing_photos,
            'merchant_id' => (string) $this->merchant_id,
            'products' => ProductResource::collection($this->products)
        ];
        // return [
        //     'type' => 'listing',
        //     'id' => (string) $this->id,
        //     'attributes' => [
        //         'name' => $this->name,
        //         'category' => $this->category,
        //         'description' => $this->description,
        //         'address' => $this->address,
        //         'city' => $this->city,
        //         'listing_photos' => $this->listing_photos,
        //         'merchant_id' => $this->merchant_id,
        //         // 'products' => $this->products? [
        //         //     'id' => (string) $this->products->id,
        //         //     'name' => $this->products->name,
        //         // ]: null
        //     ],
        //     'parent' => [
        //         'id' => (string) $this->merchant->id,
        //         'name' => $this->merchant->name,
        //         'bus_address' => $this->merchant->bus_address,

        //     ]
        // ];
    }
}
