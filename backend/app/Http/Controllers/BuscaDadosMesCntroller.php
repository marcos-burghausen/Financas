<?php

namespace App\Http\Controllers;

use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\UserDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BuscaDadosMesCntroller extends Controller
{
    use ReleasesMonthTrait, UserDataTrait;

    /**
     * Busca dados do mês com cache de 5 minutos
     * Isso reduz a carga no servidor e melhora a performance
     */
    public function buscarDadosMes(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->mesAno ?? now()->format('Y-m');
        $cacheKey = "user_data_{$user->id}_month_{$mesAno}";
        
        // Cache por 5 minutos (300 segundos)
        // Para um notebook antigo, isso reduz significativamente a carga
        $userData = Cache::remember($cacheKey, 300, function () use ($user, $mesAno) {
            return $this->getUserData($user, $mesAno, [
                'expenses',
                'revenues',
                'wallets'
            ]);
        });
        
        return response()->json($userData);
    }

    /**
     * Endpoint para limpar o cache quando houver alterações
     */
    public function limparCache(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->mesAno ?? now()->format('Y-m');
        $cacheKey = "user_data_{$user->id}_month_{$mesAno}";
        
        Cache::forget($cacheKey);
        
        // Também limpa o cache do dashboard
        $dashboardCacheKey = "dashboard_summary_user_{$user->id}_month_{$mesAno}";
        Cache::forget($dashboardCacheKey);
        
        return response()->json(['message' => 'Cache limpo com sucesso']);
    }

    // private function getUserData(object $user, string $mesAno): array
    // {
    //     return [
    //         'expensesData' => $this->classifiesReleases($user->expenses()->get(), 'Expenses', $mesAno),
    //         'revenuesData' => $this->classifiesReleases($user->revenues()->get(), 'Revenues', $mesAno),
    //         'walletsData' => [
    //             'wallets' => $user->contas()->get(),
    //             'walletsNames' => $user->contas()->pluck("name"),
    //             'saldoInicial' => $this->obterSaldoInicial($user, $mesAno),
    //             'saldoAtual' => $this->obterSaldoAtual($user, $mesAno),
    //         ],
    //         'mesAno' => $mesAno,
    //     ];
    // }
}
