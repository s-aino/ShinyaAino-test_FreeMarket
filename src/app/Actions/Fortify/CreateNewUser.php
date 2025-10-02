<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // RegisterRequest の rules/messages/attributes をそのまま使う
        $req = new RegisterRequest();

        Validator::make(
            $input,
            $req->rules(),
            method_exists($req, 'messages')   ? $req->messages()   : [],
            method_exists($req, 'attributes') ? $req->attributes() : []
        )->validate();

        return User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
