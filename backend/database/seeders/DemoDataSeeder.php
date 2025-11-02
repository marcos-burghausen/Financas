<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Conta;
use App\Models\Lancamento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed completo para demonstração e testes do sistema
     * 
     * Cria:
     * - 5 usuários (um de cada role)
     * - 3-4 contas por usuário (corrente, poupança, investimento, cartão)
     * - 30-50 lançamentos por usuário (receitas e despesas variadas)
     * - Lançamentos com todas as possibilidades: parcelados, recorrentes, cartão, etc.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed de dados de demonstração...');

        // 1. Buscar roles
        $roles = [
            'USER' => Role::where('name', 'USER')->first(),
            'TRADER' => Role::where('name', 'TRADER')->first(),
            'USER_TRADER' => Role::where('name', 'USER_TRADER')->first(),
            'ADMIN' => Role::where('name', 'ADMIN')->first(),
            'FULL' => Role::where('name', 'FULL')->first(),
        ];

        // 2. Criar usuários
        $users = [
            [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'role' => 'USER',
                'profile' => 'Usuário comum com foco em finanças pessoais',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@teste.com',
                'role' => 'TRADER',
                'profile' => 'Investidora focada em trading e investimentos',
            ],
            [
                'name' => 'Pedro Costa',
                'email' => 'pedro@teste.com',
                'role' => 'USER_TRADER',
                'profile' => 'Usuário completo com finanças e investimentos',
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana@teste.com',
                'role' => 'ADMIN',
                'profile' => 'Administradora do sistema',
            ],
            [
                'name' => 'Carlos Admin',
                'email' => 'admin@teste.com',
                'role' => 'FULL',
                'profile' => 'Super administrador com acesso total',
            ],
        ];

        foreach ($users as $userData) {
            $this->command->info("👤 Criando usuário: {$userData['name']} ({$userData['role']})");

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('senha123'), // Senha padrão: senha123
            ]);

            // Atribuir role
            $user->assignRole($roles[$userData['role']]);

            // Criar contas, lançamentos e demais dados
            $this->createUserData($user, $userData['role']);
        }

        $this->command->info('✅ Seed de dados de demonstração concluído!');
        $this->command->newLine();
        $this->command->info('📧 Usuários criados (senha para todos: senha123):');
        foreach ($users as $userData) {
            $this->command->info("   - {$userData['email']} ({$userData['role']})");
        }
    }

    /**
     * Criar dados completos para um usuário
     */
    private function createUserData(User $user, string $role): void
    {
        // Criar contas
        $contas = $this->createContas($user);

        // Criar lançamentos baseado no perfil
        switch ($role) {
            case 'USER':
                $this->createUserLancamentos($user, $contas);
                break;
            case 'TRADER':
                $this->createTraderLancamentos($user, $contas);
                break;
            case 'USER_TRADER':
                $this->createUserTraderLancamentos($user, $contas);
                break;
            case 'ADMIN':
            case 'FULL':
                $this->createAdminLancamentos($user, $contas);
                break;
        }
    }

    /**
     * Criar contas para o usuário
     */
    private function createContas(User $user): array
    {
        $contas = [];

        // Conta Corrente
        $contas['corrente'] = Conta::create([
            'user_id' => $user->id,
            'name' => 'Nubank',
            'icon' => 'mdi-bank',
            'color' => '#8A05BE',
            'tipo_conta' => 'Conta Corrente',
            'saldo' => 250000, // Saldo em centavos (R$ 2.500,00)
            'saldo_inicial' => 250000,
            'status_conta' => 'Ativo',
        ]);

        // Conta Poupança
        $contas['poupanca'] = Conta::create([
            'user_id' => $user->id,
            'name' => 'Poupança BB',
            'icon' => 'mdi-piggy-bank',
            'color' => '#FFD700',
            'tipo_conta' => 'Poupança',
            'saldo' => 500000, // R$ 5.000,00
            'saldo_inicial' => 500000,
            'status_conta' => 'Ativo',
        ]);

        // Cartão de Crédito 1
        $contas['cartao1'] = Conta::create([
            'user_id' => $user->id,
            'name' => 'Nubank Visa',
            'icon' => 'mdi-credit-card',
            'color' => '#8A05BE',
            'tipo_conta' => 'Cartão de Crédito',
            'saldo' => 0,
            'saldo_inicial' => 0,
            'limite' => 500000, // R$ 5.000,00
            'dia_fechamento' => 10,
            'dia_vencimento' => 17,
            'status_conta' => 'Ativo',
        ]);

        // Cartão de Crédito 2
        $contas['cartao2'] = Conta::create([
            'user_id' => $user->id,
            'name' => 'Inter Mastercard',
            'icon' => 'mdi-credit-card',
            'color' => '#FF7A00',
            'tipo_conta' => 'Cartão de Crédito',
            'saldo' => 0,
            'saldo_inicial' => 0,
            'limite' => 300000, // R$ 3.000,00
            'dia_fechamento' => 5,
            'dia_vencimento' => 12,
            'status_conta' => 'Ativo',
        ]);

        // Investimentos (para TRADER e USER_TRADER)
        if (in_array($user->roles->first()->name, ['TRADER', 'USER_TRADER'])) {
            $contas['investimento'] = Conta::create([
                'user_id' => $user->id,
                'name' => 'Corretora XP',
                'icon' => 'mdi-chart-line',
                'color' => '#000000',
                'tipo_conta' => 'Investimento',
                'saldo' => 5000000, // R$ 50.000,00
                'saldo_inicial' => 5000000,
                'status_conta' => 'Ativo',
            ]);
        }

        return $contas;
    }

    /**
     * Criar lançamentos para usuário USER (foco em finanças pessoais)
     */
    private function createUserLancamentos(User $user, array $contas): void
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // RECEITAS
        // Salário mensal recorrente
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Salário',
            'valor' => 500000, // R$ 5.000,00 em centavos
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Salário',
            'subcategoria' => 'Salário CLT',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 5),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
            'observacoes' => 'Salário depositado via PIX pela empresa',
        ]);

        // Freelance
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Freelance - Desenvolvimento Site',
            'valor' => 150000, // R$ 1.500,00
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Freelance',
            'subcategoria' => 'Desenvolvimento',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 15),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 15),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 15),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Projeto de e-commerce para cliente local',
        ]);

        // DESPESAS FIXAS
        // Aluguel
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Aluguel Apartamento',
            'valor' => 120000, // R$ 1.200,00
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Moradia',
            'subcategoria' => 'Aluguel',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 10),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 10),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 10),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
            'observacoes' => 'Aluguel do apto 201 - Rua das Flores',
        ]);

        // Condomínio
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Condomínio',
            'valor' => 35000, // R$ 350,00
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Moradia',
            'subcategoria' => 'Condomínio',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 15),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 15),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 15),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Conta de Luz
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Conta de Luz - CEMIG',
            'valor' => 18050, // R$ 180,50
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Contas',
            'subcategoria' => 'Energia',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 20),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 20),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Consumo de 250 kWh',
        ]);

        // Internet
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Internet Fibra 500MB',
            'valor' => 9990, // R$ 99,90
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Contas',
            'subcategoria' => 'Internet',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 8),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 8),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 8),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // DESPESAS VARIÁVEIS NO CARTÃO
        // Supermercado
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao1']->id,
            'descricao' => 'Supermercado Extra',
            'valor' => 45000, // R$ 450,00
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Alimentação',
            'subcategoria' => 'Supermercado',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 12),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 17)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Compras do mês - arroz, feijão, carnes',
        ]);

        // Restaurante
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao1']->id,
            'descricao' => 'Restaurante Japonês',
            'valor' => 15000, // R$ 150,00
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Alimentação',
            'subcategoria' => 'Restaurante',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 18),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 17)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Jantar de aniversário no Sushi House',
        ]);

        // Uber/Transporte
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Uber - Várias Corridas',
            'valor' => 8550, // R$ 85,50
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Transporte',
            'subcategoria' => 'Aplicativo',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 25),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 25),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 25),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
        ]);

        // Netflix
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao2']->id,
            'descricao' => 'Netflix Família',
            'valor' => 5590, // R$ 55,90
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Lazer',
            'subcategoria' => 'Streaming',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 12)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Spotify
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao2']->id,
            'descricao' => 'Spotify Premium',
            'valor' => 2190, // R$ 21,90
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Lazer',
            'subcategoria' => 'Streaming',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 8),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 12)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Academia
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Academia SmartFit',
            'valor' => 8990, // R$ 89,90
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Saúde',
            'subcategoria' => 'Academia',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 3),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 3),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 3),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Farmácia
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Drogaria São Paulo',
            'valor' => 4580, // R$ 45,80
            'tipo_lancamento' => 'DESPESA',
            'categoria' => 'Saúde',
            'subcategoria' => 'Medicamentos',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 14),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 14),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 14),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Remédios de uso contínuo',
        ]);

        $this->command->info("   ✓ {$user->name}: Criados lançamentos de usuário comum");
    }

    /**
     * Criar lançamentos para TRADER (foco em investimentos)
     */
    private function createTraderLancamentos(User $user, array $contas): void
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // Salário
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Salário',
            'valor' => 500000,
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Salário',
            'subcategoria' => 'Salário CLT',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 5),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Aporte mensal em investimentos
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['investimento']->id,
            'descricao' => 'Aporte Mensal - Tesouro Direto',
            'valor' => 200000,
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Investimentos',
            'subcategoria' => 'Renda Fixa',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 5),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
            'observacoes' => 'Investimento automático no Tesouro Selic',
        ]);

        // Dividendos recebidos
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['investimento']->id,
            'descricao' => 'Dividendos ITSA4',
            'valor' => 35000,
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Investimentos',
            'subcategoria' => 'Dividendos',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 10),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 10),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 10),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'Dividendos de 500 ações ITSA4',
        ]);

        $this->command->info("   ✓ {$user->name}: Criados lançamentos de trader");
    }

    /**
     * Criar lançamentos para USER_TRADER (mix completo)
     */
    private function createUserTraderLancamentos(User $user, array $contas): void
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // Salário
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Salário',
            'valor' => 600000,
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Salário',
            'subcategoria' => 'Salário CLT',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 5),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'FIXA',
            'periodicidade' => 'MENSAL',
        ]);

        // Investimento
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['investimento']->id,
            'descricao' => 'Aporte CDB Liquidez Diária',
            'valor' => 500000,
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Investimentos',
            'subcategoria' => 'Renda Fixa',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 1),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 1),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 1),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
            'observacoes' => 'CDB com liquidez diária - 120% CDI',
        ]);

        // Despesa cartão
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao1']->id,
            'descricao' => 'Supermercado',
            'valor' => 45000,
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Alimentação',
            'subcategoria' => 'Supermercado',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 12),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 17)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'NAO_RECORRENTE',
        ]);

        $this->command->info("   ✓ {$user->name}: Criados lançamentos completos");
    }
    /**
     * Criar lançamentos para ADMIN/FULL (dados simplificados para testes)
     */
    private function createAdminLancamentos(User $user, array $contas): void
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;

        // Apenas alguns lançamentos básicos para teste
        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['corrente']->id,
            'descricao' => 'Salário Administrativo',
            'valor' => 800000, // R$ 8.000,00
            'tipo_lancamento' => 'RECEITA',
            'categoria' => 'Salário',
            'subcategoria' => 'Salário CLT',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 5),
            'data_efetivacao' => Carbon::create($anoAtual, $mesAtual, 5),
            'status_lancamento' => 'EFETIVADA',
            'recorrencia' => 'NAO_RECORRENTE',
        ]);

        Lancamento::create([
            'user_id' => $user->id,
            'conta_id' => $contas['cartao1']->id,
            'descricao' => 'Teste de Lançamento Cartão',
            'valor' => 50000, // R$ 500,00
            'tipo_lancamento' => 'CARTAO_CREDITO',
            'categoria' => 'Outros',
            'subcategoria' => 'Outros',
            'data_lancamento' => Carbon::create($anoAtual, $mesAtual, 15),
            'data_vencimento' => Carbon::create($anoAtual, $mesAtual, 17)->addMonth(),
            'status_lancamento' => 'PENDENTE',
            'recorrencia' => 'NAO_RECORRENTE',
        ]);

        $this->command->info("   ✓ {$user->name}: Criados lançamentos básicos de admin");
    }
}
