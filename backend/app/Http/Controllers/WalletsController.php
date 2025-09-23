<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\Errors;
use App\Models\Conta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsController extends Controller
{
    public function saveWallet(Request $request)
    {

        $data = $request->validate(
            [
                'valor' => [
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
                ],
                'instituicaoFinanceira' => [
                    'required',
                    'min:2',
                    'max:20',
                    'unique:contas,name',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'descricao' => [
                    "nullable",
                    'max:50',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'tipoConta' => [
                    'required',
                    'min:2',
                    'max:20',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/',
                ],
                'bandeira' => 'required_if:tipo,Pessoal|string|nullable',
                'limite' => 'required_if:tipo,Pessoal|nullable|regex:/^[0-9,.]+$/',
                'dia_fechamento' => 'required_if:tipo,Pessoal|integer|min:1|max:31|nullable',
                'dia_vencimento' => 'required_if:tipo,Pessoal|integer|min:1|max:31|nullable',
            ],
            [
                'valor.max'                      => 'O campo Valor deve ter no máiximo 40 caracteres',
                'valor.regex'                    => 'O campo Valor deve conter um valor monetário',
                'instituicaoFinanceira.required' => 'O campo Instituição financeira é obrigatório',
                'instituicaoFinanceira.min'      => 'O campo Instituição deve ter pelo menos 2 caracteres',
                'instituicaoFinanceira.max'      => 'O campo Instituição deve ter no máiximo 20 caracteres',
                'instituicaoFinanceira.regex'    => 'O campo Instituição deve conter apenas letras',
                'instituicaoFinanceira.unique'   => 'Você ja possui uma conta com esse nome',
                'descricao.max'                  => 'O campo Descrição deve ter no máiximo 50 caracteres',
                'descricao.regex'                => 'O campo Descricao deve conter apenas letras',
                'tipoConta.required'             => 'O campo Tipo de conta é obrigatório',
                'tipoConta.min'                  => 'O campo Tipo de conta deve ter pelo menos 2 caracteres',
                'tipoConta.min'                  => 'O campo Tipo de conta deve ter no máiximo 20 caracteres',
                'tipoConta.regex'                => 'O campo noTipo de contame deve conter apenas letras',
                'bandeira.required_if' => 'A bandeira é obrigatória para cartões de crédito.',
                'limite.required_if' => 'O limite é obrigatório para cartões de crédito.',
            ]
        );

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $conta                    = new Conta();
            $conta->user_id           = $request->user_id;
            $conta->name              = $request->instituicaoFinanceira;
            if ($request->valor) {
                $conta->saldo         = (int) str_replace([',', '.'], '', $request->valor);
                $conta->saldo_inicial = (int) str_replace([',', '.'], '', $request->valor);
            }
            $conta->descricao         = $request->descricao;
            $conta->tipo              = $request->tipoConta;

            if ($request->tipoConta === 'Pessoal') {
                $conta->bandeira = $request->bandeira;
                $conta->dia_fechamento = $request->dia_fechamento;
                $conta->dia_vencimento = $request->dia_vencimento;
            
                if ($request->limite) {
                    $conta->limite = (int) str_replace([',', '.'], '', $request->limite);
                }
            }
            $conta->save();

            DB::commit();
        } catch (\Throwable $e) {
            info($e);
            DB::rollBack();
            // return response()->json(['error' => 'Erro ao adicionar carteira.'], 500);
            return Errors::ERROR_CREATE_CONTA->response();
        }
        // $user = User::find($request->user_id);
        // $carteiras = $user->carteiras;
        $walletsData = Conta::select('name', 'icon', 'saldo', 'tipo')->get();
        $wallets = [];
        foreach ($walletsData as $wallet) {
            $wallets[$wallet['name']] = [
                'name' => $wallet['name'],
                'icon' => $wallet['icon'],
                'valor' => $wallet['valor'],
                'tipo' => $wallet['tipo'],
            ];
        }

        LogController::addsLog($user->email, Actions::USER_CREATE_NEW_WALLET);

        return response()->json([
            'success' => 'Carteira add com sucesso.',
            'wallets' => $wallets,
        ], 200);
    }

    public function editWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        $carteiras[$request->key] = $request->carteira;
        $user->carteiras = $carteiras;
        $saved = $user->save();
        if ($saved) {
            return response()->json('carteira editada com sucesso');
        }
        return response()->json('Erro ao editar carteira');
    }

    public function deleteWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        if (!in_array($request->carteira, $carteiras)) return response()->json('Carteira não encontrado');
        $user->carteiras = array_diff($carteiras, [$request->carteira]);
        $saved = $user->save();
        if ($saved) {
            return response()->json('carteira excluida com sucesso');
        }
        return response()->json('Erro ao excluir carteira');
    }

    public function getWallets(Request $request)
    {
        $user = User::find($request->user_id);
        $carteiras = $user->carteiras;
        return response()->json($carteiras);
    }
}
