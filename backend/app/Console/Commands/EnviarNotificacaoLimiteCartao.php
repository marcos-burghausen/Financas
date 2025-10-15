<?php

namespace App\Console\Commands;

use App\Models\Lancamento;
use App\Models\User;
use App\Notifications\LimiteCartaoNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnviarNotificacaoLimiteCartao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:limite-cartao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificações quando cartões atingem percentual limite configurado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('💳 Iniciando verificação de limites de cartão...');

        $notificacoesEnviadas = 0;

        // Buscar todos os usuários com notificações de limite de cartão ativadas
        $usuarios = User::whereHas('notificationSettings', function ($query) {
            $query->where('email_limite_cartao', true);
        })->with('notificationSettings')->get();

        foreach ($usuarios as $usuario) {
            $settings = $usuario->notificationSettings;
            $percentualAlerta = $settings->percentual_alerta_cartao ?? 80;

            // Buscar cartões de crédito ativos do usuário
            $cartoes = DB::table('contas')
                ->where('user_id', $usuario->id)
                ->where('tipo_conta', 'Cartão de Crédito')
                ->where('status_conta', 'Ativo')
                ->whereNotNull('limite')
                ->where('limite', '>', 0)
                ->get();

            foreach ($cartoes as $cartao) {
                // Calcular valor utilizado (soma de lançamentos PENDENTES do cartão)
                $valorUtilizado = Lancamento::where('user_id', $usuario->id)
                    ->where('conta_id', $cartao->id)
                    ->where('tipo_lancamento', 'CARTAO_CREDITO')
                    ->where('status_lancamento', 'PENDENTE')
                    ->sum('valor');

                // Calcular percentual utilizado
                $percentualUtilizado = ($valorUtilizado / $cartao->limite) * 100;

                // Se ultrapassou o percentual de alerta, enviar notificação
                if ($percentualUtilizado >= $percentualAlerta) {
                    $usuario->notify(new LimiteCartaoNotification(
                        $cartao->name,
                        $valorUtilizado,
                        $cartao->limite,
                        round($percentualUtilizado)
                    ));

                    $notificacoesEnviadas++;
                    $this->line("✅ Notificação enviada para {$usuario->email} - Cartão: {$cartao->name} ({$percentualUtilizado}%)");
                }
            }
        }

        $this->info("✅ Total de notificações enviadas: {$notificacoesEnviadas}");
        return Command::SUCCESS;
    }
}
