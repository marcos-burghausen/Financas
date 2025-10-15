<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::USER,
                'display_name' => 'Usuário',
                'description' => 'Usuário básico com acesso a funcionalidades pessoais de finanças',
                'permissions' => [
                    'lancamentos.view',
                    'lancamentos.create',
                    'lancamentos.update',
                    'lancamentos.delete',
                    'contas.view',
                    'contas.create',
                    'contas.update',
                    'contas.delete',
                    'categorias.view',
                    'relatorios.basicos',
                ],
            ],
            [
                'name' => Role::TRADER,
                'display_name' => 'Trader',
                'description' => 'Trader com acesso a funcionalidades de investimentos e operações',
                'permissions' => [
                    'investimentos.view',
                    'investimentos.create',
                    'investimentos.update',
                    'investimentos.delete',
                    'operacoes.view',
                    'operacoes.create',
                    'ativos.view',
                    'carteira.view',
                    'relatorios.trader',
                ],
            ],
            [
                'name' => Role::USER_TRADER,
                'display_name' => 'Usuário + Trader',
                'description' => 'Perfil completo com acesso a finanças pessoais e investimentos',
                'permissions' => [
                    // Permissões de USER
                    'lancamentos.view',
                    'lancamentos.create',
                    'lancamentos.update',
                    'lancamentos.delete',
                    'contas.view',
                    'contas.create',
                    'contas.update',
                    'contas.delete',
                    'categorias.view',
                    'relatorios.basicos',
                    // Permissões de TRADER
                    'investimentos.view',
                    'investimentos.create',
                    'investimentos.update',
                    'investimentos.delete',
                    'operacoes.view',
                    'operacoes.create',
                    'ativos.view',
                    'carteira.view',
                    'relatorios.trader',
                ],
            ],
            [
                'name' => Role::ADMIN,
                'display_name' => 'Administrador',
                'description' => 'Administrador com acesso a gerenciamento de usuários e sistema',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'roles.view',
                    'roles.assign',
                    'system.config',
                    'relatorios.admin',
                    'logs.view',
                ],
            ],
            [
                'name' => Role::FULL,
                'display_name' => 'Acesso Completo',
                'description' => 'Acesso total a todas as funcionalidades do sistema',
                'permissions' => ['*'], // Todas as permissões
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('✅ Roles criadas com sucesso!');
    }
}
