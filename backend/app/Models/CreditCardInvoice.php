<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCardInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'competence',
        'closing_date',
        'due_date',
        'status_invoice',
        'total_invoice',
        'value_pay',
        'charges',
        'pay_in',
        'launch_payment_id'
    ];

    /**
     * Adicionado o tipo de dados para os campos de data.
     */
    protected $casts = [
        'closing_date' => 'date',
        'due_date' => 'date',
    ];


    /**
     * Define o relacionamento com a Conta (o cartão de crédito).
     */
    public function accountCreditCard()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * CORRIGIDO: Adicionado o relacionamento com os lançamentos que compõem a fatura.
     * Uma fatura (Invoice) tem muitos lançamentos (Lancamentos).
     */
    public function launches()
    {
        return $this->hasMany(Launch::class, 'invoice_id');
    }

    /**
     * Calcula e atualiza o valor total da fatura.
     * Soma todos os lançamentos associados a esta fatura.
     */
    public function recalculateTotals(): void
    {
        // Soma o valor de todos os lançamentos vinculados (compras - estornos)
        $total = $this->launches()->where('is_refund', false)->sum('value');
        $totalRefunds = $this->launches()->where('is_refund', true)->sum('value');

        // Atualiza o campo total_fatura e salva no banco de dados
        $this->total_invoice = $total - $totalRefunds;
        $this->save();
    }
}
