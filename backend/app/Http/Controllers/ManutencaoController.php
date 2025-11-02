<?php

namespace App\Http\Controllers;

use App\Models\Manutencao;
use App\Models\Veiculo;
use App\Models\ManutencaoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManutencaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $manutencoes = Manutencao::whereHas('veiculo', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('veiculo', 'itens')
            ->orderBy('data', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $manutencoes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculos,id',
            'tipo' => 'required|string',
            'data' => 'required|date',
            'quilometragem' => 'required|integer|min:0',
            'oficina_nome' => 'nullable|string',
            'oficina_telefone' => 'nullable|string',
            'oficina_email' => 'nullable|email',
            'oficina_endereco' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.nome' => 'required|string',
            'itens.*.descricao' => 'nullable|string',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        // Verificar se o veículo pertence ao usuário autenticado
        $veiculo = Veiculo::find($validated['veiculo_id']);
        if ($veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Calcular valor total
            $valorTotal = 0;
            foreach ($validated['itens'] as $item) {
                $valorTotal += $item['quantidade'] * $item['valor_unitario'];
            }

            // Criar manutenção
            $manutencao = Manutencao::create([
                'veiculo_id' => $validated['veiculo_id'],
                'tipo' => $validated['tipo'],
                'data' => $validated['data'],
                'quilometragem' => $validated['quilometragem'],
                'valor_total' => $valorTotal,
                'oficina_nome' => $validated['oficina_nome'],
                'oficina_telefone' => $validated['oficina_telefone'],
                'oficina_email' => $validated['oficina_email'],
                'oficina_endereco' => $validated['oficina_endereco'],
                'observacoes' => $validated['observacoes'],
            ]);

            // Criar itens
            foreach ($validated['itens'] as $item) {
                ManutencaoItem::create([
                    'manutencao_id' => $manutencao->id,
                    'nome' => $item['nome'],
                    'descricao' => $item['descricao'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $item['valor_unitario'],
                    'valor_total' => $item['quantidade'] * $item['valor_unitario'],
                ]);
            }

            // Atualizar quilometragem do veículo
            $veiculo->update(['quilometragem' => $validated['quilometragem']]);

            DB::commit();

            $manutencao->load('itens');

            return response()->json([
                'success' => true,
                'message' => 'Manutenção registrada com sucesso!',
                'data' => $manutencao,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar manutenção: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Manutencao $manutencao)
    {
        // Verificar se a manutenção pertence a um veículo do usuário autenticado
        if ($manutencao->veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $manutencao->load('veiculo', 'itens');

        return response()->json([
            'success' => true,
            'data' => $manutencao,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manutencao $manutencao)
    {
        // Verificar autorização
        if ($manutencao->veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $validated = $request->validate([
            'tipo' => 'sometimes|string',
            'data' => 'sometimes|date',
            'quilometragem' => 'sometimes|integer|min:0',
            'oficina_nome' => 'nullable|string',
            'oficina_telefone' => 'nullable|string',
            'oficina_email' => 'nullable|email',
            'oficina_endereco' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'itens' => 'sometimes|array|min:1',
            'itens.*.id' => 'nullable|exists:manutencao_itens,id',
            'itens.*.nome' => 'required|string',
            'itens.*.descricao' => 'nullable|string',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Atualizar manutenção
            $manutencao->update($validated);

            // Atualizar itens se fornecidos
            if (isset($validated['itens'])) {
                // Deletar itens antigos
                ManutencaoItem::where('manutencao_id', $manutencao->id)->delete();

                // Criar novos itens
                $valorTotal = 0;
                foreach ($validated['itens'] as $item) {
                    $valorUnitario = $item['valor_unitario'];
                    $quantidade = $item['quantidade'];
                    $valorTotalItem = $quantidade * $valorUnitario;
                    $valorTotal += $valorTotalItem;

                    ManutencaoItem::create([
                        'manutencao_id' => $manutencao->id,
                        'nome' => $item['nome'],
                        'descricao' => $item['descricao'],
                        'quantidade' => $quantidade,
                        'valor_unitario' => $valorUnitario,
                        'valor_total' => $valorTotalItem,
                    ]);
                }

                // Atualizar valor total
                $manutencao->update(['valor_total' => $valorTotal]);
            }

            DB::commit();

            $manutencao->load('itens');

            return response()->json([
                'success' => true,
                'message' => 'Manutenção atualizada com sucesso!',
                'data' => $manutencao,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar manutenção: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manutencao $manutencao)
    {
        // Verificar autorização
        if ($manutencao->veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $manutencao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Manutenção deletada com sucesso!',
        ]);
    }
}
