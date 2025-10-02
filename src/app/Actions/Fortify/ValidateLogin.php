<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;

class ValidateLogin
{
    public function __invoke($request)
    {
        $form = new LoginRequest();

        Validator::make(
            $request->all(),
            $form->rules(),
            method_exists($form, 'messages') ? $form->messages() : [],
            method_exists($form, 'attributes') ? $form->attributes() : []
        )->validate();
    }
}
