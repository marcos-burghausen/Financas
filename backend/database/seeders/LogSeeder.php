<?php

namespace Database\Seeders;

use App\Models\Log;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = [
            'Login realizado',
            'Logout realizado',
            'Criação de lançamento',
            'Edição de lançamento',
            'Exclusão de lançamento',
            'Criação de conta',
            'Edição de conta',
            'Visualização de dashboard',
            'Exportação de relatório',
            'Alteração de senha',
            'Atualização de perfil',
        ];

        $users = [
            'maria.silva@email.com',
            'joao.trader@email.com',
            'ana.admin@email.com',
            'pedro.santos@email.com',
            'carla.costa@email.com',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
        ];

        $ips = [
            '192.168.1.100',
            '192.168.1.101',
            '192.168.1.102',
            '10.0.0.50',
            '10.0.0.51',
        ];

        // Criar 100 logs de exemplo
        for ($i = 0; $i < 100; $i++) {
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Log::create([
                'email' => $users[array_rand($users)],
                'timestamp' => $createdAt->format('d/m/Y - H:i:s'),
                'user_agent' => $userAgents[array_rand($userAgents)],
                'ip' => $ips[array_rand($ips)],
                'action' => $actions[array_rand($actions)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('✅ 100 logs de atividades criados com sucesso!');
    }
}
