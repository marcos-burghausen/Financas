<?php

namespace App\Notifications;

use App\Models\Lancamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstornoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Lancamento $lancamentoEstorno,
        public Lancamento $lancamentoOriginal
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $valorFormatado = 'R$ ' . number_format($this->lancamentoEstorno->valor / 100, 2, ',', '.');
        $dataEstorno = \Carbon\Carbon::parse($this->lancamentoEstorno->data_lancamento)->format('d/m/Y');

        return (new MailMessage)
            ->subject("🔄 Estorno Registrado - {$this->lancamentoOriginal->descricao}")
            ->greeting('Olá!')
            ->line("Um estorno foi registrado no sistema.")
            ->line("📌 **Lançamento original:** {$this->lancamentoOriginal->descricao}")
            ->line("💰 **Valor estornado:** {$valorFormatado}")
            ->line("📅 **Data do estorno:** {$dataEstorno}")
            ->line("📂 **Categoria:** {$this->lancamentoEstorno->categoria}")
            ->action('Ver Lançamentos', url('/lancamentos'))
            ->line('Este é um registro automático de estorno.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'lancamento_estorno_id' => $this->lancamentoEstorno->id,
            'lancamento_original_id' => $this->lancamentoOriginal->id,
            'valor' => $this->lancamentoEstorno->valor,
            'descricao_original' => $this->lancamentoOriginal->descricao,
        ];
    }
}
