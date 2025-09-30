<?php

namespace App\Http\Traits;

use DateTime;
use App\Http\Traits\ReleasesMonthTrait;
use Carbon\Carbon;

trait UserDataTrait
{
    use ReleasesMonthTrait;

    /**
     * Busca os dados do usuário de forma seletiva.
     *
     * @param object $user O objeto do usuário.
     * @param string|null $date A data de referência no formato 'Y-m'. Se nulo, usa o mês atual.
     * @param array $sections As seções de dados a serem retornadas ('expenses', 'revenues', 'wallets'). Se vazio, retorna tudo.
     * @return array
     */

    private function getUserData(object $user, $date = null, array $sections = []): array
    {
        $mes = $date ?? Carbon::now()->format('Y-m');
        $fetchAll = empty($sections);
        $dataToReturn = [];

        $year = Carbon::parse($mes)->year;
        $month = Carbon::parse($mes)->month;

        // --- Seção de Despesas (Expenses) ---
        if ($fetchAll || in_array('expenses', $sections)) {
            $despesasDoMes = $user->expenses()
                ->whereYear('data_lancamento', $year)
                ->whereMonth('data_lancamento', $month)
                ->get();

            $categoriasDespesas = $user->categories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
            $subcategoriasDespesas = $user->subcategories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
            foreach ($categoriasDespesas as $categoria) {
                $subcategories = [];
                foreach ($subcategoriasDespesas as $subcategoria) {
                    if ($categoria->id == $subcategoria->category_id) {
                        $subcategories[] = $subcategoria->only(['id', 'name', 'color', 'icon', 'editable', 'type']);
                    }
                }
                $categoria->subcategories = $subcategories;
            }

            $dataToReturn['expenses'] = [
                ...$this->classifiesReleases($despesasDoMes, 'Expenses', $mes),
                "categories" => $categoriasDespesas,
            ];
        }

        // --- Seção de Receitas (Revenues) ---
        if ($fetchAll || in_array('revenues', $sections)) {
            $receitasDoMes = $user->revenues()
                ->whereYear('data_lancamento', $year)
                ->whereMonth('data_lancamento', $month)
                ->get();

            $categoriasReceitas = $user->categories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
            $subcategoriasReceitas = $user->subcategories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
            foreach ($categoriasReceitas as $categoria) {
                $subcategories = [];
                foreach ($subcategoriasReceitas as $subcategoria) {
                    if ($categoria->id == $subcategoria->category_id) {
                        $subcategories[] = $subcategoria->only(['id', 'name', 'color', 'icon', 'editable', 'type']);
                    }
                }
                $categoria->subcategories = $subcategories;
            }

            $dataToReturn["revenues"] = [
                ...$this->classifiesReleases($receitasDoMes, 'Revenues', $mes),
                "categories" => $categoriasReceitas,
            ];
        }

        // --- Seção de Carteiras (Wallets) ---
        if ($fetchAll || in_array('wallets', $sections)) {
            $dataToReturn['wallets'] = [
                'contas' => $user->contas()
                    ->where('tipo_conta', '!=', 'Cartão de Crédito')
                    ->get(['id', 'name', 'icon', 'saldo', 'saldo_inicial', 'descricao', 'tipo_conta', 'incluir_em_soma_inicial']),

                'cartoes' => $user->contas()
                    ->where('tipo_conta', 'Cartão de Crédito')
                    ->get(['id', 'name', 'icon', 'saldo', 'descricao', 'tipo_conta', 'incluir_em_soma_inicial', 'conta_pai_id', 'limite', 'dia_fechamento', 'dia_vencimento']),

                'contasNames' => $user->contas()->pluck("name"),
                'saldoInicial' => $this->obterSaldoInicial($user, $mes),
                'saldoAtual' => $this->obterSaldoAtual($user, $mes),
                "categories" => $user->categories()->where('type', 'contas')->get(['id', 'name', 'color', 'icon', 'editable', 'type']),
            ];
        }

        $dataToReturn['mesAno'] = $mes;

        return $dataToReturn;
    }
}
