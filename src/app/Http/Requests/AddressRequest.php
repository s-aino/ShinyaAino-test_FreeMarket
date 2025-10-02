<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postal'  => ['required', 'regex:/^\d{3}-\d{4}$/'], // ハイフンあり8文字
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'postal.required' => '郵便番号を入力してください。',
            'postal.regex'    => '郵便番号はハイフンありの8文字で入力してください。',
            'address.required' => '住所を入力してください。',
        ];
    }

    public function attributes(): array
    {
        return ['postal' => '郵便番号', 'address' => '住所'];
    }
}
