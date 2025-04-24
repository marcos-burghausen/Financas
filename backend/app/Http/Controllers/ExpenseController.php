<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use Illuminate\Http\Request;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\TotalByCategoryTrait;
use App\Mail\NotificationMail;
use App\Models\Conta;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ExpenseController extends Controller
{
    use ReleasesMonthTrait;

    public function saveExpense(Request $request)
    {
        $data = $request->validate(
            [
                'valor'     => 'required',
                'date'      => 'required|date',
                'descricao' => 'required|string',
                'categoria' => 'required|string',
                'conta'  => 'required|string',
                'status'    => 'string'
            ],
            [
                'valor.required'     => 'O campo valor é obrigatório',
                'date.required'      => 'O campo data é obrigatório',
                'descricao.unique'   => 'O campo descricao é obrigatório',
                'categoria.required' => 'O campo categoria é obrigatório',
                'conta.required'  => 'O campo conta senha é obrigatório',
            ]
        );

        $user = auth()->user();

        DB::beginTransaction();
        $expense = new Expense;
        $expense->user_id   = $user->id;
        $expense->valor     = str_replace([',', '.'], '', $data['valor']);
        $expense->date      = $data['date'];
        $expense->descricao = $data['descricao'];
        $expense->categoria = $data['categoria'];
        $expense->conta  = $data['conta'];
        $expense->status       = $data['status'] ?? 'Pendente';
        $expense->tipo         = $data['tipo'] ?? 'Não recorrente';
        $expense->num_parcelas = $data['numParcelas'] ?? 0;
        $expense->periodicidade = $data['periodicidade'] ?? null;
        $saved = $expense->save();

        if ($data['status'] === 'PAGA') {
            $conta = Conta::where('user_id', auth()->user()->id)
                ->where('name', $data['conta'])
                ->first();

            if ($conta) {
                $conta->saldo -= str_replace([',', '.'], '', $data['valor']);
                $conta->save();
            }
        }

        if (!$saved) {
            return response()->json(Errors::ERROR_REGISTERING_EXPENSE->response());
        }

        DB::commit();

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses', $request->mesReferencia);
        $walletsData = [
            'wallets' => $user->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Salvamento', 'Despesa', $expense->descricao));
        return response()->json([
            'success'      => 'despesa cadastrada com sucesso',
            'expensesData' => $expensesData,
            'walletsData'  => $walletsData,
        ], 200);
    }

    public function payExpense(Request $request)
    {
        DB::beginTransaction();

        $expense = Expense::find($request->id);
        $expense->status = 'PAGA';
        // $expense->valor = str_replace([',', '.'], '', $expense->valor);
        $saved = $expense->save();

        if (!$saved) {
            return Errors::ERROR_PAY_EXPENSE->response();
        }

        $conta = Conta::where("user_id", auth()->user()->id)
            ->where("name", $request->conta)
            ->first();

        if ($conta) {
            $conta->saldo += str_replace([',', '.'], '', $expense->valor);
            $conta->save();
        }

        DB::commit();

        $user = auth()->user();

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses', $request->mesReferencia);
        $wallets = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];

        Mail::to($user->email)->queue(new NotificationMail($user, 'Pagamento', 'Despesa', $expense->descricao));

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'expensesData' => $expensesData,
            'walletsData' => $wallets,
        ], 200);
    }

    public function editExpense(Request $request)
    {
        DB::beginTransaction();

        $add = false;
        $sub = false;
        $expense = Expense::find($request->id);
        if ($request->status === 'PAGA' && $expense->status === 'AGUARDANDO') {
            $add = true;
        }

        if ($request->status === 'AGUARDANDO' && $expense->status === 'PAGA' || $request->valor < $expense->valor) {
            $sub = true;
        }

        $expense->valor = str_replace([',', '.'], '', $request->valor);
        $expense->date = $request->date;
        $expense->descricao = $request->descricao;
        $expense->categoria = $request->categoria;
        $expense->conta = $request->conta;
        $expense->status = $request->status;
        $saved = $expense->save();

        if (!$saved) return response()->json(Errors::ERROR_UPDATING_EXPENSE->response());

        $conta = Conta::where("user_id", auth()->user()->id)
            ->where("name", $request->conta)
            ->first();
        info($conta);

        if ($conta && $add) {
            $conta->saldo += $expense->valor;
        }

        if ($conta && $sub) {
            $conta->saldo -= $expense->valor;
        }

        $conta->save();

        DB::commit();

        $user = auth()->user();
        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses', $request->mesReferencia);
        $wallets = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];


        Mail::to($user->email)->queue(new NotificationMail($user, 'Edição', 'Despesa', $expense->descricao));

        return response()->json([
            'success' => 'despesa editada com sucesso',
            'expensesData' => $expensesData,
            'walletsData' => $wallets,
        ], 200);
    }

    public function deleteExpense(Request $request)
    {
        DB::beginTransaction();
        $expense = Expense::find($request->id);

        $deleted = Expense::destroy($request->id);
        if (!$deleted) {
            return response()->json(Errors::ERROR_DELETING_EXPENSE->response());
        }

        $user = auth()->user();
        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses', $request->mesReferencia);
        $wallets = [
            'wallets' => auth()->user()->contas()->get(),
            'saldoInicial' => $this->obterSaldoInicial($user, $request->mesReferencia),
        ];


        Mail::to($user->email)->queue(new NotificationMail($user, 'Exclusão', 'Despesa', $expense->descricao));

        return response()->json([
            'success' => 'despesa excluida com sucesso',
            'expensesData' => $expensesData,
            'walletsData' => $walletsData,
        ], 200);
    }


    public function getExpense()
    {
        if ($expenses = auth()->user()->expenses()->get()) {
            return response()->json(['expenses' => $expenses]);
        }

        return response()->json(Errors::ERROR_FETCHING_EXPENSE->response());
    }
}
