<?php

namespace App\Http\Controllers;

use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\UserDataTrait;
use Illuminate\Http\Request;

class BuscaDadosMesCntroller extends Controller
{
    use ReleasesMonthTrait, UserDataTrait;

    public function buscarDadosMes(Request $request)
    {
        $user = auth()->user();
        $userData = $this->getUserData($user, $request->mesAno, [
            'expenses',
            'revenues',
            'wallets'
        ]);
        return response()->json($userData);
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
