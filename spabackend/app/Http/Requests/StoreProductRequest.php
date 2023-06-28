<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => [$this->isPostRequest(), 'max:500'],
            'slug' => [$this->isPostRequest()],
            'description' => ['max:5000'],
            'max_guest' => [$this->isPostRequest()],
            'photos' => [$this->isPostRequest()],
            'enabled' => [$this->isPostRequest()],
            'discount' => [$this->isPostRequest()],
            'rate' => [$this->isPostRequest()],
            'listing_category_id' => [$this->isPostRequest()],
        ];
    }

    private function isPostRequest() {
        return request()->isMethod('post') ? 'required' :'sometimes';
    }
}
