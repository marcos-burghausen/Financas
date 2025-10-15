<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Executa os seeders na ordem correta:
     * 1. RolesSeeder - Cria as roles e permissões do sistema
     * 2. DemoDataSeeder - Cria usuários de teste com dados completos
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            DemoDataSeeder::class,
            LogSeeder::class,
        ]);

        $this->command->newLine(2);
        $this->command->info('🎉 Database seeding completo!');
        $this->command->newLine();
        $this->command->info('📝 Sistema pronto para testes com 5 usuários completos');
        $this->command->info('🔑 Senha padrão para todos: senha123');
    }
}
