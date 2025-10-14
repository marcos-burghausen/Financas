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
                ->with('contaModel:id,name')
                ->where('tipo_lancamento', '!=', 'CARTAO_CREDITO')
                ->whereYear('data_vencimento', $year)
                ->whereMonth('data_vencimento', $month)
                ->get();

            $creditCardInvoices = CreditCardInvoice::whereHas('conta', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with('conta:id,name,icon')
                ->whereBetween('data_vencimento', [$startDate, $endDate])
                ->get();

            // --- INÍCIO DA DEPURAÇÃO ---
            // 2. Adicionados logs para verificar o conteúdo das variáveis.
            info('Despesas Padrão Encontradas:', $standardExpenses->toArray());
            info('Faturas de Cartão Encontradas:', $creditCardInvoices->toArray());
            // --- FIM DA DEPURAÇÃO ---

            $invoiceExpenses = $creditCardInvoices->map(function ($invoice) {
                $statusLancamento = $invoice->status_fatura === 'PAGA' ? 'EFETIVADA' : 'PENDENTE';

                return (object) [
                    'id' => 'invoice_' . $invoice->id,
                    'descricao' => 'Fatura ' . $invoice->conta->name,
                    'valor' => $invoice->total_fatura,
                    'status_lancamento' => $statusLancamento,
                    'data_vencimento' => $invoice->data_vencimento->format('Y-m-d'),
                    'tipo_lancamento' => 'FATURA_CARTAO',
                    'categoria' => 'Fatura de Cartão',
                    'contaModel' => $invoice->conta,
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
                ->whereYear('data_vencimento', $year)
                ->whereMonth('data_vencimento', $month)
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

        // --- Seção de Carteiras (Wallets) ---
        if ($fetchAll || in_array('wallets', $sections)) {
            $dataToReturn['wallets'] = [
                'contas' => $user->contas()
                    ->where('tipo_conta', '!=', 'Cartão de Crédito')
                    ->get()
                    ->map(function ($conta) {
                        $totalReceitas = DB::table('lancamentos')
                            ->where('conta_id', $conta->id)
                            ->where('tipo_lancamento', 'RECEITA')
                            ->sum('valor');

                        $totalDespesas = DB::table('lancamentos')
                            ->where('conta_id', $conta->id)
                            ->where('tipo_lancamento', 'DESPESA')
                            ->sum('valor');

                        $conta->saldo_previsto = $conta->saldo_inicial + $totalReceitas - $totalDespesas;

                        return $conta;
                    }),

                'cartoes' => $user->contas()
                    ->where('tipo_conta', 'Cartão de Crédito')
                    ->with('parentAccount')
                    ->get(['id', 'name', 'icon', 'saldo', 'descricao', 'tipo_conta', 'incluir_em_soma_inicial', 'conta_pai_id', 'limite', 'color', 'dia_fechamento', 'dia_vencimento'])
                    ->map(function ($cartao) {
                        $today = Carbon::today();
                        if ($today->day > $cartao->dia_fechamento) {
                            $competenciaDate = (clone $today)->addMonth();
                        } else {
                            $competenciaDate = $today;
                        }
                        $competencia = $competenciaDate->format('Y-m');

                        $faturaVigente = CreditCardInvoice::where('conta_id', $cartao->id)
                            ->where('competencia', $competencia)
                            ->with('lancamentos')
                            ->first();

                        $valorEmAberto = CreditCardInvoice::where('conta_id', $cartao->id)
                            ->where('status_fatura', '!=', 'PAGA')
                            ->sum(DB::raw('total_fatura - valor_pago'));

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

                'contasNames' => $user->contas()->pluck("name"),
                'saldoInicial' => $this->obterSaldoInicial($user, $mes),
                'saldoAtual' => $this->obterSaldoAtual($user, $mes),
                "categories" => $user->categories()->where('type', 'contas')->get(),
            ];
        }

        $dataToReturn['mesAno'] = $mes;

        return $dataToReturn;
    }
}
