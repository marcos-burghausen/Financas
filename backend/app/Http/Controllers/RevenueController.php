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
        $data = $request->validate(
            [
                'valor'         => 'required',
                'date'          => 'required|date',
                'descricao'     => 'required|string',
                'categoria'     => 'required|string',
                'conta'         => 'required|string',
                'status'        => 'string',
                'mesReferencia' => 'string',
                'numParcelas'   => 'nullable|int',
                'periodicidade' => 'nullable|string',
                'status'        => 'nullable|string',
                'tipo'          => 'string'
            ],
            [
                'valor.required'     => 'O campo valor é obrigatório',
                'date.required'      => 'O campo data é obrigatório',
                'descricao.unique'   => 'O campo descricao é obrigatório',
                'categoria.required' => 'O campo categoria é obrigatório',
                'conta.required'  => 'O campo conta é obrigatório',
            ]
        );

        $user = auth()->user();

        DB::beginTransaction();
        $revenue = new Revenue;
        $revenue->user_id   = $user->id;
        $revenue->valor     = str_replace([',', '.'], '', $data['valor']);
        $revenue->date      = $data['date'];
        $revenue->descricao = $data['descricao'];
        $revenue->categoria = $data['categoria'];
        $revenue->conta  = $data['conta'];
        $revenue->status    = $data['status'];
        $saved = $revenue->save();

        if ($data['status'] === 'Efetivada') {
            $conta = Conta::where('user_id', auth()->user()->id)
                ->where('name', $data['conta'])
                ->first();

            if ($conta) {
                $conta->saldo += str_replace([',', '.'], '', $data['valor']);
                $conta->save();
            }
        }

        if (!$saved) {
            return response()->json(Errors::ERROR_REGISTERING_REVENUE->response());
        }

        DB::commit();

        $revenuesData = $this->classifiesReleases(auth()->user()->revenues()->get(), 'Revenues', $request->mesReferencia);
        $walletsData = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $revenue->descricao));

        return response()->json([
            'success' => 'Receita cadastrada com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
        ], 200);
    }

    public function receivedRevenue(Request $request)
    {

        DB::beginTransaction();
        $revenue = Revenue::find($request->id);
        $revenue->status = 'Efetivada';
        // $revenue->valor = str_replace([',','.'], '', $revenue->valor);
        $saved = $revenue->save();

        if (!$saved) {
            return Errors::ERROR_PAY_REVENUE->response();
        }

        $conta = Conta::where("user_id", auth()->user()->id)
            ->where("name", $request->conta)
            ->first();

        if ($conta) {
            $conta->saldo += str_replace([',', '.'], '', $revenue->valor);
            $conta->save();
        }

        DB::commit();

        $user = auth()->user();
        $revenuesData = $this->classifiesReleases(auth()->user()->revenues()->get(), 'Revenues', $request->mesReferencia);
        $wallets = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $revenue->descricao));

        return response()->json([
            'success' => 'Receita recebida com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $wallets,
        ], 200);
    }

    public function editRevenue(Request $request)
    {
        DB::beginTransaction();

        $add = false;
        $sub = false;
        $revenue = Revenue::find($request->id);
        if ($request->status === 'Efetivada' && $revenue->status === 'Pendente') {
            $add = true;
        }

        if ($request->status === 'Pendente' && $revenue->status === 'Efetivada' || $request->valor < $revenue->valor) {
            $sub = true;
        }

        $revenue->valor = str_replace([',', '.'], '', $request->valor);
        $revenue->date = $request->date;
        $revenue->descricao = $request->descricao;
        $revenue->categoria = $request->categoria;
        $revenue->conta = $request->conta;
        $revenue->status = $request->status;
        $saved = $revenue->save();

        if (!$saved) return response()->json(Errors::ERROR_UPDATING_REVENUE->response());

        $conta = Conta::where("user_id", auth()->user()->id)
            ->where("name", $request->conta)
            ->first();
        info($conta);

        if ($conta && $add) {
            $conta->saldo += $revenue->valor;
        }

        if ($conta && $sub) {
            $conta->saldo -= $revenue->valor;
        }

        $conta->save();

        DB::commit();

        $user = auth()->user();
        $revenuesData = $this->classifiesReleases(auth()->user()->revenues()->get(), 'Revenues', $request->mesReferencia);
        $wallets = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];


        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita editada com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $wallets,
        ], 200);
    }

    public function deleteRevenue(Request $request)
    {
        DB::beginTransaction();
        $revenue = Revenue::find($request->id);

        $deleted = Revenue::destroy($request->id);
        if (!$deleted) {
            return response()->json(Errors::ERROR_DELETING_REVENUE->response());
        }
        DB::commit();

        $user = auth()->user();
        $revenuesData = $this->classifiesReleases(auth()->user()->revenues()->get(), 'Revenues', $request->mesReferencia);
        $walletsData = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];


        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Receita', $revenue->descricao));

        return response()->json([
            'msg' => 'Receita alterada com sucesso',
            'revenuesData' => $revenuesData,
            'walletsData' => $walletsData,
        ], 200);
    }


    public function getExpense()
    {
        if ($revenues = auth()->user()->revenues()->get()) {
            return response()->json(['expenses' => $revenues]);
        }

        return response()->json(Errors::ERROR_FETCHING_EXPENSE->response());
    }
}
