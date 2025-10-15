<?php

namespace App\Notifications;

use App\Models\Lancamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LimiteCartaoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $nomeCartao,
        public float $valorUtilizado,
        public float $limiteTotal,
        public int $percentualUtilizado
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
        $valorFormatado = 'R$ ' . number_format($this->valorUtilizado / 100, 2, ',', '.');
        $limiteFormatado = 'R$ ' . number_format($this->limiteTotal / 100, 2, ',', '.');

        return (new MailMessage)
            ->subject("⚠️ Alerta: {$this->nomeCartao} atingiu {$this->percentualUtilizado}% do limite")
            ->greeting('Olá!')
            ->line("Seu cartão **{$this->nomeCartao}** está próximo do limite.")
            ->line("💳 **Valor utilizado:** {$valorFormatado}")
            ->line("📊 **Limite total:** {$limiteFormatado}")
            ->line("📈 **Percentual utilizado:** {$this->percentualUtilizado}%")
            ->action('Ver Cartões', url('/cartoes'))
            ->line('Considere quitar algumas faturas para liberar o limite.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'nome_cartao' => $this->nomeCartao,
            'valor_utilizado' => $this->valorUtilizado,
            'limite_total' => $this->limiteTotal,
            'percentual_utilizado' => $this->percentualUtilizado,
        ];
    }
}
