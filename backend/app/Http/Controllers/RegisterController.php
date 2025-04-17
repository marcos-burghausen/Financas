<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Models\Conta;
use App\Models\User;
use App\Models\Wallets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;
use Pioneira\Security\Laravel\Facades\SecurityValidation;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate(
            [
                'name' => [
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
                'email.unique'             => 'Já existe um usuário cadastrado com esse email ',
                'email.regex'              => 'O campo email deve ter um formato válido',
                'password.required'        => 'O campo senha é obrigatório',
                'password.regex'           => 'A senha deve ter pelo menos 8 caracteres sendo uma letra maiúcula, uma minúscula, um número e um caracter especial exeto aspas simples e dupla',
                //              'confirmPassword.required' => 'O campo confirmação de senha é obrigatório',
            ]
        );

        $email = User::where('email', $data['email'])->first();
        if ($email) {
            return Errors::USER_ALREADY_REGISTERED->response();
        }

        try {
            DB::beginTransaction();

            $user                     = new User;
            $user->name               = $data['name'];
            $user->email              = $data['email'];
            $user->password           = Hash::make($data['password']);
            $user->categoriasDespesas = [];
            $user->categoriasReceitas = [];
            $user->save();

            $lastUser = User::latest('id')->first();

            $conta                          = new Conta;
            $conta->user_id                 = $lastUser->id;
            $conta->name                    = "Pessoal";
            $conta->icon                    = "cash";
            $conta->saldo_inicial           = 0;
            $conta->incluir_em_soma_inicial = true;
            $conta->descricao               = "Conta de uso pessoal";
            $conta->tipo                    = "Pessoal";
            $conta->save();
            DB::commit();
        } catch (\Throwable $e) {
            info($e);
            DB::rollBack();
            return Errors::USER_CREATE_FAILED->response();
        }

        return response()->json(['success' => 'Usuario cadastrado com sucesso.'], 200);
    }
}
