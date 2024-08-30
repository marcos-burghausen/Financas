<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Models\Expense;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyUpcomingExpensesAndRevenues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-expenses-and-revenues';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica usuários sobre despesas e receitas que vencem amanhã.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow();

        info('O comando foi iniciado em ' . Carbon::now());


        // Enviar notificações de despesas
        $expenses = Expense::whereDate('date', $tomorrow)->get();
        foreach ($expenses as $expense) {
            Mail::to($expense->user->email)->queue(new NotificationMail(
                $expense->user,
                'Vencimento de Despesa',
                'Despesa',
                $expense->descricao
            ));
        }

        // Enviar notificações de receitas
        $revenues = Revenue::whereDate('date', $tomorrow)->get();
        foreach ($revenues as $revenue) {
            Mail::to($revenue->user->email)->queue(new NotificationMail(
                $revenue->user,
                'Vencimento de Receita',
                'Receita',
                $revenue->descricao
            ));
        }

        $this->info('Notificações de vencimento enviadas com sucesso.');
    }
}
