<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:notify-upcoming-expenses-and-revenues')->dailyAt('12:00');

        // Notificação de vencimento de contas - executar às 9h todos os dias
        $schedule->command('notifications:vencimento')->dailyAt('09:00');

        // Notificação de limite de cartão - executar às 10h todos os dias
        $schedule->command('notifications:limite-cartao')->dailyAt('10:00');

        // Notificação de desvio de orçamento - executar às 20h todos os dias
        $schedule->command('notifications:desvio-orcamento')->dailyAt('20:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
