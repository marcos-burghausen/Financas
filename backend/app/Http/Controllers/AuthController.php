<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Enums\CacheNaming;
use App\Http\Traits\ReleasesMonthTrait;
use App\Utils\PioneiraCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    use ReleasesMonthTrait;

    public function auth(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        //autenticação (email e senha)
        $token = auth('api')->attempt($credentials);
        return redirect('/api/)

        if ($token) { //usuário autenticado com sucesso
            /* O metodo put espera 3 valores
               o primeiro valor é o valor da chve como se fosse o nome de uma variavel
               o segundo valor é valor da chave
               o terceiro é o tempo em segundos que a informação vai permanecer no banco
            */
            // Cache::put('login', 'logado com sucesso', 60);
            PioneiraCache::put(CacheKeys::FLOW_TITLE->append($request->email), [
                CacheNaming::EMAIL->value       => $request->email,
            ], 60);
            $token = $this->respondWithToken($token);
            return response()->json($token->original);
        } else { //erro de usuário ou senha
            return response()->json(['erro' => 'Usuário ou senha inválido!'], 403);

            //401 = Unauthorized -> não autorizado
            //403 = forbidden -> proibido (login inválido)
        }
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
        $user = auth()->user();

        $expenses = DB::table('expenses')
            ->where('user_id', $user->id)
            ->get();

        $totalExpenses = $this->valueReleasesMonth($expenses, date('m'));

        $totalReveues = 5000;
        $totalCreditCard = 5000;
        $totalBalance = 5000;

        return response()->json([
            'userName' => $user->name,
            'totalExpenses' => $totalExpenses,
            'totalReveues'  => $totalReveues,
            'totalCreditCard'  => $totalCreditCard,
            'totalBalance'  => $totalBalance,
        ]);
        // return response()->json(['user' => auth()->user()]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi realizado com sucesso!']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $new_token = auth('api')->refresh();
        return $this->respondWithToken($new_token);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 30
        ], 200);
    }
}
