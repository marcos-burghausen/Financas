<?php

namespace App\Services;

use App\Models\CreditCardInvoice;
use App\Models\Conta;
use Carbon\Carbon;

class CreditCardInvoiceService
{
    /**
     * Encontra ou cria uma fatura com base no cartão e na string de data.
     *
     * @param int $cardId O ID da conta do tipo 'Cartão de Crédito'.
     * @param Carbon $date O objeto de data para a competência.
     * @param int $userId O ID do usuário logado.
     * @return int|null O ID da fatura.
     */
    public function getOrCreateInvoiceId(int $cardId, Carbon $date, int $userId): ?int
    {
        $card = Conta::where('id', $cardId)->where('user_id', $userId)->firstOrFail();

        $competencia = $date->format('Y-m');

        // Procura por uma fatura existente ou cria uma nova
        $invoice = CreditCardInvoice::firstOrCreate(
            [
                'conta_id' => $cardId,
                'competencia' => $competencia,
            ],
            [
                'data_vencimento' => (clone $date)->day($card->dia_vencimento),
                'data_fechamento' => (clone $date)->day($card->dia_fechamento),
                'total_fatura' => 0,
                'valor_pago' => 0,
            ]
        );

        return $invoice->id;
    }
}
