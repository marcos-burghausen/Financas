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
        $revenue->num_parcelas    = $data['numParcelas'] ?? 0;
        $revenue->periodicidade   = $data['periodicidade'] ?? null;
        $revenue->data_vencimento = $data['data'];
        $revenue->data_lancamento = $data['data'];
        $revenue->status          = $data['status'] ?? 'Pendente';
        $revenue->categoria       = $data['categoria'];
        $revenue->sub_categoria   = $data['sub_categoria'];
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

        $revenue->valor        = $data['valor'] * 100;
        $revenue->date         = $data['date'];
        $revenue->descricao    = $data['descricao'];
        $revenue->categoria    = $data['categoria'];
        $revenue->conta        = $data['conta'];
        $revenue->status       = $data['status'] ?? 'Pendente';
        $revenue->tipo         = $data['tipo'] ?? 'Não recorrente';
        $revenue->num_parcelas = $data['numParcelas'] ?? 0;
        $revenue->periodicidade = $data['periodicidade'] ?? null;
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
                'valor'         => 'required|min:0.01',
                'data'          => 'required|date',
                'descricao'     => 'required|string|max:255',
                'categoria'     => 'required|string|max:100',
                'conta'         => 'required|string|max:100',
                'status'        => 'nullable|string|in:Pendente,Efetivada',
                'mesReferencia' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
                'numParcelas'   => 'nullable|integer|min:0',
                'periodicidade' => 'nullable|string|in:Mensal,Diario,Semanal,Quinzenal,Trimestral,Anual',
                'tipo'          => 'nullable|string|in:Não recorrente,Parcelada,Fixa mensal',
            ],
            [
                'valor.required'     => 'O campo valor é obrigatório',
                'valor.numeric'      => 'O valor deve ser um número',
                'valor.min'          => 'O valor deve ser maior que zero',
                'date.required'      => 'O campo data é obrigatório',
                'descricao.required' => 'O campo descrição é obrigatório',
                'categoria.required' => 'O campo categoria é obrigatório',
                'conta.required'     => 'O campo conta é obrigatório',
                'mesReferencia.regex' => 'O campo mesReferencia deve estar no formato YYYY-MM (ex: 2025-04)',
                'periodicidade.in'   => 'O campo periodicidade deve ser um dos seguintes: Mensal, Diario, Semanal, Quinzenal, Trimestral, Anual',
                'tipo.in'           => 'O campo tipo deve ser um dos seguintes: Não recorrente, Parcelada, Fixa mensal',
            ]
        );
    }
}
