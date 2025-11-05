<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'categoria',
        'valor_orcado',
        'mes_ano',
        'observacao',
    ];

    protected $casts = [
        'valor_orcado' => 'integer',
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por usuário e mês
     */
    public function scopeForUserAndMonth(Builder $query, int $userId, string $mesAno): Builder
    {
        return $query->where('user_id', $userId)->where('mes_ano', $mesAno);
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopeForCategory(Builder $query, string $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Obtém o gasto total de uma categoria no mês
     * Integra com as transações (Lancamento)
     */
    public function getGastoRealAttribute(): int
    {
        $ano = (int) substr($this->mes_ano, 0, 4);
        $mes = (int) substr($this->mes_ano, 5, 2);

        return Lancamento::where('user_id', $this->user_id)
            ->where('categoria', $this->categoria)
            ->where('tipo_lancamento', 'DESPESA')
            ->whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->where('status_lancamento', 'EFETIVADA')
            ->sum('valor');
    }

    /**
     * Calcula o saldo restante (orçado - gasto)
     */
    public function getSaldoRestanteAttribute(): int
    {
        return $this->valor_orcado - $this->gasto_real;
    }

    /**
     * Calcula o percentual gasto
     */
    public function getPercentualGastoAttribute(): float
    {
        if ($this->valor_orcado == 0) {
            return 0;
        }

        return round(($this->gasto_real / $this->valor_orcado) * 100, 2);
    }

    /**
     * Determina o status do orçamento baseado no percentual gasto
     */
    public function getStatusAttribute(): string
    {
        $percentual = $this->percentual_gasto;

        if ($percentual >= 100) {
            return 'excedido';
        } elseif ($percentual >= 80) {
            return 'alerta';
        } else {
            return 'normal';
        }
    }

    /**
     * Obtém as transações relacionadas a esta categoria no mês
     */
    public function getTransacoesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        $ano = (int) substr($this->mes_ano, 0, 4);
        $mes = (int) substr($this->mes_ano, 5, 2);

        return Lancamento::where('user_id', $this->user_id)
            ->where('categoria', $this->categoria)
            ->where('tipo_lancamento', 'DESPESA')
            ->whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->where('status_lancamento', 'EFETIVADA')
            ->orderBy('data_vencimento', 'desc')
            ->get(['id', 'descricao', 'valor', 'data_vencimento']);
    }

    /**
     * Formata o valor orçado para exibição
     */
    public function getValorOrcadoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_orcado / 100, 2, ',', '.');
    }

    /**
     * Formata o gasto real para exibição
     */
    public function getGastoRealFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->gasto_real / 100, 2, ',', '.');
    }

    /**
     * Formata o saldo restante para exibição
     */
    public function getSaldoRestanteFormatadoAttribute(): string
    {
        $saldo = $this->saldo_restante;
        $prefixo = $saldo < 0 ? '-R$ ' : 'R$ ';
        return $prefixo . number_format(abs($saldo) / 100, 2, ',', '.');
    }
}
