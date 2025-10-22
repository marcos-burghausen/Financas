<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Requests\RegisterRequest;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create(RegisterRequest $request)
    {
        $data = $request->validated();
        info($data);

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
            $user->save();

            $lastUser = User::latest('id')->first();

            $conta                          = new Account;
            $conta->user_id                 = $lastUser->id;
            $conta->name                    = "Carteira";
            $conta->icon                    = "mdi-cash";
            $conta->include_in_initial_sum  = true;
            $conta->description             = "Conta de uso pessoal";
            $conta->account_type            = "Carteira";
            $conta->save();
            DB::commit();
        } catch (\Throwable $e) {
            info($e);
            DB::rollBack();
            return Errors::USER_CREATE_FAILED->response();
        }

        // Gerar token Sanctum
        $token = $lastUser->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => 'Usuario cadastrado com sucesso.',
            'token' => $token,
            'user' => [
                'id' => $lastUser->id,
                'name' => $lastUser->name,
                'email' => $lastUser->email,
                'type' => $lastUser->type_user ?? 'USER'
            ]
        ], 200);
    }
}
