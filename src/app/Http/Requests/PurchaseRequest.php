<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'payment_method' => 'required|in:conveni,card',
            'address_id'     => 'required|exists:addresses,id',
        ];
    }

    public function attributes()
    {
        return ['payment_method' => '支払い方法', 'address_id' => '配送先'];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in'       => '支払い方法の選択が不正です。',
            'address_id.required'     => '配送先を登録してください。',
            'address_id.exists'       => '配送先が見つかりません。',
        ];
    }
}
