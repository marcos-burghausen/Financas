<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Launch extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $casts = [
        'is_refund' => 'boolean'
    ];

    protected $fillable = [
        'user_id',
        'installment_group_id',
        'invoice_id',
        'description',
        'value',
        'launch_type',
        'is_refund',
        'original_launch_id',
        'recurrence',
        'qtd_installments',
        'num_installment',
        'installment_type',
        'periodicity',
        'due_date',
        'launch_status',
        'category',
        'subcategory',
        'observations',
        'launch_date',
        'effective_date',
        'account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(CreditCardInvoice::class, 'invoice_id');
    }

    public function accountModel() // Renomeado para não conflitar com a coluna 'conta'
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function original() // Relacionamento para o estorno
    {
        return $this->belongsTo(Launch::class, 'original_launch_id');
    }
}
