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
            'name'          => ['required', 'string', 'max:20'],
            'postal'        => ['required', 'regex:/^\d{3}-\d{4}$/'], 
            'address'       => ['required', 'string', 'max:255'],
            'building'      => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:4096'], 
        ];
    }
    public function attributes(): array
    {
        return [
            'name'          => 'ユーザー名',
            'postal'        => '郵便番号',
            'address'       => '住所',
            'building'      => '建物名',
            'profile_image' => 'プロフィール画像',
        ];
    }

    public function messages(): array
    {
        return [
            'postal.regex'         => '郵便番号はハイフンありの 8 文字で入力してください。',
            'profile_image.image'  => '画像ファイルを選択してください。',
            'profile_image.mimes'  => '拡張子は jpeg もしくは png を指定してください。',
            'profile_image.max'    => '画像サイズは 4MB 以下にしてください。',
        ];
    }
}
