<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'revenue_id',
        'parcela',
        'totalParcelas',
        'valor',
        'dataVencimento',
        'dataLancamento',
        'dataEfetivacao',
        'taxaMulta',
        'valorPago',
        'status',
    ];

    public function lancamento()
    {
        return $this->belongsTo(Lancamento::class, 'lancamento_id');
    }
}
