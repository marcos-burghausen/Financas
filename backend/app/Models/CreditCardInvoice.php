<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCardInvoice extends Model
{
    protected $fillable = [
        'conta_id',
        'competencia',
        'data_fechamento',
        'data_vencimento',
        'status',
        'total_em_centavos',
        'paid_at',
        'payment_lancamento_id'
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class, 'invoice_id');
    }
}
