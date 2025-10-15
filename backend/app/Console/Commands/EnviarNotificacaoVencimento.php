<?php

namespace App\Console\Commands;

use App\Models\Lancamento;
use App\Models\User;
use App\Notifications\VencimentoContaNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class EnviarNotificacaoVencimento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:vencimento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificações de vencimento de contas para usuários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Iniciando envio de notificações de vencimento...');

        $notificacoesEnviadas = 0;

        // Buscar todos os usuários com notificações ativadas
        $usuarios = User::whereHas('notificationSettings', function ($query) {
            $query->where('email_vencimento', true);
        })->with('notificationSettings')->get();

        foreach ($usuarios as $usuario) {
            $settings = $usuario->notificationSettings;
            $diasAntecedencia = $settings->dias_antecedencia_vencimento ?? 3;

            // Data limite para verificar vencimentos
            $dataLimite = Carbon::now()->addDays($diasAntecedencia);

            // Buscar lançamentos pendentes próximos ao vencimento
            $lancamentos = Lancamento::where('user_id', $usuario->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->where('status_lancamento', 'PENDENTE')
                ->whereDate('data_vencimento', '<=', $dataLimite)
                ->whereDate('data_vencimento', '>=', Carbon::now())
                ->get();

            foreach ($lancamentos as $lancamento) {
                $diasRestantes = Carbon::now()->diffInDays($lancamento->data_vencimento, false);

                // Enviar notificação
                $usuario->notify(new VencimentoContaNotification($lancamento, $diasRestantes));
                $notificacoesEnviadas++;

                $this->line("✅ Notificação enviada para {$usuario->email} - Conta: {$lancamento->descricao}");
            }
        }

        $this->info("✅ Total de notificações enviadas: {$notificacoesEnviadas}");
        return Command::SUCCESS;
    }
}
