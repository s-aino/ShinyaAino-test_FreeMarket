<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_image' => ['nullable', 'file', 'mimes:jpeg,png'],
            'name'          => ['required', 'string', 'max:20'],
            'postal'        => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'       => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.mimes' => 'プロフィール画像はjpegもしくはpngを指定してください。',
            'name.required'       => 'ユーザー名を入力してください。',
            'name.max'            => 'ユーザー名は20文字以内で入力してください。',
            'postal.required'     => '郵便番号を入力してください。',
            'postal.regex'        => '郵便番号はハイフンありの8文字で入力してください。',
            'address.required'    => '住所を入力してください。',
        ];
    }

    public function attributes(): array
    {
        return [
            'profile_image' => 'プロフィール画像',
            'name' => 'ユーザー名',
            'postal' => '郵便番号',
            'address' => '住所',
        ];
    }
}
