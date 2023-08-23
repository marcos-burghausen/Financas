<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;

class ExpenseController extends Controller
{
    public function saveExpense(Request $request)
    {

        $data = $request->validate(
            [
                'valor'     => 'required',
                'date'      => 'required|date',
                'descricao' => 'required|string',
                'categoria' => 'required|string',
                'carteira'  => 'required|string',
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
        $saved = $expense->save();

        if (!$saved) {
            return response()->json('erro ao cadastrar despesa');
        }

        // Mail::to($user->email)->send(new DespesaRegistradaMail($despesa));

        return response()->json('despesa cadastrada com sucesso', 200);
    }

    public function deleteExpense(Request $request)
    {
        $delete = Expense::destroy($request->id);
        if ($delete) {
            return response()->json('Despesa excluida com sucesso');
        }
        return response()->json('Erro ao excluir despesa');
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
        if ($saved) {
            return response()->json('Despesa atualizado com sucesso');
        }
        return response()->json('Erro ao atualizar despesa');
    }

    public function getExpense()
    {
        return response()->json(auth()->user()->expenses()->get());
    }
}
