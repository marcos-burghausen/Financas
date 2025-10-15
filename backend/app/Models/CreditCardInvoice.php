<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCardInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id',
        'competencia',
        'data_fechamento',
        'data_vencimento',
        'status_fatura',
        'total_fatura',
        'valor_pago',
        'encargos',
        'pago_em',
        'lancamento_pagamento_id'
    ];

    /**
     * Adicionado o tipo de dados para os campos de data.
     */
    protected $casts = [
        'data_fechamento' => 'date',
        'data_vencimento' => 'date',
    ];


    /**
     * Define o relacionamento com a Conta (o cartão de crédito).
     */
    public function conta()
    {
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    /**
     * CORRIGIDO: Adicionado o relacionamento com os lançamentos que compõem a fatura.
     * Uma fatura (Invoice) tem muitos lançamentos (Lancamentos).
     */
    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class, 'invoice_id');
    }

    /**
     * Calcula e atualiza o valor total da fatura.
     * Soma todos os lançamentos associados a esta fatura.
     */
    public function recalculateTotals(): void
    {
        // Soma o valor de todos os lançamentos vinculados (compras - estornos)
        $total = $this->lancamentos()->where('is_estorno', false)->sum('valor');
        $totalEstornos = $this->lancamentos()->where('is_estorno', true)->sum('valor');

        // Atualiza o campo total_fatura e salva no banco de dados
        $this->total_fatura = $total - $totalEstornos;
        $this->save();
    }
}
