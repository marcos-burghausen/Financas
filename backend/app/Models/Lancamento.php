<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $casts = [
        'is_estorno' => 'boolean',
        'data_vencimento' => 'date',
        'data_lancamento' => 'date',
        'data_efetivacao' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'installment_group_id',
        'invoice_id',
        'descricao',
        'valor',
        'tipo_lancamento',
        'is_estorno',
        'original_lancamento_id',
        'recorrencia',
        'qtd_parcelas',
        'num_parcela',
        'tipo_parcela',
        'periodicidade',
        'data_vencimento',
        'status_lancamento',
        'categoria',
        'subcategoria',
        'observacoes',
        'data_lancamento',
        'data_efetivacao',
        'conta_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(CreditCardInvoice::class, 'invoice_id');
    }

    public function contaModel() // Renomeado para não conflitar com a coluna 'conta'
    {
        return $this->belongsTo(Conta::class, 'conta_id');
    }

    public function original() // Relacionamento para o estorno
    {
        return $this->belongsTo(Lancamento::class, 'original_lancamento_id');
    }
}
