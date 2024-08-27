<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Traits\GroupReleasesTrait;
use Illuminate\Http\Request;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\TotalByCategoryTrait;
use App\Mail\NotificationMail;
use App\Models\Expense;
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
                'carteira'  => 'required|string',
                'status'    => 'string'
            ],
            [
                'valor.required'     => 'O campo valor é obrigatório',
                'date.required'      => 'O campo data é obrigatório',
                'descricao.unique'   => 'O campo descricao é obrigatório',
                'categoria.required' => 'O campo categoria é obrigatório',
                'carteira.required'  => 'O campo carteira senha é obrigatório',
            ]
        );

        $user = auth()->user();

        $expense = new Expense;
        $expense->user_id   = $user->id;
        $expense->valor     = str_replace([',', '.'], '', $data['valor']);
        $expense->date      = $data['date'];
        $expense->descricao = $data['descricao'];
        $expense->categoria = $data['categoria'];
        $expense->carteira  = $data['carteira'];
        $expense->status    = $data['status'];
        $saved = $expense->save();

        if (!$saved) {
            return response()->json(Errors::ERROR_REGISTERING_EXPENSE->response());
        }

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses');

        Mail::to($user->email)->send(new NotificationMail($expense));
        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'expensesData' => $expensesData,
        ], 200);
    }

    public function payExpense(Request $request)
    {
        $expense = Expense::find($request->id);
        $expense->status = 'PAGA';
        $expense->valor = str_replace([',', '.'], '', $expense->valor);
        $saved = $expense->save();

        if (!$saved) {
            return Errors::ERROR_PAY_EXPENSE->response();
        }

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses');

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'expensesData' => $expensesData,
        ], 200);
    }

    public function editExpense(Request $request)
    {
        $expense = Expense::find($request->id);
        $expense->valor = str_replace([',', '.'], '', $request->valor);
        $expense->date = $request->date;
        $expense->descricao = $request->descricao;
        $expense->categoria = $request->categoria;
        $expense->carteira = $request->carteira;
        $expense->status = $request->status;
        $saved = $expense->save();

        if (!$saved) return response()->json(Errors::ERROR_UPDATING_EXPENSE->response());

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses');

        return response()->json([
            'success' => 'despesa auterada com sucesso',
            'expensesData' => $expensesData,
        ], 200);
    }

    public function deleteExpense(Request $request)
    {
        $deleted = Expense::destroy($request->id);
        if (!$deleted) {
            return response()->json(Errors::ERROR_DELETING_EXPENSE->response());
        }

        $expensesData = $this->classifiesReleases(auth()->user()->expenses()->get(), 'Expenses');

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'expensesData' => $expensesData,
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
