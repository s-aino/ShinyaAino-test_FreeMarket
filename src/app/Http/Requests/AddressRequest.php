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
            'postal'   => ['required', 'regex:/^\d{3}-\d{4}$/'], // 123-4567
            'address'  => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'postal'   => '郵便番号',
            'address'  => '住所',
            'building' => '建物名',
        ];
    }

    public function messages(): array
    {
        return [
            'postal.regex' => '郵便番号はハイフンありの 8 文字で入力してください。（例: 123-4567）',
        ];
    }
}
