<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Models\User;
use App\Models\Wallets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pioneira\Security\Laravel\Facades\SecurityValidation;

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
            return response()->json(Errors::USER_ALREADY_REGISTERED->response());
        }

        $password = $data['password'];
        $data['password'] =  password_hash(
            $password,
            PASSWORD_ARGON2I
        );

        $categoriasDespesasDefault = [
            ['name' => 'Casa',       'cor' => 'cor__1', 'icon' => 'home-outline',           'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Transporte', 'cor' => 'cor__2', 'icon' => 'car-estate',             'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Educação',   'cor' => 'cor__3', 'icon' => 'account-school-outline', 'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Lazer',      'cor' => 'cor__4', 'icon' => 'umbrella-beach-outline', 'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Vestuario',  'cor' => 'cor__5', 'icon' => 'tshirt-crew-outline',    'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Viagem',     'cor' => 'cor__6', 'icon' => 'airplane',               'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Saúde',      'cor' => 'cor__7', 'icon' => 'medical-bag',            'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Outros',     'cor' => 'cor__8', 'icon' => 'dots-horizontal',        'edit' => false, 'typeCategory' => 'despesa'],
        ];

        $categoriasReceitasDefault = [
            ['name' => 'Salario',       'cor' => 'cor__12', 'icon' => 'currency-usd',    'edit' => false, 'typeCategory' => 'recita'],
            ['name' => 'Investimentos', 'cor' => 'cor__11', 'icon' => 'finance',         'edit' => false, 'typeCategory' => 'recita'],
            ['name' => 'Outros',        'cor' => 'cor__7',  'icon' => 'dots-horizontal', 'edit' => false, 'typeCategory' => 'recita'],
        ];

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->categoriasDespesas = $categoriasDespesasDefault;
        $user->categoriasReceitas = $categoriasReceitasDefault;
        $user->carteiras          = ['Pessoal'];
        $user->save();

        return response()->json('usuario cadastrado com sucesso', 200);
    }
}
