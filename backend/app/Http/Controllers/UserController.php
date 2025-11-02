<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Retorna os dados do usuário autenticado
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Atualiza o perfil do usuário
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14|unique:users,cpf,' . $user->id,
            'date_of_birth' => 'nullable|date',
            'profession' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user
        ]);
    }

    /**
     * Atualiza a senha do usuário
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
        ]);

        // Verificar se a senha atual está correta
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['A senha atual está incorreta.'],
            ]);
        }

        // Atualizar a senha
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Senha alterada com sucesso!'
        ]);
    }

    /**
     * Retorna estatísticas básicas do usuário
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        $stats = [
            'contas' => $user->contas()->count(),
            'receitas' => $user->lancamentos()->where('tipo_lancamento', 'RECEITA')->count(),
            'despesas' => $user->lancamentos()->where('tipo_lancamento', 'DESPESA')->count(),
            'categorias' => $user->categorias()->count(),
        ];

        return response()->json($stats);
    }
}
