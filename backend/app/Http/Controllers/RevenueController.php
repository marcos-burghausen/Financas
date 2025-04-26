<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use App\Http\Traits\ReleasesMonthTrait;
use App\Mail\NotificationMail;
use App\Models\Conta;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RevenueController extends Controller
{
    use ReleasesMonthTrait;

    public function saveRevenue(Request $request)
    {
        $data = $this->validateRevenue($request);

        $user = auth()->user();

        DB::beginTransaction();
        $revenue = new Revenue;
        $revenue->user_id         = $user->id;
        $revenue->descricao       = $data['descricao'];
        $revenue->valor           = str_replace([',', '.'], '', $data['valor']);
        $revenue->tipo            = $data['tipo'] ?? 'Não recorrente';
        $revenue->num_parcelas    = $data['numParcelas'] ?? null;
        $revenue->periodicidade   = $data['periodicidade'] ?? null;
        $revenue->data_vencimento = $data['dataVencimento'];
        $revenue->status          = $data['status'];
        $revenue->categoria       = $data['categoria'];
        $revenue->subcategoria    = $data['subcategoria'];
        $revenue->data_lancamento = $data['dataLancamento'];
        $revenue->data_efetivacao = $data['dataEfetivacao'] ?? null;
        $revenue->conta           = $data['conta'];
        $saved = $revenue->save();

        if ($data['status'] === 'Efetivada') {
            $conta = Conta::where('user_id', $user->id)
                ->where('name', $data['conta'])
                ->first();

            if ($conta) {
                $conta->saldo += $revenue->valor;
                $conta->save();
            }
        }

        if (!$saved) {
            DB::rollBack();
            return response()->json(Errors::ERROR_REGISTERING_REVENUE->response(), 422);
        }

        DB::commit();

        $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        $walletsData = [
            'wallets' => $user->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $revenue->descricao));

        return response()->json([
            'success' => 'Receita cadastrada com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
        ], 201);
    }

    public function editRevenue(Request $request, $id)
    {
        $data = $this->validateRevenue($request);

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


        $revenue->descricao       = $data['descricao'];
        $revenue->valor           = str_replace([',', '.'], '', $data['valor']);
        $revenue->tipo            = $data['tipo'] ?? 'Não recorrente';
        $revenue->num_parcelas    = $data['numParcelas'] ?? null;
        $revenue->periodicidade   = $data['periodicidade'] ?? null;
        $revenue->data_vencimento = $data['dataVencimento'];
        $revenue->status          = $data['status'];
        $revenue->categoria       = $data['categoria'];
        $revenue->subcategoria    = $data['subcategoria'];
        $revenue->data_lancamento = $data['dataLancamento'];
        $revenue->data_efetivacao = $data['dataEfetivacao'] ?? null;
        $revenue->conta           = $data['conta'];
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

        $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        $walletsData = [
            'wallets' => $user->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Edição', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita editada com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
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

        $revenue->status = 'Efetivada';
        $revenue->data_efetivacao = date('Y-m-d');
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

        $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        $walletsData = [
            'wallets' => $user->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Recebimento', 'Receita', $revenue->descricao));

        return response()->json([
            'success' => 'Receita recebida com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
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

        $revenuesData = $this->classifiesReleases($user->revenues()->get(), 'Revenues', $data['mesReferencia'] ?? date('Y-m'));
        $walletsData = [
            'wallets' => $user->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $data['mesReferencia'] ?? date('Y-m')),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Exclusão', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita excluída com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
        ], 200);
    }

    public function getRevenue()
    {
        $revenues = auth()->user()->revenues()->get();
        return response()->json(['revenues' => $revenues], 200);
    }

    protected function validateRevenue(Request $request)
    {
        return $request->validate(
            [
                'id'              => 'nullable | integer',
                'descricao'       => 'required | string | max:50',
                'valor'           => 'required | min:0.01',
                'tipo'            => 'string | in:Não recorrente,Parcelada,Fixa mensal',
                'numParcelas'    => 'nullable | integer | min:2',
                'periodicidade'   => 'nullable | string | in:Mensal,Diario,Semanal,Quinzenal,Trimestral,Anual',
                'dataVencimento' => 'required | date',
                'status'          => 'required | string | in:Pendente,Efetivada',
                'categoria'       => 'required | string | max:30',
                'subcategoria'    => 'required | string | max:30',
                'dataLancamento' => 'required | date',
                'dataEfetivacao' => 'nullable | date',
                'conta'           => 'required | string | max:30',
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
}
