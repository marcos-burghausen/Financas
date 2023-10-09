<?php

namespace App\Http\Controllers;

use App\Enums\Errors;
use App\Http\Traits\ReleasesMonthTrait;
use App\Models\Revenue;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    use ReleasesMonthTrait;

    public function saveRevenue(Request $request)
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
                'carteira.required'  => 'O campo carteira é obrigatório',
            ]
        );
        $revenue = new Revenue;
        $revenue->user_id   = auth()->user()->id;
        $revenue->valor     = $data['valor'];
        $revenue->date      = $data['date'];
        $revenue->descricao = $data['descricao'];
        $revenue->categoria = $data['categoria'];
        $revenue->carteira  = $data['carteira'];
        $revenue->status    = $data['status'];
        $saved = $revenue->save();

        if (!$saved) {
            return response()->json(Errors::ERROR_REGISTERING_REVENUE->response());
        }

        $revenues = auth()->user()->revenues()->get();
        $valueTotalRevenuesMonth = $this->valueReleasesMonth($revenues, date('m'));
        $valuePending = $this->valuePending($revenues, date('m'), "AGUARDANDO");
        $valueReceived = $this->valuePending($revenues, date('m'), "RECEBIDA");
        $revenuesMonth = $this->releasesMonth($revenues, date('m'));

        // Mail::to($user->email)->send(new DespesaRegistradaMail($despesa));
        return response()->json([
            'success' => 'Receita cadastrada com sucesso',
            'valueTotalRevenuesMonth' => $valueTotalRevenuesMonth,
            'revenues' => $revenues,
            'valueReceived' => $valueReceived,
            'valuePending' => $valuePending,
            'revenuesMonth' => $revenuesMonth
        ], 200);
    }

    public function receivedRevenue(Request $request)
    {
        $revenue = Revenue::find($request->id);
        $revenue->status = 'RECEBIDA';
        $saved = $revenue->save();
        if (!$saved) {
            return Errors::ERROR_PAY_REVENUE->response();
        }

        $revenues = auth()->user()->revenues()->get();
        $valueTotalRevenuesMonth = $this->valueReleasesMonth($revenues, date('m'));
        $valuePending = $this->valuePending($revenues, date('m'), "AGUARDANDO");
        $valueReceived = $this->valuePending($revenues, date('m'), "RECEBIDA");
        $revenuesMonth = $this->releasesMonth($revenues, date('m'));

        return response()->json([
            'msg' => 'Receita recebida com sucesso',
            'valueTotalRevenuesMonth' => $valueTotalRevenuesMonth,
            'valueReceived' => $valueReceived,
            'revenuesMonth' => $revenuesMonth,
            'valuePending' => $valuePending,
            'revenues' => $revenues,
        ],200);
    }

    public function editRevenue(Request $request)
    {
        $revenue = Revenue::find($request->id);
        $revenue->valor = $request->valor;
        $revenue->date = $request->date;
        $revenue->descricao = $request->descricao;
        $revenue->categoria = $request->categoria;
        $revenue->carteira = $request->carteira;
        $revenue->status = $request->status;
        $saved = $revenue->save();

        if (!$saved) return response()->json(Errors::ERROR_UPDATING_REVENUE->response());

        $revenues = auth()->user()->revenues()->get();
        $valueTotalRevenuesMonth = $this->valueReleasesMonth($revenues, date('m'));
        $valuePending = $this->valuePending($revenues, date('m'), "AGUARDANDO");
        $valueReceived = $this->valuePending($revenues, date('m'), "RECEBIDA");
        $revenuesMonth = $this->releasesMonth($revenues, date('m'));

        return response()->json([
            'msg' => 'Receita recebida com sucesso',
            'valueTotalRevenuesMonth' => $valueTotalRevenuesMonth,
            'valueReceived' => $valueReceived,
            'revenuesMonth' => $revenuesMonth,
            'valuePending' => $valuePending,
            'revenues' => $revenues,
        ],200);
    }

    public function deleteRevenue(Request $request)
    {
        $deleted = Revenue::destroy($request->id);
        if (!$deleted) {
            return response()->json(Errors::ERROR_DELETING_REVENUE->response());
        }

        $revenues = auth()->user()->revenues()->get();
        $valueTotalRevenuesMonth = $this->valueReleasesMonth($revenues, date('m'));
        $valuePending = $this->valuePending($revenues, date('m'), "AGUARDANDO");
        $valueReceived = $this->valuePending($revenues, date('m'), "RECEBIDA");
        $revenuesMonth = $this->releasesMonth($revenues, date('m'));

        return response()->json([
            'msg' => 'Receita recebida com sucesso',
            'valueTotalRevenuesMonth' => $valueTotalRevenuesMonth,
            'valueReceived' => $valueReceived,
            'revenuesMonth' => $revenuesMonth,
            'valuePending' => $valuePending,
            'revenues' => $revenues,
        ],200);
    }


    public function getExpense()
    {
        if ($revenues = auth()->user()->revenues()->get()) {
            return response()->json(['expenses' => $revenues]);
        }

        return response()->json(Errors::ERROR_FETCHING_EXPENSE->response());
    }
}
