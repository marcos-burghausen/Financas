<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Traits\UserDataTrait;

class WalletService
{

    use UserDataTrait;

    protected $invoiceService;

    public function __construct(CreditCardInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Salva (cria ou atualiza) uma conta/carteira.
     * Se for um novo cartão de crédito, cria a primeira fatura.
     *
     * @param array $data Os dados validados e transformados da request.
     * @param User $user O usuário autenticado.
     * @return Conta
     */
    public function saveWallet(array $data, User $user): Conta
    {
        $isNewRecord = empty($data['id']);

        $conta = Conta::updateOrCreate(
            ['id' => $data['id'] ?? null, 'user_id' => $user->id],
            $data
        );

        // Se for um NOVO cartão de crédito, crie a fatura para o mês vigente
        if ($isNewRecord && $conta->tipo_conta === 'Cartão de Crédito') {
            $today = Carbon::today();
            // Determina se a fatura vigente é do mês atual ou do próximo
            if ($today->day > $conta->dia_fechamento) {
                $competenciaDate = $today->addMonth();
            } else {
                $competenciaDate = $today;
            }

            // Chama o serviço para criar a fatura
            $this->invoiceService->getOrCreateInvoiceId($conta->id, $competenciaDate, $user->id);
        }

        return $conta;
    }

    public function getWallets(User $user, ?string $mesAno): array
    {
        $wallets = $this->getUserData($user, $mesAno, ['wallets']);



        return $wallets;
    }
}
