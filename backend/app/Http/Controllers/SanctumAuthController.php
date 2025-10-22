<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\CacheKeys;
use App\Enums\CacheNaming;
use App\Enums\Errors;
use App\Http\Traits\UserDataTrait;
use App\Mail\NotificationMail;
use App\Models\User;
use App\Utils\FinancasCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SanctumAuthController extends Controller
{
    use UserDataTrait;

    /**
     * Login com Sanctum
     */
    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'required' => 'O campo :attribute é obrigatório',
                'email.email' => 'O email precisa ter um formato válido',
            ],
            [
                'password' => 'senha'
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            LogController::addsLog($request->email, Actions::USER_OR_PASSWORD_INVALID);
            return Errors::INVALID_USERNAME_OR_PASSWORD->response();
        }

        // Revoga todos os tokens anteriores do usuário (previne tokens antigos)
        $user->tokens()->delete();

        // Cria um novo token (SEMPRE gera novo, não usar cache aqui!)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Cache por 10 minutos
        FinancasCache::put(CacheKeys::FLOW_TITLE->append($request->email), [
            CacheNaming::EMAIL->value => $request->email,
        ], 30);

        $mesAno = $request->query('mesAno', now()->format('Y-m'));

        // IMPORTANTE: Não cachear o token! Apenas os dados do usuário
        // Busca apenas o resumo do dashboard no login (performance)
        $userData = $this->getUserData($user, $mesAno, ['summary']);

        $loginData = [
            'token' => $token, // Token SEMPRE novo, nunca em cache
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type_user,
            ],
            'mesAno' => $mesAno,
            'summary' => $userData['summary'],
        ];

        LogController::addsLog($request->email, Actions::LOGIN);
        Mail::to('rafaelburghausen@gmail.com')->queue(new NotificationMail($user, 'Login', 'Login', $user->name));

        return response()->json($loginData);
    }

    /**
     * Retorna dados do usuário autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return Errors::ERROR_WHILE_GETTING_USER_DATA->response();
        }

        // Busca todos os dados do usuário
        $userData = $this->getUserData($user);

        LogController::addsLog($user->email, Actions::ME);

        return response()->json($userData);
    }

    /**
     * Logout - revoga o token atual
     */
    public function logout(Request $request)
    {
        // Revoga o token atual do usuário
        $request->user()->currentAccessToken()->delete();

        LogController::addsLog($request->user()->email, Actions::LOGOUT);

        return response()->json(['message' => 'Logout realizado com sucesso!']);
    }

    /**
     * Revoga todos os tokens do usuário
     */
    public function logoutAll(Request $request)
    {
        // Revoga TODOS os tokens do usuário
        $request->user()->tokens()->delete();

        LogController::addsLog($request->user()->email, Actions::LOGOUT);

        return response()->json(['message' => 'Todos os tokens foram revogados!']);
    }
}
