<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use Illuminate\Http\Request;
use App\Http\Traits\ReleasesMonthTrait;
use App\Models\Expense;

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
        $expense = new Expense;
        $expense->user_id   = auth()->user()->id;
        $expense->valor     = $data['valor'];
        $expense->date      = $data['date'];
        $expense->descricao = $data['descricao'];
        $expense->categoria = $data['categoria'];
        $expense->carteira  = $data['carteira'];
        $expense->status    = $data['status'];
        $saved = $expense->save();

        if (!$saved) {
            return response()->json(Errors::ERROR_REGISTERING_EXPENSE->response());
        }

        $expenses = auth()->user()->expenses()->get();
        $valueTotalExpensesMonth = $this->valueReleasesMonth($expenses, date('m'));
        $valuePending = $this->valuePending($expenses, date('m'), "AGUARDANDO");
        $valuePay = $this->valuePending($expenses, date('m'), "PAGA");
        $expensesMonth = $this->releasesMonth($expenses, date('m'));

        // Mail::to($user->email)->send(new DespesaRegistradaMail($despesa));
        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'valueTotalExpensesMonth' => $valueTotalExpensesMonth,
            'expenses' => $expenses,
            'valuePay' => $valuePay,
            'valuePending' => $valuePending,
            'expensesMonth' => $expensesMonth
        ], 200);
    }

    public function payExpense(Request $request)
    {
        $expense = Expense::find($request->id);
        $expense->status = 'PAGA';
        $saved = $expense->save();
        if (!$saved) {
            return Errors::ERROR_PAY_EXPENSE->response();
        }

        $expenses = auth()->user()->expenses()->get();
        $valueTotalExpensesMonth = $this->valueReleasesMonth($expenses, date('m'));
        $valuePending = $this->valuePending($expenses, date('m'), "AGUARDANDO");
        $valuePay = $this->valuePending($expenses, date('m'), "PAGA");
        $expensesMonth = $this->releasesMonth($expenses, date('m'));

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'valueTotalExpensesMonth' => $valueTotalExpensesMonth,
            'expenses' => $expenses,
            'valuePay' => $valuePay,
            'valuePending' => $valuePending,
            'expensesMonth' => $expensesMonth
        ], 200);
    }

    public function editExpense(Request $request)
    {
        $expense = Expense::find($request->id);
        $expense->valor = $request->valor;
        $expense->date = $request->date;
        $expense->descricao = $request->descricao;
        $expense->categoria = $request->categoria;
        $expense->carteira = $request->carteira;
        $expense->status = $request->status;
        $saved = $expense->save();

        if (!$saved) return response()->json(Errors::ERROR_UPDATING_EXPENSE->response());

        $expenses = auth()->user()->expenses()->get();
        $valueTotalExpensesMonth = $this->valueReleasesMonth($expenses, date('m'));
        $valuePending = $this->valuePending($expenses, date('m'), "AGUARDANDO");
        $valuePay = $this->valuePending($expenses, date('m'), "PAGA");
        $expensesMonth = $this->releasesMonth($expenses, date('m'));

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'valueTotalExpensesMonth' => $valueTotalExpensesMonth,
            'expenses' => $expenses,
            'valuePay' => $valuePay,
            'valuePending' => $valuePending,
            'expensesMonth' => $expensesMonth
        ], 200);
    }

    public function deleteExpense(Request $request)
    {
        $deleted = Expense::destroy($request->id);
        if (!$deleted) {
            return response()->json(Errors::ERROR_DELETING_EXPENSE->response());
        }

        $expenses = auth()->user()->expenses()->get();
        $valueTotalExpensesMonth = $this->valueReleasesMonth($expenses, date('m'));
        $valuePending = $this->valuePending($expenses, date('m'), "AGUARDANDO");
        $valuePay = $this->valuePending($expenses, date('m'), "PAGA");
        $expensesMonth = $this->releasesMonth($expenses, date('m'));

        return response()->json([
            'success' => 'despesa cadastrada com sucesso',
            'valueTotalExpensesMonth' => $valueTotalExpensesMonth,
            'expenses' => $expenses,
            'valuePay' => $valuePay,
            'valuePending' => $valuePending,
            'expensesMonth' => $expensesMonth
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
