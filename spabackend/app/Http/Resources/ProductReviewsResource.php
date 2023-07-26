<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewsResource extends JsonResource
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
            'product_id' => (string) $this->product_id,
            'book_id' => (string) $this->book_id,
            'photos' => $this->photos,
            'rating' => $this->rating,
            'review' => $this->review,
            'user' => $this->user,
        ];
    }
}
