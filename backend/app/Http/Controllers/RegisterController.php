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
                'name' =>[
                    'required',
                    'min:3',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'email' => [
                    'required',
                    'regex:/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',
                ],
                'password' => [
                    'required',
                    'regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\\¨~?´`!@#$%^&*()_+={}:;.<>,|\[\]\-\/])[0-9a-zA-Z\\¨~?´`!@#$%^&*()_+={}:;.<>,|\[\]\-\/]{8,}$/',
                ]
                // 'confirmPassword' => 'required|string',
            ],
            [
                'name.required'            => 'O campo nome é obrigatório',
                'name.min'                 => 'O campo nome deve ter pelo menos 3 caracteres',
                'name.regex'               => 'O campo nome deve conter apenas letras',
                'email.required'           => 'O campo email é obrigatório',
                'email.unique'             => 'Já existe um usuário cadastrado com esse email',
                'password.required'        => 'O campo senha é obrigatório',
  //              'confirmPassword.required' => 'O campo confirmação de senha é obrigatório',
            ]
        );

        $email = User::where('email', $data['email'])->first();
        if ($email) {
            return Errors::USER_ALREADY_REGISTERED->response();
        }

        $password = $data['password'];
        $data['password'] =  password_hash(
            $password,
            PASSWORD_ARGON2I
        );

        $categoriasDespesasDefault = [
            ['name' => 'Casa',       'color' => 'cor__1', 'icon' => 'home-outline',           'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Transporte', 'color' => 'cor__2', 'icon' => 'car-estate',             'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Educação',   'color' => 'cor__3', 'icon' => 'account-school-outline', 'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Lazer',      'color' => 'cor__4', 'icon' => 'umbrella-beach-outline', 'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Vestuario',  'color' => 'cor__5', 'icon' => 'tshirt-crew-outline',    'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Viagem',     'color' => 'cor__6', 'icon' => 'airplane',               'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Saúde',      'color' => 'cor__7', 'icon' => 'medical-bag',            'edit' => false, 'typeCategory' => 'despesa'],
            ['name' => 'Outros',     'color' => 'cor__8', 'icon' => 'dots-horizontal',        'edit' => false, 'typeCategory' => 'despesa'],
        ];

        $categoriasReceitasDefault = [
            ['name' => 'Salario',       'color' => 'cor__12', 'icon' => 'currency-usd',    'edit' => false, 'typeCategory' => 'receita'],
            ['name' => 'Investimentos', 'color' => 'cor__11', 'icon' => 'finance',         'edit' => false, 'typeCategory' => 'receita'],
            ['name' => 'Outros',        'color' => 'cor__7',  'icon' => 'dots-horizontal', 'edit' => false, 'typeCategory' => 'receita'],
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
