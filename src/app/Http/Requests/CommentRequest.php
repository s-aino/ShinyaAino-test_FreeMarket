<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['comment' => ['required', 'string', 'max:255']];
    }

    public function messages(): array
    {
        return [
            'comment.required' => '商品コメントを入力してください。',
            'comment.max'      => '商品コメントは最大255文字です。',
        ];
    }

    public function attributes(): array
    {
        return ['comment' => '商品コメント'];
    }
}
