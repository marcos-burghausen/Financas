<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManutencaoItem extends Model
{
    protected $table = 'manutencao_itens';

    protected $fillable = [
        'manutencao_id',
        'nome',
        'descricao',
        'quantidade',
        'valor_unitario',
        'valor_total',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Relacionamento com Manutencao
     */
    public function manutencao(): BelongsTo
    {
        return $this->belongsTo(Manutencao::class);
    }
}
