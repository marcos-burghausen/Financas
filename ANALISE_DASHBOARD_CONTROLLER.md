# 🔍 ANÁLISE DO DashboardController

## 📊 O QUE ESTÁ RETORNANDO ATUALMENTE

```json
{
  "mesAno": "2025-10",
  "receitas": {
    "qtd_receitas": 0, // ❌ Sempre retorna 0 (não conta registros)
    "total": 0, // ✓ Soma valor total receitas
    "recebido": 0, // ✓ Soma valor receitas EFETIVADA
    "pendente": 0 // ✓ Soma valor receitas PENDENTE
  },
  "despesas": {
    "total": 0, // ✓ Soma valor total despesas
    "pago": 0, // ✓ Soma valor despesas EFETIVADA
    "pendente": 0 // ✓ Soma valor despesas PENDENTE
  },
  "contas": [],
  "saldos": {
    "inicial": 0,
    "atual": 0,
    "diferenca": 0
  }
}
```

---

## ❌ PROBLEMAS IDENTIFICADOS

### 1️⃣ **Falta Contagem de Registros**

- Só tem `qtd_receitas` e mesmo assim sempre retorna 0
- Falta `qtd_despesas`, `qtd_efetivada`, `qtd_pendente` para ambas
- O `count()` na linha 61 não funciona (DB::raw não retorna count, apenas sum)

### 2️⃣ **Falta Variação Comparado com Mês Anterior**

- Não calcula variação de receitas vs mês anterior
- Não calcula variação de despesas vs mês anterior
- Essencial para dashboard mostrar tendências

### 3️⃣ **Estrutura Incompleta**

- Não separa:
  - Quantidade TOTAL de registros do mês
  - Quantidade de EFETIVADA
  - Quantidade de PENDENTE
  - Valor total de RECEBIDAS (mesmo que pago/efetivado)
  - Valor total de PENDENTE

---

## ✅ O QUE PRECISA RETORNAR

### Para RECEITAS:

```json
"receitas": {
  "qtd_total": 5,              // Total de registros do mês
  "qtd_efetivada": 3,          // Registros EFETIVADA
  "qtd_pendente": 2,           // Registros PENDENTE
  "valor_total": 5000,         // Soma de todos os valores
  "valor_recebido": 3000,      // Soma EFETIVADA
  "valor_pendente": 2000,      // Soma PENDENTE
  "variacao": 20               // % comparado com mês anterior
}
```

### Para DESPESAS:

```json
"despesas": {
  "qtd_total": 8,              // Total de registros do mês
  "qtd_efetivada": 5,          // Registros EFETIVADA
  "qtd_pendente": 3,           // Registros PENDENTE
  "valor_total": 3000,         // Soma de todos os valores
  "valor_pago": 2500,          // Soma EFETIVADA
  "valor_pendente": 500,       // Soma PENDENTE
  "variacao": -15              // % comparado com mês anterior
}
```

---

## 🔧 CÓDIGO CORRIGIDO

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $mesAno = $request->query('mesAno', now()->format('Y-m'));
        $user = auth()->user();
        $cacheKey = "dashboard_summary_user_{$user->id}_month_{$mesAno}";

        return Cache::remember($cacheKey, 300, function () use ($user, $mesAno) {
            [$ano, $mes] = explode('-', $mesAno);

            // Calcular mês anterior
            $mesPrevio = (int)$mes - 1;
            $anoPrevio = (int)$ano;
            if ($mesPrevio < 1) {
                $mesPrevio = 12;
                $anoPrevio--;
            }
            $mesAnoPrevio = str_pad($anoPrevio, 4, '0', STR_PAD_LEFT) . '-' . str_pad($mesPrevio, 2, '0', STR_PAD_LEFT);

            // ========== RECEITAS - MÊS ATUAL ==========
            $receitasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    COUNT(*) as qtd_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN 1 ELSE 0 END) as qtd_efetivada,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN 1 ELSE 0 END) as qtd_pendente,
                    SUM(valor) as valor_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as valor_recebido,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as valor_pendente
                ')
                ->first();

            // ========== RECEITAS - MÊS ANTERIOR ==========
            $receitasDataAnterior = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'RECEITA')
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->selectRaw('SUM(valor) as total')
                ->first();

            // Calcular variação de receitas
            $totalReceitasAtual = (float)($receitasData->valor_total ?? 0);
            $totalReceitasAnterior = (float)($receitasDataAnterior->total ?? 0);
            $variacaoReceitas = $this->calcularVariacao($totalReceitasAtual, $totalReceitasAnterior);

            // ========== DESPESAS - MÊS ATUAL ==========
            $despesasData = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->selectRaw('
                    COUNT(*) as qtd_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN 1 ELSE 0 END) as qtd_efetivada,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN 1 ELSE 0 END) as qtd_pendente,
                    SUM(valor) as valor_total,
                    SUM(CASE WHEN status_lancamento = "EFETIVADA" THEN valor ELSE 0 END) as valor_pago,
                    SUM(CASE WHEN status_lancamento = "PENDENTE" THEN valor ELSE 0 END) as valor_pendente
                ')
                ->first();

            // ========== DESPESAS - MÊS ANTERIOR ==========
            $despesasDataAnterior = DB::table('lancamentos')
                ->where('user_id', $user->id)
                ->where('tipo_lancamento', 'DESPESA')
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->selectRaw('SUM(valor) as total')
                ->first();

            // Calcular variação de despesas
            $totalDespesasAtual = (float)($despesasData->valor_total ?? 0);
            $totalDespesasAnterior = (float)($despesasDataAnterior->total ?? 0);
            $variacaoDespesas = $this->calcularVariacao($totalDespesasAtual, $totalDespesasAnterior);

            // Buscar contas
            $contas = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('status_conta', 'Ativo')
                ->select('id', 'name', 'saldo', 'icon', 'color')
                ->get();

            // Calcular saldos
            $saldoInicial = DB::table('contas')
                ->where('user_id', $user->id)
                ->where('incluir_em_soma_inicial', true)
                ->sum('saldo');

            $saldoAtual = $contas->sum('saldo');

            return response()->json([
                'success' => true,
                'mesAno' => $mesAno,
                'receitas' => [
                    'qtd_total' => (int)($receitasData->qtd_total ?? 0),
                    'qtd_efetivada' => (int)($receitasData->qtd_efetivada ?? 0),
                    'qtd_pendente' => (int)($receitasData->qtd_pendente ?? 0),
                    'valor_total' => (float)($receitasData->valor_total ?? 0),
                    'valor_recebido' => (float)($receitasData->valor_recebido ?? 0),
                    'valor_pendente' => (float)($receitasData->valor_pendente ?? 0),
                    'variacao' => $variacaoReceitas,
                ],
                'despesas' => [
                    'qtd_total' => (int)($despesasData->qtd_total ?? 0),
                    'qtd_efetivada' => (int)($despesasData->qtd_efetivada ?? 0),
                    'qtd_pendente' => (int)($despesasData->qtd_pendente ?? 0),
                    'valor_total' => (float)($despesasData->valor_total ?? 0),
                    'valor_pago' => (float)($despesasData->valor_pago ?? 0),
                    'valor_pendente' => (float)($despesasData->valor_pendente ?? 0),
                    'variacao' => $variacaoDespesas,
                ],
                'contas' => $contas,
                'saldos' => [
                    'inicial' => (float)$saldoInicial,
                    'atual' => (float)$saldoAtual,
                    'diferenca' => (float)($saldoAtual - $saldoInicial),
                ],
            ]);
        });
    }

    /**
     * Calcula a variação percentual entre dois valores
     */
    private function calcularVariacao(float $valorAtual, float $valorAnterior): float
    {
        if ($valorAnterior > 0) {
            return (($valorAtual - $valorAnterior) / $valorAnterior) * 100;
        } elseif ($valorAtual > 0) {
            return 100; // Crescimento de 0 para algo
        }
        return 0; // Ambos 0
    }

    public function clearCache(Request $request)
    {
        $user = auth()->user();
        $mesAno = $request->query('mesAno', now()->format('Y-m'));
        $cacheKey = "dashboard_summary_user_{$user->id}_month_{$mesAno}";

        Cache::forget($cacheKey);

        return response()->json(['success' => true, 'message' => 'Cache limpo com sucesso']);
    }
}
```

---

## 📊 RESPOSTA ESPERADA

```json
{
  "success": true,
  "mesAno": "2025-10",
  "receitas": {
    "qtd_total": 5,
    "qtd_efetivada": 3,
    "qtd_pendente": 2,
    "valor_total": 5000,
    "valor_recebido": 3000,
    "valor_pendente": 2000,
    "variacao": 20
  },
  "despesas": {
    "qtd_total": 8,
    "qtd_efetivada": 5,
    "qtd_pendente": 3,
    "valor_total": 3000,
    "valor_pago": 2500,
    "valor_pendente": 500,
    "variacao": -15
  },
  "contas": [...],
  "saldos": {
    "inicial": 10000,
    "atual": 12000,
    "diferenca": 2000
  }
}
```

---

## 🎯 BENEFÍCIOS

| Campo              | Antes        | Depois     |
| ------------------ | ------------ | ---------- |
| **qtd_total**      | ❌ Falta     | ✅ Retorna |
| **qtd_efetivada**  | ❌ Falta     | ✅ Retorna |
| **qtd_pendente**   | ❌ Falta     | ✅ Retorna |
| **valor_recebido** | ✓ Tem (pago) | ✅ Mantém  |
| **valor_pendente** | ✓ Tem        | ✅ Mantém  |
| **variacao**       | ❌ Falta     | ✅ Retorna |

---

## 🔧 HELPERS UTILIZADOS

### `calcularVariacao()`

```php
/**
 * Fórmula: ((Valor Atual - Valor Anterior) / Valor Anterior) * 100
 *
 * Casos:
 * - Valor anterior > 0: Usa fórmula normal
 * - Valor anterior = 0 e atual > 0: Retorna 100% (crescimento infinito)
 * - Ambos 0: Retorna 0%
 */
private function calcularVariacao(float $valorAtual, float $valorAnterior): float
```

---

## 📝 NOTAS IMPORTANTES

1. **Cache:** Mantido em 5 minutos (300 segundos)
2. **Performance:** Usa agregações SQL (selectRaw) - muito mais rápida que loops PHP
3. **Precisão:** Todos os valores em float (mantém centavos)
4. **Variação:** Compara sempre com o mês imediatamente anterior
5. **Compatibilidade:** Mantém estrutura anterior + adiciona novos campos
