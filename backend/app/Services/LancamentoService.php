<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\User;
use App\Models\CreditCardInvoice;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;

class LancamentoService
{
    protected $invoiceService;

    public function __construct(CreditCardInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Ponto de entrada principal para criar um novo lançamento.
     */
    public function createLancamento(array $data, User $user): void
    {

        if ($data['tipo_lancamento'] === 'CARTAO_CREDITO') {
            $this->createCreditCardLancamento($data, $user);
        } else {
            $this->createStandardLancamento($data, $user);
        }
    }

    public function efetivarLancamento(Lancamento $lancamento): void
    {
        // Se o lançamento já estiver efetivado, não faz nada.
        if ($lancamento->status_lancamento === 'EFETIVADA') {
            return;
        }

        // 1. Atualiza o status e a data de efetivação.
        $lancamento->status_lancamento = 'EFETIVADA';
        $lancamento->data_efetivacao = Carbon::now();
        $lancamento->save();

        // 2. Chama o método que já criamos para atualizar o saldo da conta.
        // Ele já contém a lógica para verificar o tipo (RECEITA/DESPESA) e o status.
        $this->atualizarSaldos($lancamento);
    }

    /**
     * Lida com a criação de lançamentos padrão (Receita/Despesa).
     */
    private function createStandardLancamento(array $data, User $user): void
    {
        switch ($data['recorrencia']) {
            case 'PARCELADO':
                $this->createLancamentoParcelado($data, $user);
                break;
            // Adicione a lógica para 'FIXA' se for diferente de 'PARCELADO'
            case 'FIXA':
            case 'NAO_RECORRENTE':
            default:
                $this->createLancamentoUnico($data, $user);
                break;
        }
    }

    /**
     * Lida com a criação de lançamentos de Cartão de Crédito (à vista ou parcelado).
     */
    private function createCreditCardLancamento(array $data, User $user): void
    {
        $conta = Conta::findOrFail($data['conta_id']);
        $faturasAfetadas = [];

        // Se for parcelado, entra no loop
        if ($data['recorrencia'] === 'PARCELADO' && $data['qtd_parcelas'] > 1) {
            $numParcelas = (int) $data['qtd_parcelas'];
            $valorTotal = $data['valor'];
            $tipoParcela = $data['tipo_parcela'] ?? 'TOTAL';

            if ($tipoParcela === 'TOTAL') {
                $valorBaseParcela = intdiv($valorTotal, $numParcelas);
                $resto = $valorTotal % $numParcelas;
            } else { // 'PARCELA'
                $valorBaseParcela = $valorTotal;
                $resto = 0;
            }

            $groupId = Str::uuid();
            $dataLancamentoOriginal = Carbon::parse($data['data_lancamento']);


            for ($i = 1; $i <= $numParcelas; $i++) {
                // 3. Lógica para determinar a competência de cada parcela
                $competenciaDate = (clone $dataLancamentoOriginal)->addMonthsNoOverflow($i - 1);

                $invoiceId = $this->invoiceService->getOrCreateInvoiceId($conta->id, $competenciaDate, $user->id);


                if (!isset($faturasAfetadas[$invoiceId])) {
                    $faturasAfetadas[$invoiceId] = CreditCardInvoice::find($invoiceId);
                }

                $valorDaParcela = $valorBaseParcela + (($i === 1) ? $resto : 0);

                Lancamento::create(array_merge($data, [
                    'user_id' => $user->id,
                    'installment_group_id' => $groupId,
                    'descricao' => $data['descricao'] . " ($i/$numParcelas)",
                    'valor' => $valorDaParcela,
                    'qtd_parcelas' => $numParcelas,
                    'num_parcela' => $i,
                    'invoice_id' => $invoiceId,
                    'status_lancamento' => 'EFETIVADA',
                ]));
            }
        } else {
            $faturaDateObject = Carbon::createFromLocaleFormat('!M/Y', 'pt_BR', $data['fatura']);
            $invoiceId = $this->invoiceService->getOrCreateInvoiceId($conta->id, $faturaDateObject, $user->id);
            $faturasAfetadas[$invoiceId] = CreditCardInvoice::find($invoiceId);

            Lancamento::create(array_merge($data, [
                'user_id' => $user->id,
                'recorrencia' => 'NAO_RECORRENTE',
                'invoice_id' => $invoiceId,
                'status_lancamento' => 'EFETIVADA',
            ]));
        }

        /// Após criar os lançamentos, recalcula o total de todas as faturas afetadas
        foreach ($faturasAfetadas as $fatura) {
            if ($fatura) {
                $fatura->recalculateTotals();
            }
        }
    }

    /**
     * Cria um lançamento único padrão.
     */
    private function createLancamentoUnico(array $data, User $user): void
    {
        $lancamento = Lancamento::create(array_merge($data, ['user_id' => $user->id]));
        $this->atualizarSaldos($lancamento);
    }

    /**
     * Cria lançamentos parcelados padrão (Receita/Despesa).
     */
    private function createLancamentoParceladoStandard(array $data, User $user): void
    {
        $qtdParcelas = (int) $data['qtd_parcelas'];
        $valorTotal = $data['valor'];
        $tipoParcela = $data['tipo_parcela'] ?? 'TOTAL';

        if ($tipoParcela === 'TOTAL') {
            $valorBaseParcela = intdiv($valorTotal, $qtdParcelas);
            $resto = $valorTotal % $qtdParcelas;
        } else {
            $valorBaseParcela = $valorTotal;
            $resto = 0;
        }

        $groupId = Str::uuid();
        $dataVencimentoBase = new DateTime($data['data_vencimento']);
        $diaOriginal = (int)$dataVencimentoBase->format('d');

        for ($i = 1; $i <= $qtdParcelas; $i++) {
            $valorDaParcela = $valorBaseParcela + (($i === 1) ? $resto : 0);

            $offsetMeses = $i - 1;
            $dataBaseLoop = (new DateTime($dataVencimentoBase->format('Y-m-01')))->modify("+$offsetMeses month");
            $ultimoDiaDoMesCalculado = (int)$dataBaseLoop->format('t');
            $diaDaParcela = min($diaOriginal, $ultimoDiaDoMesCalculado);
            $dataVencimentoParcela = $dataBaseLoop->format("Y-m-{$diaDaParcela}");

            $lancamento = Lancamento::create(array_merge($data, [
                'user_id'              => $user->id,
                'installment_group_id' => $groupId,
                'descricao'            => $data['descricao'] . " ($i/$qtdParcelas)",
                'valor'                => $valorDaParcela,
                'qtd_parcelas'         => $qtdParcelas,
                'num_parcela'         => $i,
                'data_vencimento'      => $dataVencimentoParcela,
                // Apenas a primeira parcela herda o status; as outras são pendentes
                'status_lancamento'    => ($i === 1 && $data['status_lancamento'] === 'EFETIVADA') ? 'EFETIVADA' : 'PENDENTE',
            ]));

            // A atualização de saldo é chamada para cada parcela,
            // mas só terá efeito se a parcela for 'EFETIVADA'
            $this->atualizarSaldos($lancamento);
        }
    }

    /**
     * Roteador para a lógica de atualização de saldos de contas.
     */
    private function atualizarSaldos(Lancamento $lancamento): void
    {
        if (in_array($lancamento->tipo_lancamento, ['RECEITA', 'DESPESA'])) {
            if ($lancamento->status_lancamento === 'EFETIVADA' && $lancamento->conta_id) {
                $conta = Conta::find($lancamento->conta_id);
                if ($conta) {
                    if ($lancamento->tipo_lancamento === 'RECEITA') {
                        $conta->saldo += $lancamento->valor;
                    } else { // DESPESA
                        $conta->saldo -= $lancamento->valor;
                    }
                    $conta->save();
                }
            }
        }
    }
    // private function atualizarSaldos(Lancamento $lancamento): void
    // {
    //     // Lógica para atualizar o saldo da conta bancária
    //     if ($lancamento->tipo_lancamento !== 'CARTAO_CREDITO' && $lancamento->conta_id) {
    //         $conta = Conta::find($lancamento->conta_id);
    //         if ($conta) {
    //             if ($lancamento->tipo_lancamento === 'RECEITA') {
    //                 $conta->saldo += $lancamento->valor;
    //             } else { // DESPESA
    //                 $conta->saldo -= $lancamento->valor;
    //             }
    //             $conta->save();
    //         }
    //     }

    //     // Lógica para recalcular o total da fatura do cartão
    //     if ($lancamento->invoice_id) {
    //         $fatura = $lancamento->fatura; // Usando o relacionamento definido no Model
    //         if ($fatura) {
    //             $fatura->recalculateTotals();
    //         }
    //     }
    // }
}
