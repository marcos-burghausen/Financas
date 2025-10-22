<?php

namespace App\Http\Traits;

use App\Models\CreditCardInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait UserDataTrait
{
    use ReleasesMonthTrait;

    /**
     * Busca os dados do usuário de forma seletiva.
     *
     * @param object $user O objeto do usuário.
     * @param string|null $date A data de referência no formato 'Y-m'.
     * @param array $sections As seções de dados a serem retornadas.
     * @return array
     */
    // CORRIGIDO: O tipo do parâmetro $user foi alterado de 'iterable' para 'object'.
    private function getUserData(object $user, $date = null, array $sections = []): array
    {
        $mes = $date ?? Carbon::now()->format('Y-m');
        $fetchAll = empty($sections);
        $dataToReturn = [];

        $year = Carbon::parse($mes)->year;
        $month = Carbon::parse($mes)->month;
        $startDate = Carbon::parse($mes)->startOfMonth();
        $endDate = Carbon::parse($mes)->endOfMonth();

        // --- Seção de Despesas (Expenses) ---
        if ($fetchAll || in_array('expenses', $sections)) {
            $standardExpenses = $user->expenses()
                ->with('accountModel:id,name')
                ->where('launch_type', '!=', 'CARTAO_CREDITO')
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $month)
                ->get();

            $creditCardInvoices = CreditCardInvoice::whereHas('accountCreditCard', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with('accountCreditCard:id,name,icon')
                ->whereBetween('due_date', [$startDate, $endDate])
                ->get();

            $invoiceExpenses = $creditCardInvoices->map(function ($invoice) {
                $statusLancamento = $invoice->status_invoice === 'PAGA' ? 'EFETIVADA' : 'PENDENTE';

                return (object) [
                    'id' => 'invoice_' . $invoice->id,
                    'description' => 'Fatura ' . $invoice->accountCreditCard->name,
                    'value' => $invoice->total_invoice,
                    'launch_status' => $statusLancamento,
                    'due_date' => $invoice->due_date->format('Y-m-d'),
                    'launch_date' => 'FATURA_CARTAO',
                    'category' => 'Fatura de Cartão',
                    'accountModel' => $invoice->accountCreditCard,
                    'invoice' => $invoice,
                ];
            });

            $despesasDoMes = $standardExpenses->concat($invoiceExpenses);
            info('Lista de Despesas Final (Após junção):', $despesasDoMes->toArray());

            $categoriasDespesas = $user->categories()->whereIn('type', ['ambas', 'despesa'])->get();
            $subcategoriasDespesas = $user->subcategories()->whereIn('type', ['ambas', 'despesa'])->get();
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
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $month)
                ->get();

            $categoriasReceitas = $user->categories()->whereIn('type', ['ambas', 'receita'])->get();
            $subcategoriasReceitas = $user->subcategories()->whereIn('type', ['ambas', 'receita'])->get();
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

        // --- Seção de Resumo do Dashboard (Summary) ---
        if ($fetchAll || in_array('summary', $sections)) {
            $dataToReturn['summary'] = [
                'saldoAtual' => DB::table('accounts')
                    ->where('user_id', $user->id)
                    ->where('account_type', '!=', 'Cartão de Crédito')
                    ->sum('balance'),
                'totalReceitas' => DB::table('launches')
                    ->where('user_id', $user->id)
                    ->where('launch_type', 'RECEITA')
                    ->whereYear('due_date', $year)
                    ->whereMonth('due_date', $month)
                    ->sum('value'),
                'totalDespesas' => DB::table('launches')
                    ->where('user_id', $user->id)
                    ->where('launch_type', 'DESPESA')
                    ->whereYear('due_date', $year)
                    ->whereMonth('due_date', $month)
                    ->sum('value'),
            ];
        }

        // --- Seção de Carteiras (Wallets) ---
        if ($fetchAll || in_array('accounts', $sections)) {
            $dataToReturn['accounts'] = [
                'contas' => $user->account()
                    ->where('account_type', '!=', 'Cartão de Crédito')
                    ->get()
                    ->map(function ($conta) use ($year, $month) {
                        $totalReceitas = DB::table('launches')
                            ->where('account_id', $conta->id)
                            ->where('launch_type', 'RECEITA')
                            ->whereYear('due_date', $year)
                            ->whereMonth('due_date', $month)
                            ->sum('value');

                        $totalDespesas = DB::table('launches')
                            ->where('conta_id', $conta->id)
                            ->where('launch_type', 'DESPESA')
                            ->whereYear('due_date', $year)
                            ->whereMonth('due_date', $month)
                            ->sum('value');

                        $conta->saldo_previsto = $conta->saldo_inicial + $totalReceitas - $totalDespesas;

                        return $conta;
                    }),

                'cartoes' => $user->account()
                    ->where('account_type', 'Cartão de Crédito')
                    ->with('parentAccount')
                    ->get(['id', 'name', 'icon', 'balance', 'description', 'account_type', 'include_in_initial_sum', 'parent_account_id', 'limit', 'color', 'closing_day', 'due_day'])
                    ->map(function ($cartao) {
                        $today = Carbon::today();
                        if ($today->day > $cartao->closing_day) {
                            $competenciaDate = (clone $today)->addMonth();
                        } else {
                            $competenciaDate = $today;
                        }
                        $competencia = $competenciaDate->format('Y-m');

                        $faturaVigente = CreditCardInvoice::where('account_id', $cartao->id)
                            ->where('competence', $competencia)
                            ->with('launches')
                            ->first();

                        $valorEmAberto = CreditCardInvoice::where('account_id', $cartao->id)
                            ->where('status_invoice', '!=', 'PAGA')
                            ->sum(DB::raw('total_invoice - value_pay'));

                        $cartao->total_fatura_vigente = $faturaVigente ? $faturaVigente->total_fatura : 0;
                        $cartao->lancamentos_fatura_vigente = $faturaVigente ? $faturaVigente->lancamentos : [];
                        $cartao->conta_pai_name = $cartao->parentAccount ? $cartao->parentAccount->name : null;
                        $cartao->data_fechamento = $faturaVigente ? $faturaVigente->data_fechamento : null;
                        $cartao->data_vencimento = $faturaVigente ? $faturaVigente->data_vencimento : null;
                        $cartao->status_fatura = $faturaVigente ? $faturaVigente->status_fatura : 'INEXISTENTE';
                        $cartao->valor_em_aberto = $valorEmAberto;

                        unset($cartao->parentAccount);

                        return $cartao;
                    }),

                'contasNames' => $user->account()->pluck("name"),
                'saldoAtual' => $this->obterSaldoAtual($user, $mes),
                "categories" => $user->categories()->where('type', 'account')->get(),
            ];
        }

        $dataToReturn['mesAno'] = $mes;

        return $dataToReturn;
    }
}
