<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 'name' => [$this->isPostRequest(), 'unique:ListingCategory', 'max:255'],
            'name' => [$this->isPostRequest(), 'max:255'],
            'category' => [$this->isPostRequest(), 'max:255'],
            'description' => [$this->isPostRequest(), 'max:1000'],
            'address' => [$this->isPostRequest(), 'max:1000'],
            'city' => [$this->isPostRequest(), 'max:500'],
            'listing_photos' => ['max:500'],
            'merchant_id' =>[$this->isPostRequest()]
        ];
    }

    private function isPostRequest() {
        return request()->isMethod('post') ? 'required' :'sometimes';
    }
}
