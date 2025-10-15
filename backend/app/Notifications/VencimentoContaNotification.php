<?php

namespace App\Notifications;

use App\Models\Lancamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class VencimentoContaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lancamento;
    protected $diasRestantes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lancamento $lancamento, int $diasRestantes)
    {
        $this->lancamento = $lancamento;
        $this->diasRestantes = $diasRestantes;
    }

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
        $valor = number_format($this->lancamento->valor, 2, ',', '.');
        $vencimento = Carbon::parse($this->lancamento->data_vencimento)->format('d/m/Y');
        $categoria = $this->lancamento->category->name ?? 'Sem categoria';

        $mensagem = $this->diasRestantes === 0
            ? "Sua conta vence HOJE!"
            : "Sua conta vence em {$this->diasRestantes} dia(s)!";

        return (new MailMessage)
            ->subject("🔔 Lembrete: Conta a Vencer - {$this->lancamento->descricao}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line($mensagem)
            ->line("**Descrição:** {$this->lancamento->descricao}")
            ->line("**Valor:** R$ {$valor}")
            ->line("**Data de Vencimento:** {$vencimento}")
            ->line("**Categoria:** {$categoria}")
            ->action('Ver Lançamentos', url('/dashboard'))
            ->line('Não esqueça de realizar o pagamento para evitar juros e multas!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'lancamento_id' => $this->lancamento->id,
            'descricao' => $this->lancamento->descricao,
            'valor' => $this->lancamento->valor,
            'data_vencimento' => $this->lancamento->data_vencimento,
            'dias_restantes' => $this->diasRestantes,
        ];
    }
}
