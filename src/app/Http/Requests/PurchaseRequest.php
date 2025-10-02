<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string'],   // 必要なら in:card,bank,cod など
            'address_id'     => ['required', 'integer', 'exists:addresses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'address_id.required'     => '配送先を選択してください。',
            'address_id.exists'       => '選択した配送先が不正です。',
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method' => '支払い方法',
            'address_id'     => '配送先',
        ];
    }
}
