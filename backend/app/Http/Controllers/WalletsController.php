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
        $user = auth()->user();

        $validatedData  = $request->validate(
            [
                'id' => 'nullable|integer|exists:contas,id,user_id,',
                'name' => [
                    'required',
                    'min:2',
                    'max:20',
                    // Rule::unique('contas')->where('tipoConta', $request->tipoConta),
                    // Garante que o nome seja único para o usuário E para o tipo de conta.
                    Rule::unique('contas')->where(function ($query) use ($request, $user) {
                        return $query->where('user_id', $user->id)
                            ->where('tipo_conta', $request->tipo_conta);
                    })->ignore($request->id),
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'tipo_conta' => ['required', Rule::in(['Carteira', 'Conta Corrente', 'Poupança', 'Investimento', 'Outro', 'Cartão de Crédito'])],
                'color' => 'nullable|string',
                'conta_id' => 'nullable|integer|exists:contas,id',
                'icon' => 'nullable|string',
                'incluir_em_soma_inicial' => 'nullable|boolean',
                'saldo' => [
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
                ],
                'saldo_inicial' => [
                    'nullable',
                    'max:40',
                    'regex:/^[0-9,.]+$/'
                ],
                'descricao' => [
                    "nullable",
                    'max:50',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
                ],
                'bandeira'       => 'required_if:tipo,Carteira|string|nullable',
                'limite'         => 'required_if:tipo,Carteira|nullable|regex:/^[0-9,.]+$/',
                'dia_fechamento' => 'required_if:tipo,Carteira|integer|min:1|max:31|nullable',
                'dia_vencimento' => 'required_if:tipo,Carteira|integer|min:1|max:31|nullable',
            ],
            [
                'saldo.max'            => 'O campo saldo deve ter no máiximo 40 caracteres',
                'saldo.regex'          => 'O campo saldo deve conter um valor monetário',
                'name.required'        => 'O campo Nome é obrigatório',
                'name.min'             => 'O campo Nome deve ter pelo menos 2 caracteres',
                'name.max'             => 'O campo Nome deve ter no máiximo 20 caracteres',
                'name.regex'           => 'O campo Nome deve conter apenas letras',
                'name.unique'          => 'Você ja possui uma conta com esse nome',
                'descricao.max'        => 'O campo Descrição deve ter no máiximo 50 caracteres',
                'descricao.regex'      => 'O campo Descricao deve conter apenas letras',
                'tipo_conta.required'  => 'O campo Tipo de conta é obrigatório',
                'tipo_conta.min'       => 'O campo Tipo de conta deve ter pelo menos 2 caracteres',
                'tipo_conta.min'       => 'O campo Tipo de conta deve ter no máiximo 20 caracteres',
                'tipo_conta.regex'     => 'O campo Tipo de conta deve conter apenas letras',
                'bandeira.required_if' => 'A bandeira é obrigatória para cartões de crédito.',
                'limite.required_if'   => 'O limite é obrigatório para cartões de crédito.',
            ]
        );

        try {
            $dataToSave = [
                'user_id' => $user->id,
                'name' => $validatedData['name'],
                'tipo_conta' => $validatedData['tipo_conta'],
                // 'saldo_inicial' => $validatedData['saldo_inicial'] ?? $validatedData['saldo_inicial'] * 100 ?? 0,
                'saldo' => (int) str_replace([',', '.'], '', $validatedData['saldo']),
                'incluir_em_soma_inicial' => $validatedData['incluir_em_soma_inicial'],
                'icon' => $validatedData['icon'] ?? 'default_icon',
                // 'descricao' => $validatedData['descricao'],
                'status_conta' => 'Ativo',
            ];

            if ($validatedData['tipo_conta'] === 'Cartão de Crédito') {
                $dataToSave['limite'] = (int) str_replace([',', '.'], '', $validatedData['limite']);
                $dataToSave['dia_vencimento'] = $validatedData['dia_vencimento'];
                $dataToSave['dia_fechamento'] = $validatedData['dia_fechamento'];
                $dataToSave['conta_pai_id'] = $validatedData['conta_id'];
            } else {
                $dataToSave['saldo'] = (int) str_replace([',', '.'], '', $validatedData['saldo_inicial']);
            }

            $conta = Conta::updateOrCreate(
                ['id' => $request->id, 'user_id' => $user->id],
                $dataToSave
            );

            $allWalletsData = $this->getUserData($user, date('Y-m'), ['wallets']);

            return response()->json([
                'success' => 'Conta salva com sucesso!',
                'wallets' => $allWalletsData['wallets']
            ], 201);
        } catch (\Throwable $e) {
            info($e);
            DB::rollBack();
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

    /**
     * Busca todas as faturas de um cartão de crédito específico.
     */
    public function getInvoices(Conta $conta)
    {
        // Garante que o usuário só possa ver as faturas do seu próprio cartão
        if ($conta->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        if ($conta->tipo_conta !== 'Cartão de Crédito') {
            return response()->json(['error' => 'A conta não é um cartão de crédito.'], 422);
        }

        $invoices = CreditCardInvoice::where('conta_id', $conta->id)
            ->with('lancamentos') // Carrega os lançamentos junto com cada fatura
            ->orderBy('competencia', 'desc')
            ->get();

        return response()->json(['invoices' => $invoices]);
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
