<?php

namespace App\Http\Traits;

use App\Models\CreditCardInvoice;
use App\Http\Traits\ReleasesMonthTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait UserDataTrait
{
    use ReleasesMonthTrait;

    /**
     * Busca os dados do usuário de forma seletiva.
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
                ->with('contaModel:id,name')
                ->where('tipo_lancamento', '!=', 'CARTAO_CREDITO')
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
                ->with('contaModel:id,name')
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
                    ->get(['id', 'name', 'icon', 'saldo', 'saldo_inicial', 'color', 'descricao', 'tipo_conta', 'incluir_em_soma_inicial']),

                'cartoes' => $user->contas()
                    ->where('tipo_conta', 'Cartão de Crédito')
                    ->with('parentAccount')
                    ->get(['id', 'name', 'icon', 'saldo', 'descricao', 'tipo_conta', 'incluir_em_soma_inicial', 'conta_pai_id', 'bandeira', 'limite', 'color'])
                    ->map(function ($cartao) {
                        // 1. Determina a competência da fatura atual/vigente.
                        $today = Carbon::today();
                        if ($today->day > $cartao->dia_fechamento) {
                            // Se o dia de hoje já passou do dia do fechamento, a fatura vigente é a do próximo mês.
                            $competenciaDate = $today->addMonth();
                        } else {
                            // Senão, a fatura vigente é a do mês atual.
                            $competenciaDate = $today;
                        }
                        $competencia = $competenciaDate->format('Y-m');

                        // 2. Busca a fatura no banco de dados.
                        $faturaVigente = CreditCardInvoice::where('conta_id', $cartao->id)
                            ->where('competencia', $competencia)
                            ->with('lancamentos')
                            ->first();

                        // Soma o (total da fatura - valor pago) para todas as faturas que não estão 'PAGA'.
                        $valorEmAberto = CreditCardInvoice::where('conta_id', $cartao->id)
                            ->where('status_fatura', '!=', 'PAGA')
                            ->sum(DB::raw('total_fatura - valor_pago'));

                        // 3. Adiciona os novos campos ao objeto do cartão.
                        $cartao->total_fatura = $faturaVigente ? $faturaVigente->total_fatura : 0;
                        $cartao->lancamentos_fatura = $faturaVigente ? $faturaVigente->lancamentos : [];
                        $cartao->conta_pai_name = $cartao->parentAccount ? $cartao->parentAccount->name : null;
                        $cartao->data_fechamento = $faturaVigente ? $faturaVigente->data_fechamento : null;
                        $cartao->data_vencimento = $faturaVigente ? $faturaVigente->data_vencimento : null;
                        $cartao->status_fatura = $faturaVigente ? $faturaVigente->status_fatura : null;
                        $cartao->valor_em_aberto = $valorEmAberto;


                        unset($cartao->parentAccount);

                        return $cartao;
                    }),

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
