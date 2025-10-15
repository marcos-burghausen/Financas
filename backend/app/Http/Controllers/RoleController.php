<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Listar todas as roles disponíveis
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();

        return response()->json($roles);
    }

    /**
     * Atribuir role(s) a um usuário
     */
    public function assignToUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->syncRoles(...$validated['roles']);

        return response()->json([
            'message' => 'Roles atribuídas com sucesso!',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Remover role de um usuário
     */
    public function removeFromUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->removeRole($validated['role']);

        return response()->json([
            'message' => 'Role removida com sucesso!',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Obter roles de um usuário
     */
    public function userRoles(User $user)
    {
        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->roles
        ]);
    }

    /**
     * Verificar permissões do usuário autenticado
     */
    public function myPermissions(Request $request)
    {
        $user = $request->user();

        $allPermissions = [];
        foreach ($user->roles as $role) {
            $allPermissions = array_merge($allPermissions, $role->permissions ?? []);
        }

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->roles->pluck('name'),
            'permissions' => array_unique($allPermissions),
            'is_admin' => $user->isAdmin()
        ]);
    }
}
