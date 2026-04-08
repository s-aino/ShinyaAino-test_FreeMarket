<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'コメントを入力してください。',
            'body.string'   => 'コメントの形式が正しくありません。',
            'body.max'      => 'コメントは255文字以内で入力してください。',
        ];
    }

    public function attributes(): array
    {
        return [
            'body' => '商品コメント',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->to(url()->previous() . '#comment-body')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
