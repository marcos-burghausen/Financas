<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Launch;
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
        info($data);

        if ($data['launch_type'] === 'CARTAO_CREDITO') {
            $this->createCreditCardLancamento($data, $user);
        } else {
            info('Criando lançamento padrão (Receita/Despesa)');
            $this->createStandardLancamento($data, $user);
        }
    }

    public function efetivarLancamento(Launch $lancamento): void
    {
        // Se o lançamento já estiver efetivado, não faz nada.
        if ($lancamento->launch_status === 'EFETIVADA') {
            return;
        }

        // 1. Atualiza o status e a data de efetivação.
        $lancamento->launch_status = 'EFETIVADA';
        $lancamento->effective_date = Carbon::now();
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
        switch ($data['recurrence']) {
            case 'PARCELADO':
                $this->createLancamentoParceladoStandard($data, $user);
                break;
            case 'FIXA':
                $this->createLancamentoFixoStandard($data, $user);
                break;
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
        $conta = Account::findOrFail($data['account_id']);
        $faturasAfetadas = [];

        // Se for parcelado, entra no loop
        if ($data['recurrence'] === 'PARCELADO' && $data['qtd_installments'] > 1) {
            $numParcelas = (int) $data['qtd_installments'];
            $valorTotal = $data['value'];
            $tipoParcela = $data['installment_type'] ?? 'TOTAL';

            if ($tipoParcela === 'TOTAL') {
                $valorBaseParcela = intdiv($valorTotal, $numParcelas);
                $resto = $valorTotal % $numParcelas;
            } else { // 'PARCELA'
                $valorBaseParcela = $valorTotal;
                $resto = 0;
            }

            $groupId = Str::uuid();
            $dataLancamentoOriginal = Carbon::parse($data['due_date']);


            for ($i = 1; $i <= $numParcelas; $i++) {
                // 3. Lógica para determinar a competência de cada parcela
                $competenciaDate = (clone $dataLancamentoOriginal)->addMonthsNoOverflow($i - 1);

                $invoiceId = $this->invoiceService->getOrCreateInvoiceId($conta->id, $competenciaDate, $user->id);


                if (!isset($faturasAfetadas[$invoiceId])) {
                    $faturasAfetadas[$invoiceId] = CreditCardInvoice::find($invoiceId);
                }

                $valorDaParcela = $valorBaseParcela + (($i === 1) ? $resto : 0);

                Launch::create(array_merge($data, [
                    'user_id' => $user->id,
                    'installment_group_id' => $groupId,
                    'description' => $data['description'] . " ($i/$numParcelas)",
                    'value' => $valorDaParcela,
                    'qtd_installments' => $numParcelas,
                    'num_installment' => $i,
                    'invoice_id' => $invoiceId,
                    'launch_status' => 'EFETIVADA',
                ]));
            }
        } else {
            $faturaDateObject = Carbon::createFromLocaleFormat('!M/Y', 'pt_BR', $data['fatura']);
            $invoiceId = $this->invoiceService->getOrCreateInvoiceId($conta->id, $faturaDateObject, $user->id);
            $faturasAfetadas[$invoiceId] = CreditCardInvoice::find($invoiceId);

            Launch::create(array_merge($data, [
                'user_id' => $user->id,
                'recurrence' => 'NAO_RECORRENTE',
                'invoice_id' => $invoiceId,
                'launch_status' => 'EFETIVADA',
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
        $lancamento = Launch::create(array_merge($data, ['user_id' => $user->id]));
        $this->atualizarSaldos($lancamento);
    }

    /**
     * Cria lançamentos parcelados padrão (Receita/Despesa).
     */
    private function createLancamentoParceladoStandard(array $data, User $user): void
    {
        $qtdParcelas = (int) $data['qtd_installments'];
        $numParcela = (int) ($data['num_installment'] ?? 1);
        $valorTotal = $data['value'];
        $tipoParcela = $data['installment_type'] ?? 'TOTAL';

        if ($tipoParcela === 'TOTAL') {
            $valorBaseParcela = intdiv($valorTotal, $qtdParcelas);
            $resto = $valorTotal % $qtdParcelas;
        } else {
            $valorBaseParcela = $valorTotal;
            $resto = 0;
        }

        $groupId = Str::uuid();
        $dataVencimentoBase = new DateTime($data['due_date']);
        $diaOriginal = (int)$dataVencimentoBase->format('d');
        $parcelaInicial = $data['num_installment'] ?? 1;
        $statusInicial = $data['launch_status'];

        // for ($i = 1; $i <= $qtdParcelas; $i++) {
        for ($i = $parcelaInicial; $i <= $qtdParcelas; $i++) {
            $valorDaParcela = $valorBaseParcela + (($i === 1) ? $resto : 0);

            $offsetMeses = $i - $parcelaInicial;
            $dataBaseLoop = (new DateTime($dataVencimentoBase->format('Y-m-01')))->modify("+$offsetMeses month");
            $ultimoDiaDoMesCalculado = (int)$dataBaseLoop->format('t');
            $diaDaParcela = min($diaOriginal, $ultimoDiaDoMesCalculado);
            $dataVencimentoParcela = $dataBaseLoop->format("Y-m-{$diaDaParcela}");

            $statusDaParcelaFixa = ($statusInicial === 'EFETIVADA' && $i === $parcelaInicial) ? 'EFETIVADA' : 'PENDENTE';

            $lancamento = Launch::create(array_merge($data, [
                'user_id'              => $user->id,
                'installment_group_id' => $groupId,
                'description'          => $data['description'] . " ($i/$qtdParcelas)",
                'value'                => $valorDaParcela,
                'installment_type'     => $data['installment_type'],
                'recurrence'           => 'Parcelado',
                'qtd_installments'     => $qtdParcelas,
                'num_installment'      => $i,
                'due_date'             => $dataVencimentoParcela,
                'launch_status'        => $statusDaParcelaFixa,
                'category'             => $data['category'],
                'subcategory'          => $data['subcategory'],
                'launch_date'          => $data['launch_date'],
                'account_id'           => $data['account_id'],
                'periodicity'          => $data['periodicity'] ?? null,
            ]));

            if ($statusInicial === 'EFETIVADA') {
                // $this->atualizarSaldo($conta, $valorInputEmCentavos, $data['tipo']);
                $this->atualizarSaldos($lancamento);
            }
        }
    }

    /**
     * Cria lançamentos fixos padrão (Receita/Despesa).
     */
    private function createLancamentoFixoStandard(array $data, User $user): void
    {
        info('Criando lançamento fixo padrão', $data);
        $groupId = Str::uuid();
        $dataVencimentoBase = new DateTime($data['due_date']);
        $diaOriginal = (int)$dataVencimentoBase->format('d');
        $statusInicial = $data['launch_status'];

        for ($i = 1; $i <= 12; $i++) {
            $offsetMeses = $i - 1;
            $dataBaseLoop = (new DateTime($dataVencimentoBase->format('Y-m-01')))->modify("+$offsetMeses month");
            $ultimoDiaDoMesCalculado = (int)$dataBaseLoop->format('t');
            $diaDaParcela = min($diaOriginal, $ultimoDiaDoMesCalculado);
            $dataVencimentoParcela = $dataBaseLoop->format("Y-m-{$diaDaParcela}");

            $statusDaParcelaFixa = ($statusInicial === 'EFETIVADA' && $i === 1) ? 'EFETIVADA' : 'PENDENTE';

            $lancamento = Launch::create([
                'user_id'              => $user->id,
                'installment_group_id' => $groupId,
                'description'          => $data['description'],
                'value'                => $data['value'],
                'launch_type'          => $data['launch_type'],
                'recurrence'           => 'Fixa',
                'qtd_installments'     => $data['qtd_installments'],
                'num_installment'      => $i,
                'due_date'             => $dataVencimentoParcela,
                'launch_status'        => $statusDaParcelaFixa,
                'category'             => $data['category'],
                'subcategory'          => $data['subcategory'],
                'launch_date'          => $data['launch_date'],
                'account_id'           => $data['account_id'] ?? null,
                'installment_type'     => $data['installment_type'] ?? 'total',
                'periodicity'          => $data['periodicity'] ?? null,
            ]);
            info('Lançamento fixo criado: numero -> ', $lancamento->toArray());

            if ($statusInicial === 'EFETIVADA') {
                // $this->atualizarSaldo($conta, $valorInputEmCentavos, $data['tipo']);
                $this->atualizarSaldos($lancamento);
            }
        }
    }

    /**
     * Roteador para a lógica de atualização de saldos de contas.
     */
    private function atualizarSaldos(Launch $lancamento): void
    {
        if (in_array($lancamento->launch_type, ['RECEITA', 'DESPESA'])) {
            if ($lancamento->launch_status === 'EFETIVADA' && $lancamento->account_id) {
                $conta = Account::find($lancamento->account_id);
                if ($conta) {
                    if ($lancamento->launch_type === 'RECEITA') {
                        $conta->balance += $lancamento->value;
                    } else { // DESPESA
                        $conta->balance -= $lancamento->value;
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
