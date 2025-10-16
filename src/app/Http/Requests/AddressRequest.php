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
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'line1'       => ['required', 'string', 'max:255'],
            'line2'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'postal_code' => '郵便番号',
            'line1' => '住所',
            'line2' => '建物名',
        ];
    }
}
