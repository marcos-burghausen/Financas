<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veiculo extends Model
{
    protected $fillable = [
        'user_id',
        'placa',
        'marca',
        'modelo',
        'ano',
        'cor',
        'quilometragem',
        'combustivel',
        'proximaManutencao',
        'status',
    ];

    protected $casts = [
        'ano' => 'integer',
        'quilometragem' => 'integer',
        'proximaManutencao' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Manutenções
     */
    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class);
    }
}
