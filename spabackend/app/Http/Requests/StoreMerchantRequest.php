<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerchantRequest extends FormRequest
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
            'name' => [$this->isPostRequest(), 'string', 'max:500'],
            'bus_contact_name' => [$this->isPostRequest(), 'string'],
            'bus_contact_no' => [$this->isPostRequest()],
            'bus_email' => [$this->isPostRequest(), 'string', 'email', 'max:500'],
            'bus_address' => [$this->isPostRequest(), 'string', 'max:1000'],
        ];
    }

    private function isPostRequest() {
        return request()->isMethod('post') ? 'required' :'sometimes';
    }
}
