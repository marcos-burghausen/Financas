<?php

namespace App\Http\Controllers;

use App\Http\Traits\ReleasesMonthTrait;
use Illuminate\Http\Request;

class BuscaDadosMesCntroller extends Controller
{
    use ReleasesMonthTrait;

    public function buscarDadosMes(Request $request)
    {
        $user = auth()->user();
        $userData = $this->getUserData($user, $request->mes, $request->buscar);
        return response()->json($userData);

        return response()->json([
            'userData' => $userData
        ]);


        $mes = $request->mes;
        $ano = $request->ano;
        $dados = [
            'mes' => $mes,
            'ano' => $ano
        ];
    }

    private function getUserData(object $user, string $data): array
    {
        return [
            'expensesData' => $this->classifiesReleases($user->expenses()->get(), 'Expenses', $data),
            'revenuesData' => $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data),
            'walletsData' => [
                'mes_ano_referencia' => $data,
                'wallets' => $user->contas()->get(),
                'walletsNames' => $user->contas()->pluck("name"),
                'saldoInicial' => $this->obterSaldoInicial($user, $data),
                'saldoAtual' => $this->obterSaldoAtual($user, $data),
            ],
        ];
    }
}
