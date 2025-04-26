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
        'tipo',
        'status',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
