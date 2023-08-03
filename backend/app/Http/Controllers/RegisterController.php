<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate(
            [
                'name'            => 'required|min:3|string',
                'email'           => 'required|email',
                'password'        => 'required|string',
                'confirmPassword' => 'required|string',
            ],
            [
                'name.required'            => 'O campo nome é obrigatório',
                'name.min'                 => 'O campo nome deve ter pelo menos 3 caracteres',
                'email.required'           => 'O campo email é obrigatório',
                'email.unique'             => 'Já existe um usuário cadastrado com esse email',
                'password.required'        => 'O campo senha é obrigatório',
                'confirmPassword.required' => 'O campo confirmação de senha é obrigatório',
            ]
        );

        $email = User::where('email', $data['email'])->first();
        if ($email) {
            return response()->json('email já cadastrado', 201);
        }

        $password = $data['password'];
        $data['password'] =  password_hash(
            $password,
            PASSWORD_ARGON2I
        );

        User::factory()->createOne([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);
        return response()->json('usuario cadastrado com sucesso', 200);
    }
}
