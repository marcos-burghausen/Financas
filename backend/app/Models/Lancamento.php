<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
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

}
