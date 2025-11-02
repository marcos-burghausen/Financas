<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lancamento;
use App\Models\Role;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Listar todos os usuários com suas roles
     */
    public function listUsers(): JsonResponse
    {
        $users = User::with('roles')
            ->select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Obter estatísticas do sistema
     */
    public function getStats(): JsonResponse
    {
        // Total de usuários
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        // Usuários por role
        $usersByRole = Role::withCount('users')
            ->get()
            ->map(function ($role) {
                return [
                    'role_name' => $role->display_name,
                    'count' => $role->users_count
                ];
            });

        // Total de lançamentos
        $totalLancamentos = Lancamento::count();

        // Lançamentos deste mês
        $lancamentosThisMonth = Lancamento::whereYear('data_lancamento', now()->year)
            ->whereMonth('data_lancamento', now()->month)
            ->count();

        // Lançamentos por usuário (top 5)
        $lancamentosPorUsuario = DB::table('lancamentos')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->user_id);
                return [
                    'user_name' => $user ? $user->name : 'Usuário desconhecido',
                    'total' => $item->total
                ];
            });

        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'users_by_role' => $usersByRole,
            'total_lancamentos' => $totalLancamentos,
            'lancamentos_this_month' => $lancamentosThisMonth,
            'lancamentos_por_usuario' => $lancamentosPorUsuario,
        ]);
    }

    /**
     * Alternar status ativo/inativo do usuário
     */
    public function toggleUserStatus(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        // Não permitir desativar o próprio usuário
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Você não pode desativar sua própria conta'
            ], 403);
        }

        // Alternar email_verified_at (representa ativo/inativo)
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $message = 'Usuário desativado com sucesso';
        } else {
            $user->email_verified_at = now();
            $message = 'Usuário ativado com sucesso';
        }

        $user->save();

        return response()->json([
            'message' => $message,
            'is_active' => $user->email_verified_at !== null,
            'user' => $user
        ]);
    }

    /**
     * Atualizar dados de um usuário
     */
    public function updateUser(Request $request, int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Deletar um usuário
     */
    public function deleteUser(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        // Não permitir deletar o próprio usuário
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Você não pode deletar sua própria conta'
            ], 403);
        }

        // Não permitir deletar usuário com role FULL
        if ($user->hasRole('FULL')) {
            return response()->json([
                'message' => 'Não é possível deletar um usuário com permissão FULL'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso'
        ]);
    }

    /**
     * Obter logs de atividades
     */
    public function getActivityLogs(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 50);
        $action = $request->input('action'); // Filtro por ação
        $email = $request->input('email'); // Filtro por usuário
        $dateFrom = $request->input('date_from'); // Data inicial
        $dateTo = $request->input('date_to'); // Data final

        $query = Log::query()->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($action) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($email) {
            $query->where('email', 'like', "%{$email}%");
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate($perPage);

        return response()->json($logs);
    }
}
