<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\UserDataTrait;
use App\Mail\NotificationMail;
use App\Models\Conta;
use App\Models\Lancamento;
use App\Models\Parcela;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DateTime;

class RevenueController extends Controller
{
    use ReleasesMonthTrait, UserDataTrait;

    public function saveRevenue(Request $request)
    {
        try {
            $data = $this->validateData($request);
            
            /** @var \App\Models\User $user */
            $user = auth()->user();

            DB::beginTransaction();

            $recorrencia = $data['recorrencia'] ?? 'Não recorrente';
            $valorInputEmCentavos = (int) str_replace([',', '.'], '', $data['valor']);
            $statusInicial = $data['status'];

            $conta = Conta::where('user_id', $user->id)->where('name', $data['conta'])->first();

            if (!$conta) {
               DB::rollBack();
                return response()->json(['error' => 'Conta não encontrada'], 404);
            }

            // CASO 1: Lançamento Parcelado
            if ($recorrencia === 'Parcelado' && isset($data['numParcelas']) && $data['numParcelas'] > 1) {

                $numParcelas = (int) $data['numParcelas'];
                $groupId = Str::uuid();
                $parcelaInicial = $data['parcelaAtual'] ?? 1;

                if (isset($data['tipoParcela']) && $data['tipoParcela'] === 'total') {
                    $valorBaseParcela = floor($valorInputEmCentavos / $numParcelas);
                    $resto = $valorInputEmCentavos % $numParcelas;
                } else {
                    $valorBaseParcela = $valorInputEmCentavos;
                    $resto = 0;
                }

                $dataVencimentoBase = new DateTime($data['dataVencimento']);
                $diaOriginal = (int)$dataVencimentoBase->format('d');

                for ($i = $parcelaInicial; $i <= $numParcelas; $i++) {
                    $valorDaParcelaAtual = $valorBaseParcela;

                    // Adiciona o resto apenas à primeira parcela (se o parcelamento começar do 1)
                    if ($data['tipoParcela'] === 'total' && $i === 1 && $parcelaInicial === 1) {
                        $valorDaParcelaAtual += $resto;
                    }

                    // Define o status desta parcela específica.
                    $statusDaParcela = 'Pendente';
                    if ($statusInicial === 'Efetivada' && $i === $parcelaInicial) {
                        $statusDaParcela = 'Efetivada';
                    }

                    $offsetMeses = $i - $parcelaInicial;
                    $dataBaseLoop = (new DateTime($dataVencimentoBase->format('Y-m-01')))->modify("+$offsetMeses month");
                    $ultimoDiaDoMesCalculado = (int)$dataBaseLoop->format('t');
                    $diaDaParcela = min($diaOriginal, $ultimoDiaDoMesCalculado);
                    $dataVencimentoParcela = $dataBaseLoop->format("Y-m-{$diaDaParcela}");

                    Lancamento::create([
                        'user_id'              => $user->id,
                        'installment_group_id' => $groupId,
                        'descricao'            => $data['descricao'] . " (" . $i . "/" . $numParcelas . ")",
                        'valor'                => $valorDaParcelaAtual,
                        'recorrencia'          => 'Parcelado',
                        'numParcelas'          => $numParcelas,
                        'parcelaAtual'         => $i,
                        'dataVencimento'       => $dataVencimentoParcela,
                        'status'               => $statusDaParcela,
                        'categoria'            => $data['categoria'],
                        'subcategoria'         => $data['subcategoria'],
                        'dataLancamento'       => $data['dataLancamento'],
                        'conta'                => $data['conta'],
                        'tipoParcela'          => $data['tipoParcela'] ?? 'total',
                        'periodicidade'        => $data['periodicidade'] ?? null,
                        'dataEfetivacao'       => $statusDaParcela === 'Efetivada' ? date('Y-m-d') : null,
                    ]);
                }

                // **LÓGICA DE SALDO PARA PARCELADO**
                if ($statusInicial === 'Efetivada') {
                    $primeiraParcela = $valorBaseParcela + $resto;
                    $this->atualizarSaldo($conta, $primeiraParcela, $data['tipo']);
                }

            // CASO 2: Lançamento Fixo
            } elseif ($recorrencia === 'Fixa') {

                $groupId = Str::uuid();
                $dataVencimentoBase = new DateTime($data['dataVencimento']);
                $diaOriginal = (int)$dataVencimentoBase->format('d');

                for ($i = 1; $i <= 12; $i++) {
                    $offsetMeses = $i - 1;
                    $dataBaseLoop = (new DateTime($dataVencimentoBase->format('Y-m-01')))->modify("+$offsetMeses month");
                    $ultimoDiaDoMesCalculado = (int)$dataBaseLoop->format('t');
                    $diaDaParcela = min($diaOriginal, $ultimoDiaDoMesCalculado);
                    $dataVencimentoParcela = $dataBaseLoop->format("Y-m-{$diaDaParcela}");

                    $statusDaParcelaFixa = ($statusInicial === 'Efetivada' && $i === 1) ? 'Efetivada' : 'Pendente';
                    
                    Lancamento::create([
                        'user_id'              => $user->id,
                        'installment_group_id' => $groupId,
                        'descricao'            => $data['descricao'],
                        'valor'                => $valorInputEmCentavos,
                        'recorrencia'          => 'Fixa',
                        'numParcelas'          => $data['numParcelas'],
                        'parcelaAtual'         => $i,
                        'dataVencimento'       => $dataVencimentoParcela,
                        'status'               => $statusDaParcelaFixa,
                        'categoria'            => $data['categoria'],
                        'subcategoria'         => $data['subcategoria'],
                        'dataLancamento'       => $data['dataLancamento'],
                        'conta'                => $data['conta'],
                        'tipoParcela'          => $data['tipoParcela'] ?? 'total',
                        'periodicidade'        => $data['periodicidade'] ?? null,
                        'dataEfetivacao'       => $statusDaParcelaFixa === 'Efetivada' ? date('Y-m-d') : null,
                    ]);
                }

                // **LÓGICA DE SALDO PARA FIXA MENSAL**
                if ($statusInicial === 'Efetivada') {
                    $this->atualizarSaldo($conta, $valorInputEmCentavos, $data['tipo']);
                }

            // CASO 3: Lançamento Único (Não recorrente)
            } else {

                $Lancamento = new Lancamento;
                $Lancamento->fill($data); // Preenche o modelo com os dados validados
                $Lancamento->user_id = $user->id;
                $Lancamento->valor = $valorInputEmCentavos;
                $saved = $Lancamento->save();

                // **LÓGICA DE SALDO PARA NÃO RECORRENTE**
                if ($saved && $statusInicial === 'Efetivada') {
                    $this->atualizarSaldo($conta, $valorInputEmCentavos, $data['tipo']);
                }
            }

            DB::commit();

            Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $data['descricao']));
            $data = $this->getUserData($user);

            return response()->json([
                'success' => 'Receita cadastrada com sucesso',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Erro ao salvar receita: ' . $e->getMessage());
            return response()->json(Errors::ERROR_REGISTERING_LANCAMENTO->response(), 500);
        }
    }

    public function editRevenue(Request $request, $id)
    {
        $data = $this->validateData($request);

        $user = auth()->user();

        DB::beginTransaction();

        $revenue = Revenue::where('id', $id)->where('user_id', $user->id)->first();
        if (!$revenue) {
            DB::rollBack();
            return response()->json(['error' => 'Receita não encontrada'], 404);
        }

        $oldStatus = $revenue->status;
        $oldValor = $revenue->valor;
        $oldConta = $revenue->conta;


        $revenue->descricao      = $data['descricao'];
        $revenue->valor          = str_replace([',', '.'], '', $data['valor']);
        $revenue->recorrencia    = $data['recorrencia'] ?? 'Não recorrente';
        $revenue->numParcelas    = $data['numParcelas'] ?? null;
        $revenue->periodicidade  = $data['periodicidade'] ?? null;
        $revenue->dataVencimento = $data['dataVencimento'];
        $revenue->status         = $data['status'];
        $revenue->categoria      = $data['categoria'];
        $revenue->subcategoria   = $data['subcategoria'];
        $revenue->dataLancamento = $data['dataLancamento'];
        $revenue->dataEfetivacao = $data['dataEfetivacao'] ?? null;
        $revenue->conta          = $data['conta'];
        $saved = $revenue->save();

        if (!$saved) {
            DB::rollBack();
            return response()->json(Errors::ERROR_UPDATING_REVENUE->response(), 422);
        }

        $conta = Conta::where('user_id', $user->id)
            ->where('name', $data['conta'])
            ->first();

        $oldContaModel = $oldConta === $data['conta'] ? $conta : Conta::where('user_id', $user->id)
            ->where('name', $oldConta)
            ->first();

        if ($oldStatus === 'Efetivada' && $data['status'] !== 'Efetivada') {
            if ($oldContaModel) {
                $oldContaModel->saldo -= $oldValor;
                $oldContaModel->save();
            }
        } elseif ($oldStatus !== 'Efetivada' && $data['status'] === 'Efetivada') {
            if ($conta) {
                $conta->saldo += $revenue->valor;
                $conta->save();
            }
        } elseif ($oldStatus === 'Efetivada' && $data['status'] === 'Efetivada') {
            if ($oldContaModel && $oldConta !== $data['conta']) {
                $oldContaModel->saldo -= $oldValor;
                $oldContaModel->save();
            }
            if ($conta) {
                $conta->saldo += $revenue->valor - ($oldConta === $data['conta'] ? $oldValor : 0);
                $conta->save();
            }
        }

        DB::commit();

        // $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        // $walletsData = [
        //     'wallets' => $user->contas()->get(),
        //     'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        // ];
        $data = $this->getUserData($user);

        Mail::to($user->email)->queue(new NotificationMail($user, 'Edição', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita editada com sucesso',
            // 'revenuesData' => $revenuesData,
            // 'walletsData' => $walletsData,
            'data' => $data
        ], 200);
    }

    public function receivedRevenue(Request $request, $id)
    {
        $data = $request->validate([
            'conta'         => 'required|string|max:100',
            'mesReferencia' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        $revenue = Revenue::where('id', $id)->where('user_id', $user->id)->first();
        if (!$revenue) {
            DB::rollBack();
            return response()->json(['error' => 'Receita não encontrada'], 404);
        }

        if ($revenue->status === 'Efetivada') {
            DB::rollBack();
            return response()->json(['error' => 'Receita já está efetivada'], 422);
        }

        $revenue->status = 'Efetivada';
        $revenue->dataEfetivacao = date('Y-m-d');
        $saved = $revenue->save();

        if (!$saved) {
            DB::rollBack();
            return response()->json(Errors::ERROR_PAY_REVENUE->response(), 422);
        }

        $conta = Conta::where('user_id', $user->id)
            ->where('name', $data['conta'])
            ->first();

        if ($conta) {
            $conta->saldo += $revenue->valor;
            $conta->save();
        }

        DB::commit();

        // $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        // $walletsData = [
        //     'wallets' => $user->contas()->get(),
        //     'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        // ];
        $data = $this->getUserData($user);

        Mail::to($user->email)->queue(new NotificationMail($user, 'Recebimento', 'Receita', $revenue->descricao));

        return response()->json([
            'success' => 'Receita recebida com sucesso',
            // 'revenuesData' => $revenuesData,
            // 'walletsData' => $walletsData,
            'data' => $data
        ], 200);
    }

    public function deleteRevenue(Request $request, $id)
    {
        $data = $request->validate([
            'mesReferencia' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        $revenue = Revenue::where('id', $id)->where('user_id', $user->id)->first();
        if (!$revenue) {
            DB::rollBack();
            return response()->json(['error' => 'Receita não encontrada'], 404);
        }

        if ($revenue->status === 'Efetivada') {
            $conta = Conta::where('user_id', $user->id)
                ->where('name', $revenue->conta)
                ->first();
            if ($conta) {
                $conta->saldo -= $revenue->valor;
                $conta->save();
            }
        }

        $deleted = $revenue->delete();
        if (!$deleted) {
            DB::rollBack();
            return response()->json(Errors::ERROR_DELETING_REVENUE->response(), 422);
        }

        DB::commit();

        // $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        // $walletsData = [
        //     'wallets' => $user->contas()->get(),
        //     'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        // ];
        $data = $this->getUserData($user);

        Mail::to($user->email)->queue(new NotificationMail($user, 'Exclusão', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita excluída com sucesso',
            // 'revenuesData' => $revenuesData,
            // 'walletsData' => $walletsData,
            'data' => $data
        ], 200);
    }

    public function getRevenue()
    {
        $revenues = auth()->user()->revenues()->get();
        return response()->json(['revenues' => $revenues], 200);
    }

    protected function validateData(Request $request)
    {
        return $request->validate(
            [
                'id'             => 'nullable | integer',
                'descricao'      => 'required | string | max:50',
                'valor'          => 'required | min:0.01',
                'recorrencia'    => 'string | in:Não recorrente,Parcelado,Fixa mensal',
                'numParcelas'    => 'nullable | integer | min:2',
                'parcelaAtual'   => 'nullable|integer|min:1',
                'tipoParcela'    => 'nullable|string|in:total,parcela',
                'periodicidade'  => 'nullable | string | in:Mensal,Diario,Semanal,Quinzenal,Trimestral,Anual',
                'dataVencimento' => 'required | date',
                'status'         => 'required | string | in:Pendente,Efetivada',
                'categoria'      => 'required | string | max:30',
                'subcategoria'   => 'required | string | max:30',
                'dataLancamento' => 'required | date',
                'dataEfetivacao' => 'nullable | date',
                'conta'          => 'required | string | max:30',
                'mesReferencia'  => 'required | string | regex:/^\d{4}-\d{2}$/',
            ],
            [
                'required'             => 'O campo :attribute é obrigatório',
                'integer'              => 'O campo :attribute deve ser um número',
                'string'               => 'O campo :attribute deve conter apenas letras',
                'max'                  => 'O campo :attribute deve conter no máximo :max caracteres',
                'min'                  => 'O campo :attribute deve ser maior que :min',
                'in'                   => 'O campo :attribute não corresponde ao valor esperado',
                'date'                 => 'O campo :attribute nâo é uma data valida',
                'mesReferencia.regex' => 'O campo mesReferencia deve estar no formato YYYY-MM (ex: 2025-04)',
            ]
        );
    }

    protected function criarParcelas($data, $lancamentoId)
    {
        $valorTotalNumerico = (float) str_replace(['.', ','], ['', '.'], $data['valor']);

        $valorParcela = $valorTotalNumerico / $data['numParcelas'];
        $dataVencimento = $data['dataVencimento'];

        for ($i = $data['parcelaAtual']; $i <= $data['numParcelas']; $i++) {
            Parcela::create([
                'revenue_id'     => $lancamentoId,
                'parcela'        => $i,
                "totalParcelas"  => $data['numParcelas'],
                'valor'          => str_replace([',', '.'], '', $valorParcela),
                'dataVencimento' => date('Y-m-d', strtotime($dataVencimento . " + " . ($i - 1) . " month")),
                'dataLancamento' => $data['dataLancamento'],
                'dataEfetivacao' => $data['dataEfetivacao'] ?? null,
                'status'         => 'Pendente',
            ]);
        }
        return true;
    }
}
