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

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    /**
     * Relacionamento com os lançamentos que compõem a fatura.
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
