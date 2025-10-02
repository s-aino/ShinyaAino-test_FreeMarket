<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required','string','max:255'],   // 商品名
            'description' => ['required','string','max:255'],   // 設計表：必須 + 最大255
            'image'       => ['required','file','mimes:jpeg,png'],
            'category_id' => ['required','integer','exists:categories,id'],
            'condition'   => ['required','string'],             // 選択肢があるなら in: を後で追加
            'price'       => ['required','integer','min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max'      => '商品説明は最大255文字です。',
            'image.required'       => '商品画像を選択してください。',
            'image.mimes'          => '商品画像はjpegもしくはpngを指定してください。',
            'category_id.required' => '商品のカテゴリーを選択してください。',
            'category_id.exists'   => '選択したカテゴリーが不正です。',
            'condition.required'   => '商品の状態を選択してください。',
            'price.required'       => '商品価格を入力してください。',
            'price.integer'        => '商品価格は整数で入力してください。',
            'price.min'            => '商品価格は0円以上で入力してください。',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => '商品名',
            'description' => '商品説明',
            'image' => '商品画像',
            'category_id' => '商品のカテゴリー',
            'condition' => '商品の状態',
            'price' => '商品価格',
        ];
    }
}
