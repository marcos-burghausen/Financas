<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manutencao extends Model
{
    protected $table = 'manutencoes';

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

    protected $appends = ['oficina', 'veiculoId'];

    protected $casts = [
        'data' => 'date',
        'quilometragem' => 'integer',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Get the oficina data as an object
     */
    public function getOficinaAttribute()
    {
        return (object) [
            'nome' => $this->oficina_nome,
            'telefone' => $this->oficina_telefone,
            'email' => $this->oficina_email,
            'endereco' => $this->oficina_endereco,
        ];
    }

    /**
     * Get the veiculo_id as veiculoId (camelCase for frontend)
     */
    public function getVeiculoIdAttribute()
    {
        return $this->attributes['veiculo_id'] ?? null;
    }

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
