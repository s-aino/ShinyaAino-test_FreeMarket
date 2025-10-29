<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'], // 商品名
            'description' => ['required', 'string', 'max:255'], // 商品説明（最大255文字）
            'image' => ['required', 'image', 'mimes:jpeg,png'], // 拡張子制限
            'categories' => ['required', 'array'], // 商品のカテゴリー
            'categories.*' => ['exists:categories,id'],
            'condition' => ['required', 'string'], // 商品の状態
            'price' => ['required', 'integer', 'min:0'], // 0円以上
            'brand' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '商品名は必須です。',
            'description.required' => '商品説明は必須です。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '商品画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像は.jpeg または .png 形式のみ対応しています。',
            'categories.required' => '商品カテゴリーを選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'price.required' => '商品価格は必須です。',
            'price.integer' => '商品価格は数値で入力してください。',
            'price.min' => '商品価格は0円以上で入力してください。',
            'brand.string' => 'ブランド名は文字列で入力してください。', 
            'brand.max' => 'ブランド名は255文字以内で入力してください。', 
        ];
    }
}
