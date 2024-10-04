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
            ->where('incluir_em_soma_inicial', true)
            ->sum('saldo');
        // $despesas = $user->expenses()->get();
        // $receitas = $user->revenues()->get();
        info($somaSaldo);
        $ano = date('Y');
        $mes = date('m');

        $dataLimite = (new DateTime("$ano-$mes-01"))->modify('-1 day')->format('Y-m-d');

        foreach ($contas as $conta) {
            if ($conta->incluir_em_soma_inicial) {
                $despesas = $user->expenses()
                    ->where('carteira', $conta->name)
                    ->where('status', 'PAGA')
                    ->where('date', '<=', $dataLimite)
                    ->sum('valor');
                // ->get();
                $receitas = $user->revenues()
                    ->where('carteira', $conta->name)
                    ->where('status', 'RECEBIDA')
                    ->where('date', '<=', $dataLimite)
                    ->sum('valor');
                info('despesas ' . $receitas);
            }
        }
        return 1000 / 100;
    }
}
