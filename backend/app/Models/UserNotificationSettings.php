<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSettings extends Model
{
    protected $fillable = [
        'user_id',
        'email_vencimento',
        'email_limite_cartao',
        'email_estorno',
        'email_desvio_orcamento',
        'dias_antecedencia_vencimento',
        'percentual_alerta_cartao',
        'receber_resumo_mensal',
        'horario_preferido',
    ];

    protected $casts = [
        'email_vencimento' => 'boolean',
        'email_limite_cartao' => 'boolean',
        'email_estorno' => 'boolean',
        'email_desvio_orcamento' => 'boolean',
        'receber_resumo_mensal' => 'boolean',
        'dias_antecedencia_vencimento' => 'integer',
        'percentual_alerta_cartao' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Criar configurações padrão para um usuário
     */
    public static function createDefault(int $userId): self
    {
        return self::create(['user_id' => $userId]);
    }
}
