<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function saveReleases(Request $request)
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
        $user = auth()->user();
        Expense::factory()->createOne([
            'user_id'   => $user->id,
            'valor'     => $data['valor'],
            'date'      => $data['date'],
            'descricao' => $data['descricao'],
            'categoria' => $data['categoria'],
            'carteira'  => $data['carteira'],
        ]);

        // Mail::to($user->email)->send(new DespesaRegistradaMail($despesa));

        return response()->json('despesa cadastrada com sucesso', 200);
    }
}
