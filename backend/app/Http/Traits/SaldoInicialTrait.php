<?php

namespace App;

trait SaldoInicialTrait
{
    public function obterSaldoInicial(object $user, string $mes): float
    {
        // Verifica se existe um saldo inicial para o usuário e tipo de conta
        $user = auth()->user();
        $contas = $user->contas()->get();
        $somaSaldo = $user->contas()
            ->where('incluirEmSomaInicial', true)
            ->sum('saldo');
        // $despesas = $user->expenses()->get();
        // $receitas = $user->revenues()->get();
        info($somaSaldo);
        $ano = date('Y');
        $mes = date('m');

        $dataLimite = (new DateTime("$ano-$mes-01"))->modify('-1 day')->format('Y-m-d');

        foreach ($contas as $conta) {
            if ($conta->incluirEmSomaInicial) {
                $despesas = $user->expenses()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataVencimento', '<=', $dataLimite)
                    ->sum('valor');
                // ->get();
                $receitas = $user->revenues()
                    ->where('conta', $conta->name)
                    ->where('status', 'Efetivada')
                    ->where('dataVencimento', '<=', $dataLimite)
                    ->sum('valor');
                info('despesas ' . $receitas);
            }
        }
        return 1000 / 100;
    }
}
