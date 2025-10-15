<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DesvioOrcamentoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $categoria,
        public float $orcamento,
        public float $valorGasto,
        public int $percentualGasto
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
        $orcamentoFormatado = 'R$ ' . number_format($this->orcamento / 100, 2, ',', '.');
        $gastoFormatado = 'R$ ' . number_format($this->valorGasto / 100, 2, ',', '.');
        $excesso = $this->valorGasto - $this->orcamento;
        $excessoFormatado = 'R$ ' . number_format($excesso / 100, 2, ',', '.');

        return (new MailMessage)
            ->subject("⚠️ Orçamento Ultrapassado - {$this->categoria}")
            ->greeting('Olá!')
            ->line("A categoria **{$this->categoria}** ultrapassou o orçamento planejado.")
            ->line("💰 **Orçamento planejado:** {$orcamentoFormatado}")
            ->line("📊 **Valor gasto:** {$gastoFormatado}")
            ->line("📈 **Percentual gasto:** {$this->percentualGasto}%")
            ->line("🔴 **Excedente:** {$excessoFormatado}")
            ->action('Ver Orçamentos', url('/orcamentos'))
            ->line('Revise seus gastos nesta categoria para manter o controle financeiro.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'categoria' => $this->categoria,
            'orcamento' => $this->orcamento,
            'valor_gasto' => $this->valorGasto,
            'percentual_gasto' => $this->percentualGasto,
        ];
    }
}
