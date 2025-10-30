<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $action;
    public $itemType;
    public $itemName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $action, $itemType, $itemName)
    {
        $this->user = $user;
        $this->action = $action;
        $this->itemType = $itemType;
        $this->itemName = $itemName;

        Log::info('📧 NotificationMail criado', [
            'user_email' => $user->email ?? 'N/A',
            'user_name' => $user->name ?? 'N/A',
            'action' => $action,
            'itemType' => $itemType,
            'itemName' => $itemName
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificação de Ação',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mails.notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Falha no envio de NotificationMail', [
            'user_email' => $this->user->email ?? 'N/A',
            'user_name' => $this->user->name ?? 'N/A',
            'action' => $this->action,
            'itemType' => $this->itemType,
            'itemName' => $this->itemName,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
