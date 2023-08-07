<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        //autenticação (email e senha)
        $token = auth('api')->attempt($credentials);

        if ($token) { //usuário autenticado com sucesso
            return $this->respondWithToken($token);
        } else { //erro de usuário ou senha
            return response()->json(['erro' => 'Usuário ou senha inválido!'], 403);

            //401 = Unauthorized -> não autorizado
            //403 = forbidden -> proibido (login inválido)
        }
        return response()->json(['token' => $token]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 30
        ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function me()
    {
        return response()->json(['user' => auth()->user()]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi realizado com sucesso!']);
    }
}
