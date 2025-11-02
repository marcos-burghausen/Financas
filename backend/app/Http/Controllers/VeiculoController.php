<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $veiculos = Veiculo::where('user_id', $user->id)
            ->with('manutencoes')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $veiculos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|unique:veiculos,placa',
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'ano' => 'required|integer|min:1900',
            'cor' => 'nullable|string',
            'quilometragem' => 'required|integer|min:0',
            'combustivel' => 'required|in:Gasolina,Diesel,Etanol,Híbrido,Elétrico',
            'proximaManutencao' => 'required|integer|min:0',
            'status' => 'required|in:ativo,inativo,manutenção',
        ]);

        $veiculo = Veiculo::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Veículo criado com sucesso!',
            'data' => $veiculo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Veiculo $veiculo)
    {
        // Verificar se o veículo pertence ao usuário autenticado
        if ($veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $veiculo->load('manutencoes.itens');

        return response()->json([
            'success' => true,
            'data' => $veiculo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Veiculo $veiculo)
    {
        // Verificar se o veículo pertence ao usuário autenticado
        if ($veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $validated = $request->validate([
            'placa' => 'sometimes|string|unique:veiculos,placa,' . $veiculo->id,
            'marca' => 'sometimes|string',
            'modelo' => 'sometimes|string',
            'ano' => 'sometimes|integer|min:1900',
            'cor' => 'nullable|string',
            'quilometragem' => 'sometimes|integer|min:0',
            'combustivel' => 'sometimes|in:Gasolina,Diesel,Etanol,Híbrido,Elétrico',
            'proximaManutencao' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:ativo,inativo,manutenção',
        ]);

        $veiculo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Veículo atualizado com sucesso!',
            'data' => $veiculo,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veiculo $veiculo)
    {
        // Verificar se o veículo pertence ao usuário autenticado
        if ($veiculo->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $veiculo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Veículo deletado com sucesso!',
        ]);
    }
}
