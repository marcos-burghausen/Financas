<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'saldo',
        'saldoInicial',
        'incluirEmSomaInicial',
        'descricao',
        'tipoConta',
        'status',
        'bandeira',
        'limite',
        'dia_fechamento',
        'dia_vencimento',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
