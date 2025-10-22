<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
        'balance',
        'include_in_initial_sum',
        'description',
        'account_type',
        'account_status',
        'limit',
        'closing_day',
        'due_day',
        'parent_account_id',
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
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    /**
     * Define o relacionamento para as contas filhas.
     * Uma conta principal pode ter vários cartões de crédito (contas filhas) vinculados.
     */
    public function childAccounts()
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }
}
