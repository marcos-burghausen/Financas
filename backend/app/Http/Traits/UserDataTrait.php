<?php

namespace App\Http\Traits;

use DateTime;
use App\Http\Traits\ReleasesMonthTrait;

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
        $mes = $date ?? date('Y-m');

        $fetchAll = empty($sections);

        $dataToReturn = [];

        // --- Seção de Despesas (Expenses) ---
        if ($fetchAll || in_array('expenses', $sections)) {
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
                ...$this->classifiesReleases($user->expenses()->get(), 'Expenses', $mes),
                "categories" => $categoriasDespesas,
            ];
        }

        // --- Seção de Receitas (Revenues) ---
        if ($fetchAll || in_array('revenues', $sections)) {
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
                ...$this->classifiesReleases($user->revenues()->get(), 'Revenues', $mes),
                "categories" => $categoriasReceitas,
            ];
        }

        // --- Seção de Carteiras (Wallets) ---
        if ($fetchAll || in_array('wallets', $sections)) {
            $dataToReturn['wallets'] = [
                'contas' => $user->contas()
                    ->where('tipoConta', '!=', 'Cartão de Crédito')
                    ->get(['id', 'name', 'icon', 'saldo', 'saldoInicial', 'descricao', 'tipoConta', 'incluirEmSomaInicial']),

                'cartoes' => $user->contas()
                    ->where('tipoConta', 'Cartão de Crédito')
                    ->get(['id', 'name', 'icon', 'saldo', 'descricao', 'tipoConta', 'incluirEmSomaInicial', 'conta', 'limite', 'dia_fechamento', 'dia_vencimento']),

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
