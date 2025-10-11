<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'address'     => ['required', 'max:255'],
            'building'    => ['nullable', 'max:255'],
        ];
    }

    public function attributes()
    {
        return ['postal_code' => '郵便番号', 'address' => '住所', 'building' => '建物名'];
    }

    public function messages()
    {
        return ['postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。'];
    }
}
