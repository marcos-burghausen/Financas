<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $casts = [
        'is_estorno' => 'boolean'
    ];

    protected $fillable = [
        'user_id',
        'installment_group_id',
        'descricao',
        'valor',
        'tipo',
        'recorrencia',
        'numParcelas',
        'parcelaAtual',
        'tipoParcela',
        'periodicidade',
        'dataVencimento',
        'status',
        'categoria',
        'subcategoria',
        'dataLancamento',
        'dataEfetivacao',
        'conta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(CreditCardInvoice::class, 'invoice_id');
    }

    public function contaModel()
    {
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function original()
    {
        return $this->belongsTo(Lancamento::class, 'original_lancamento_id');
    }
}
