<?php

namespace App\Console\Commands;

use App\Models\Lancamento;
use App\Models\User;
use App\Notifications\DesvioOrcamentoNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EnviarNotificacaoDesvioOrcamento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:desvio-orcamento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificações quando categorias ultrapassam o orçamento planejado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Iniciando verificação de desvios de orçamento...');

        // NOTA: Como não existe tabela de orçamentos ainda, 
        // esta implementação é um placeholder que pode ser ativado
        // quando a funcionalidade de orçamentos for implementada

        $this->warn('⚠️  Sistema de orçamentos ainda não implementado.');
        $this->line('Este command será ativado quando a tabela de orçamentos for criada.');

        // Código de exemplo para quando orçamentos forem implementados:
        /*
        $notificacoesEnviadas = 0;

        $usuarios = User::whereHas('notificationSettings', function ($query) {
            $query->where('email_desvio_orcamento', true);
        })->with('notificationSettings')->get();

        $mesAtual = Carbon::now()->format('Y-m');

        foreach ($usuarios as $usuario) {
            // Buscar orçamentos do usuário
            $orcamentos = DB::table('orcamentos')
                ->where('user_id', $usuario->id)
                ->where('mes', $mesAtual)
                ->get();

            foreach ($orcamentos as $orcamento) {
                // Calcular gastos da categoria no mês
                $valorGasto = Lancamento::where('user_id', $usuario->id)
                    ->where('categoria', $orcamento->categoria)
                    ->where('tipo_lancamento', 'DESPESA')
                    ->whereYear('data_lancamento', Carbon::now()->year)
                    ->whereMonth('data_lancamento', Carbon::now()->month)
                    ->sum('valor');

                $percentualGasto = ($valorGasto / $orcamento->valor) * 100;

                // Se ultrapassou 100%, enviar notificação
                if ($percentualGasto > 100) {
                    $usuario->notify(new DesvioOrcamentoNotification(
                        $orcamento->categoria,
                        $orcamento->valor,
                        $valorGasto,
                        round($percentualGasto)
                    ));

                    $notificacoesEnviadas++;
                    $this->line("✅ Notificação enviada para {$usuario->email} - Categoria: {$orcamento->categoria}");
                }
            }
        }

        $this->info("✅ Total de notificações enviadas: {$notificacoesEnviadas}");
        */

        return Command::SUCCESS;
    }
}
