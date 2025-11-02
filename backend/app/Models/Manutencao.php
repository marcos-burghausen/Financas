<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manutencao extends Model
{
    protected $fillable = [
        'veiculo_id',
        'tipo',
        'data',
        'quilometragem',
        'valor_total',
        'oficina_nome',
        'oficina_telefone',
        'oficina_email',
        'oficina_endereco',
        'observacoes',
    ];

    protected $casts = [
        'data' => 'date',
        'quilometragem' => 'integer',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Relacionamento com Veiculo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    /**
     * Relacionamento com Itens
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ManutencaoItem::class);
    }
}
