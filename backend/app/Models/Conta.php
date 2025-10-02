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
        'color',
        'bandeira',
        'saldo',
        'saldo_inicial',
        'incluir_em_soma_inicial',
        'descricao',
        'tipo_conta',
        'status_conta',
        'limite',
        'dia_fechamento',
        'dia_vencimento',
        'conta_pai_id',
    ];

    /**
     * Define o relacionamento com o usuário.
     */
    public function user() // Nome da função alterado para seguir a convenção do Laravel (user ao invés de users)
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define o relacionamento para a conta principal (pai).
     * Um cartão de crédito pertence a uma conta principal.
     */
    public function parentAccount()
    {
        return $this->belongsTo(Conta::class, 'conta_pai_id');
    }

    /**
     * Define o relacionamento para as contas filhas.
     * Uma conta principal pode ter vários cartões de crédito (contas filhas) vinculados.
     */
    public function childAccounts()
    {
        return $this->hasMany(Conta::class, 'conta_pai_id');
    }
}
