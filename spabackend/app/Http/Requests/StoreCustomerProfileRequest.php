<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerProfileRequest extends FormRequest
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
            'first_name' => [$this->isPostRequest(), 'max:255'],
            'last_name' => [$this->isPostRequest(), 'max:255'],
            'id_type' => [$this->isPostRequest()],
            'billing_address' => [$this->isPostRequest(), 'max:1000'],
            'contact_no' => [$this->isPostRequest()],
        ];
    }

    private function isPostRequest() {
        return request()->isMethod('post') ? 'required' :'sometimes';
    }
}
