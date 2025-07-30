<?php

namespace App\Http\Traits;

use DateTime;
use App\Http\Traits\ReleasesMonthTrait;

trait UserDataTrait
{
    use ReleasesMonthTrait;

    private function getUserData(object $user): array
    {
        // Expenses (Despesas)
        $categoriasDespesas = $user->categories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
        $subcategoriasDespesas = $user->subcategories()->whereIn('type', ['ambas', 'despesa'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
        foreach ($categoriasDespesas as $categoria) {
            $subcategories = []; // Temporary array for subcategories
            foreach ($subcategoriasDespesas as $subcategoria) {
                if ($categoria->id == $subcategoria->category_id) {
                    $subcategories[] = [
                        'id' => $subcategoria->id,
                        'name' => $subcategoria->name,
                        'color' => $subcategoria->color,
                        'icon' => $subcategoria->icon,
                        'editable' => $subcategoria->editable,
                        'type' => $subcategoria->type
                    ];
                }
            }
            $categoria->subcategories = $subcategories; // Assign to a custom attribute
        }

        // Revenues (Receitas)
        $categoriasReceitas = $user->categories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'name', 'color', 'icon', 'editable', 'type']);
        $subcategoriasReceitas = $user->subcategories()->whereIn('type', ['ambas', 'receita'])->get(['id', 'category_id', 'name', 'color', 'icon', 'editable', 'type']);
        foreach ($categoriasReceitas as $categoria) {
            $subcategories = []; // Temporary array for subcategories
            foreach ($subcategoriasReceitas as $subcategoria) {
                if ($categoria->id == $subcategoria->category_id) {
                    $subcategories[] = [
                        'id' => $subcategoria->id,
                        'name' => $subcategoria->name,
                        'color' => $subcategoria->color,
                        'icon' => $subcategoria->icon,
                        'editable' => $subcategoria->editable,
                        'type' => $subcategoria->type
                    ];
                }
            }
            $categoria->subcategories = $subcategories; // Assign to a custom attribute
        }

        return [
            'expenses' => [
                ...$this->classifiesReleases($user->expenses()->get(), 'Expenses'),
                "categories" => [
                    ...$categoriasDespesas,
                ],
            ],
            "revenues" => [
                ...$this->classifiesReleases($user->revenues()->get(), 'Revenues'),
                "categories" => [
                    ...$categoriasReceitas,
                ],
            ],
            'wallets'         => [
                'contas'             => $user->contas()->get(['id', 'name', 'icon', 'saldo', 'saldoInicial', 'descricao', 'tipo', 'incluirEmSomaInicial']),
                'contasNames'       => $user->contas()->pluck("name"),
                'saldoInicial'       => $this->obterSaldoInicial($user),
                // 'saldoAtual' => $this->obterSaldoAtual($user),
                "categories" => [
                    ...$user->categories()->where('type', 'contas')->get(['id', 'name', 'color', 'icon', 'editable', 'type']),
                ],
            ],
            'mesAno' => date('Y-m'),
        ];
    }

}
