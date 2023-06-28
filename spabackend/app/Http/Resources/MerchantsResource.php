<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantsResource extends JsonResource
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
            // 'user_id' => (string) $this->user->id,
            'name' => $this->name,
            'bus_contact_name' => $this->bus_contact_name,
            'bus_contact_no' => $this->bus_contact_no,
            'bus_email' => $this->bus_email,
            'bus_address' => $this->bus_address,
            'listings' => ListingCategoryResource::collection($this->listings)
        ];
    }
}
