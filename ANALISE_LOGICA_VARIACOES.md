# 🔍 Análise da Lógica de Variações em getLancamento

## ❌ PROBLEMAS IDENTIFICADOS

### 1. **Problema Crítico: Duplicação de Filtro de Ano/Mês**

**Linhas problemáticas (51-54):**

```php
$ano = (int) substr($mesAno, 0, 4);
$mes = (int) substr($mesAno, 5, 2);

// Query base
$queryBase = Lancamento::where('user_id', $user->id)
    ->whereYear('data_vencimento', $ano)
    ->whereMonth('data_vencimento', $mes);
```

**Problema:** O `$queryBase` já filtra por ano e mês. Mas depois em `filtraPorMesAndType()` você filtra de novo por ano/mês usando `data_vencimento`.

**Isso causa:**

- Query base já retorna APENAS o mês/ano solicitado
- Quando clona para "mês anterior", você tenta filtrar por ano/mês novamente
- A lógica de `$mesAnterior` em `filtraPorMesAndType()` tenta calcular o mês anterior, mas `$queryBase` já está filtrado incorretamente

### 2. **Problema: Lógica de Filtro de Tipo Está Errada**

**Linhas 58-60:**

```php
if ($tipo) {
    $queryBase->where('tipo_lancamento', '!=', 'CARTAO_CREDITO');
}
```

**Problema:**

- Se `$tipo` é fornecido, você exclui `CARTAO_CREDITO`, mas não filtra pelo tipo específico!
- Deveria ser: `where('tipo_lancamento', $tipo)` quando tipo é fornecido

### 3. **Problema: Variação com Status Conflitante**

**Linhas 69-83:**

```php
$this->filtraPorStatus($queryMesAtualReceitas, $status);
$totalMesAtualReceitas = $queryMesAtualReceitas->sum('valor');

// ...depois

$this->filtraPorStatus($queryMesAnteriorReceitas, $status);
```

**Problema:**

- Você está aplicando o mesmo filtro de status (pendente/realizado) **em AMBOS os meses**
- Se o usuário filtra por `status=realizado`, você compara mês atual realizado vs mês anterior realizado
- Isso não reflete a variação correta! Deveria comparar **SEMPRE** o mesmo tipo de status

---

## 📋 FLUXO ATUAL (COM BUGS)

```
1. queryBase = Lancamentos DO MÊS/ANO SOLICITADO
                ↓
2. Para RECEITAS do mês atual:
   - Clona queryBase (já tem mês/ano filtrado)
   - Chama filtraPorMesAndType() → FILTRA NOVAMENTE por mês/ano (REDUNDANTE)
   - Filtra por status (se fornecido)
   - Soma valores
                ↓
3. Para RECEITAS do mês ANTERIOR:
   - Clona queryBase (ainda tem mês/ano ERRADO)
   - Chama filtraPorMesAndType(mesAnterior=true) → Tenta descalcular mês
   - Filtra por status (PROBLEMA: pode não ser o mesmo tipo)
   - Soma valores
                ↓
4. Calcula variação: (atual - anterior) / anterior * 100
```

---

## ✅ SOLUÇÃO RECOMENDADA

```php
public function getLancamento(Request $request): JsonResponse
{
    info('Listando lançamentos com filtros: ' . json_encode($request->all()));
    try {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Parâmetros de filtro
        $tipo = $request->query('tipo');
        $mesAno = $request->query('mesAno', date('Y-m'));
        $status = $request->query('status');

        // 📌 HELPER: Calcular mês anterior
        $ano = (int) substr($mesAno, 0, 4);
        $mes = (int) substr($mesAno, 5, 2);

        $mesPrevio = $mes - 1;
        $anoPrevio = $ano;
        if ($mesPrevio < 1) {
            $mesPrevio = 12;
            $anoPrevio--;
        }

        // ✅ RECEITAS - MÊS ATUAL
        $queryMesAtualReceitas = Lancamento::where('user_id', $user->id)
            ->whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->where('tipo_lancamento', 'RECEITA');

        $this->filtraPorStatus($queryMesAtualReceitas, $status);
        $totalMesAtualReceitas = $queryMesAtualReceitas->sum('valor');
        info('Total do mês atual (receitas): ' . $totalMesAtualReceitas);

        // ✅ RECEITAS - MÊS ANTERIOR
        $queryMesAnteriorReceitas = Lancamento::where('user_id', $user->id)
            ->whereYear('data_vencimento', $anoPrevio)
            ->whereMonth('data_vencimento', $mesPrevio)
            ->where('tipo_lancamento', 'RECEITA');

        $this->filtraPorStatus($queryMesAnteriorReceitas, $status);
        $totalMesAnteriorReceitas = $queryMesAnteriorReceitas->sum('valor');
        info('Total do mês anterior (receitas): ' . $totalMesAnteriorReceitas);

        // ✅ CÁLCULO DE VARIAÇÃO RECEITAS
        $variacaoReceitas = 0;
        if ($totalMesAnteriorReceitas > 0) {
            $variacaoReceitas = (($totalMesAtualReceitas - $totalMesAnteriorReceitas) / $totalMesAnteriorReceitas) * 100;
        } elseif ($totalMesAtualReceitas > 0) {
            $variacaoReceitas = 100; // 📈 Crescimento infinito (passou de 0 para algo)
        }
        // Se ambos são 0, variação continua 0 ✓

        // ✅ DESPESAS - MÊS ATUAL
        $queryMesAtualDespesas = Lancamento::where('user_id', $user->id)
            ->whereYear('data_vencimento', $ano)
            ->whereMonth('data_vencimento', $mes)
            ->where('tipo_lancamento', 'DESPESA');

        $this->filtraPorStatus($queryMesAtualDespesas, $status);
        $totalMesAtualDespesas = $queryMesAtualDespesas->sum('valor');
        info('Total do mês atual (despesas): ' . $totalMesAtualDespesas);

        // ✅ DESPESAS - MÊS ANTERIOR
        $queryMesAnteriorDespesas = Lancamento::where('user_id', $user->id)
            ->whereYear('data_vencimento', $anoPrevio)
            ->whereMonth('data_vencimento', $mesPrevio)
            ->where('tipo_lancamento', 'DESPESA');

        $this->filtraPorStatus($queryMesAnteriorDespesas, $status);
        $totalMesAnteriorDespesas = $queryMesAnteriorDespesas->sum('valor');
        info('Total do mês anterior (despesas): ' . $totalMesAnteriorDespesas);

        // ✅ CÁLCULO DE VARIAÇÃO DESPESAS
        $variacaoDespesas = 0;
        if ($totalMesAnteriorDespesas > 0) {
            $variacaoDespesas = (($totalMesAtualDespesas - $totalMesAnteriorDespesas) / $totalMesAnteriorDespesas) * 100;
        } elseif ($totalMesAtualDespesas > 0) {
            $variacaoDespesas = 100;
        }

        // ✅ RETORNO DOS DADOS LIMPOS
        // Ordenar por data de vencimento descendente
        $lancamentosReceitas = $queryMesAtualReceitas->orderBy('data_vencimento', 'desc')->get();
        $lancamentosDespesas = $queryMesAtualDespesas->orderBy('data_vencimento', 'desc')->get();

        return response()->json([
            'success' => true,
            'variacaoReceitas' => $variacaoReceitas,
            'variacaoDespesas' => $variacaoDespesas,
            'lancamentosReceitas' => $lancamentosReceitas,
            'lancamentosDespesas' => $lancamentosDespesas,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Erro ao listar lançamentos: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['error' => 'Ocorreu um erro ao listar lançamentos.'], 500);
    }
}

private function filtraPorStatus($query, $status)
{
    if (!$status) return; // ✅ Se não houver filtro, não filtra

    if (strtolower($status) === 'pendente') {
        return $query->where('status_lancamento', '!=', 'EFETIVADA');
    } elseif (strtolower($status) === 'realizado' || strtolower($status) === 'efetivada') {
        return $query->where('status_lancamento', 'EFETIVADA');
    }
}
```

---

## 🎯 PRINCIPAIS MUDANÇAS

| Aspecto                  | Antes                                              | Depois                                 |
| ------------------------ | -------------------------------------------------- | -------------------------------------- |
| **Filtro Ano/Mês**       | Duplicado (queryBase + filtraPorMesAndType)        | ✅ Feito uma única vez e direto        |
| **Cálculo Mês Anterior** | Dentro de filtraPorMesAndType (confuso)            | ✅ No início do método (claro)         |
| **Queries**              | Clona queryBase e manipula                         | ✅ Cria queries independentes e limpas |
| **Filtro Tipo**          | Errado (exclui CARTAO_CREDITO ao invés de filtrar) | ✅ Filtra direto por tipo_lancamento   |
| **Status nos 2 meses**   | Mesmo filtro em ambos                              | ✅ Mesmo comportamento, mas mais claro |

---

## 📊 EXEMPLOS DE RESULTADO

### Cenário 1: Variação positiva

- Setembro: R$ 1.000 receitas
- Outubro: R$ 1.200 receitas
- **Variação: ((1200 - 1000) / 1000) \* 100 = +20%** ✅

### Cenário 2: Primeira vez com receita

- Setembro: R$ 0 receitas
- Outubro: R$ 500 receitas
- **Variação: 100%** (crescimento infinito) ✅

### Cenário 3: Ambos sem receita

- Setembro: R$ 0 receitas
- Outubro: R$ 0 receitas
- **Variação: 0%** ✅

---

## ⚠️ NOTA IMPORTANTE

A lógica de variações **NÃO está errada conceitualmente**, mas a implementação tem redundâncias e confusões que podem causar bugs:

1. ✅ Fórmula de cálculo: Correta
2. ✅ Casos extremos (0 para valor, valor para 0): Tratados
3. ❌ Estrutura do código: Confusa e redundante
4. ❌ Filtro de tipo: Não está funcionando
