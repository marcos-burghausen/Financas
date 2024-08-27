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
                'valor' =>[
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
                ],
                'instituicaoFinanceira' =>[
                    'required',
                    'min:2',
                    'max:20',
                    'unique:contas,name',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'descricao' =>[
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
            ]
        );

        $user = auth()->user();

        try {
            $carteira = new Conta();
            $carteira->user_id = $request->user_id;
            $carteira->name = $request->instituicaoFinanceira;
            if ($request->valor) {
                $carteira->saldo = str_replace([',', '.'], '', $request->valor);
            }
            $carteira->descricao = $request->descricao;
            $carteira->tipo = $request->tipoConta;
            $carteira->save();  
            
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
