<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $filable = [
        'user_id',
        'descricao',
        'valor',
        'tipo',
        'numParcelas',
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
