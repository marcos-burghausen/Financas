<?php

namespace App\Http\Controllers;

use App\Http\Traits\UserDataTrait;
use Illuminate\Http\Request;

class UserDataController extends Controller
{
    use UserDataTrait;

    /**
     * Busca apenas despesas do mês
     */
    public function getExpenses(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->query('mesAno', now()->format('Y-m'));

        // Cache por 5 minutos
        $cacheKey = "expenses_user_{$user->id}_month_{$mesAno}";
        $data = cache()->remember($cacheKey, 300, function () use ($user, $mesAno) {
            return $this->getUserData($user, $mesAno, ['expenses']);
        });

        return response()->json($data);
    }

    /**
     * Busca apenas receitas do mês
     */
    public function getRevenues(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->query('mesAno', now()->format('Y-m'));

        // Cache por 5 minutos
        $cacheKey = "revenues_user_{$user->id}_month_{$mesAno}";
        $data = cache()->remember($cacheKey, 300, function () use ($user, $mesAno) {
            return $this->getUserData($user, $mesAno, ['revenues']);
        });

        return response()->json($data);
    }

    /**
     * Busca apenas carteiras
     */
    public function getWallets(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->query('mesAno', now()->format('Y-m'));

        // Cache por 5 minutos
        $cacheKey = "wallets_user_{$user->id}_month_{$mesAno}";
        $data = cache()->remember($cacheKey, 300, function () use ($user, $mesAno) {
            return $this->getUserData($user, $mesAno, ['wallets']);
        });

        return response()->json($data);
    }

    /**
     * Invalida o cache do usuário
     */
    public function invalidateCache(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->input('mesAno');

        if ($mesAno) {
            cache()->forget("expenses_user_{$user->id}_month_{$mesAno}");
            cache()->forget("revenues_user_{$user->id}_month_{$mesAno}");
            cache()->forget("wallets_user_{$user->id}_month_{$mesAno}");
            cache()->forget("login_data_user_{$user->id}_month_{$mesAno}");
        } else {
            cache()->flush(); // Use com cuidado em produção
        }

        return response()->json(['message' => 'Cache invalidado com sucesso']);
    }
}
