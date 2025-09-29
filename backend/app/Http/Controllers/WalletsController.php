<?php

namespace App\Http\Controllers;

use App\Enums\Actions;
use App\Enums\Errors;
use App\Models\Conta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Traits\ReleasesMonthTrait;
use App\Http\Traits\UserDataTrait;

class WalletsController extends Controller
{
    use ReleasesMonthTrait, UserDataTrait;

    public function saveWallet(Request $request)
    {
        $data = $request->validate(
            [
                'name' => [
                    'required',
                    'min:2',
                    'max:20',
                    Rule::unique('contas')->where('tipoConta', $request->tipoConta),
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'color' => 'nullable|string',
                'conta' => 'nullable|string',
                'icon' => 'nullable|string',
                'incluirEmSomaInicial' => 'nullable|boolean',
                'saldo' => [
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
                ],
                'saldoInicial' => [
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
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
                'bandeira' => 'required_if:tipo,Carteira|string|nullable',
                'limite' => 'required_if:tipo,Carteira|nullable|regex:/^[0-9,.]+$/',
                'dia_fechamento' => 'required_if:tipo,Carteira|integer|min:1|max:31|nullable',
                'dia_vencimento' => 'required_if:tipo,Carteira|integer|min:1|max:31|nullable',
            ],
            [
                'saldo.max'                      => 'O campo saldo deve ter no máiximo 40 caracteres',
                'saldo.regex'                    => 'O campo saldo deve conter um valor monetário',
                'name.required' => 'O campo Nome é obrigatório',
                'name.min'      => 'O campo Nome deve ter pelo menos 2 caracteres',
                'name.max'      => 'O campo Nome deve ter no máiximo 20 caracteres',
                'name.regex'    => 'O campo Nome deve conter apenas letras',
                'name.unique'   => 'Você ja possui uma conta com esse nome',
                'descricao.max'                  => 'O campo Descrição deve ter no máiximo 50 caracteres',
                'descricao.regex'                => 'O campo Descricao deve conter apenas letras',
                'tipoConta.required'             => 'O campo Tipo de conta é obrigatório',
                'tipoConta.min'                  => 'O campo Tipo de conta deve ter pelo menos 2 caracteres',
                'tipoConta.min'                  => 'O campo Tipo de conta deve ter no máiximo 20 caracteres',
                'tipoConta.regex'                => 'O campo Tipo de conta deve conter apenas letras',
                'bandeira.required_if' => 'A bandeira é obrigatória para cartões de crédito.',
                'limite.required_if' => 'O limite é obrigatório para cartões de crédito.',
            ]
        );
        // return response()->json([$data], 200);

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $conta                    = new Conta();
            $conta->user_id           = $user->id;
            $conta->name              = $data['name'];
            if ($data['saldo']) {
                $conta->saldo         = (int) str_replace([',', '.'], '', $data['saldo']);
                $conta->saldoInicial = (int) str_replace([',', '.'], '', $data['saldo']);
            }

            if ($conta->descricao) {
                $conta->descricao = $data['descricao'];
            }
            $conta->tipoConta = $data['tipoConta'];

            if ($data['tipoConta'] === 'Cartão de Crédito') {
                $conta->icon = $data['bandeira'];
                $conta->dia_fechamento = $data['dia_fechamento'];
                $conta->dia_vencimento = $data['dia_vencimento'];

                if ($data['limite']) {
                    $conta->limite = (int) str_replace([',', '.'], '', $data['limite']);
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
        $data = $this->getUserData($user, 'wallets');

        LogController::addsLog($user->email, Actions::USER_CREATE_NEW_WALLET);

        return response()->json([
            'success' => 'Carteira add com sucesso.',
            'wallets' => $data,
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
