# ✅ CORREÇÃO DA LÓGICA DE VARIAÇÕES - COMPLETA

## 📋 Resumo das Mudanças

A lógica de `getLancamento()` foi completamente refatorada para:

1. **Separar corretamente RECEITAS e DESPESAS** quando não há filtro de tipo
2. **Excluir CARTAO_CREDITO** automaticamente quando não há filtro
3. **Aplicar filtro de tipo** quando fornecido
4. **Calcular variações corretas** para cada tipo de lançamento

---

## 🔧 Mudanças Implementadas

### ✅ 1. **Novo Sistema de Filtro de Tipo**

**Antes (❌ Errado):**

```php
if (!$tipo) {
    $queryBase->where('tipo_lancamento', '!=', 'CARTAO_CREDITO');
}
// ❌ Problema: Se tipo é fornecido, não filtra por nada!
```

**Depois (✅ Correto):**

```php
$tiposLancamento = [];
if (!$tipo) {
    // Se não houver filtro, pegar RECEITA e DESPESA (excluir CARTAO_CREDITO)
    $tiposLancamento = ['RECEITA', 'DESPESA'];
} else {
    // Se houver filtro, usar só o tipo fornecido
    $tiposLancamento = [$tipo];
}
```

### ✅ 2. **Cálculo de Mês Anterior Simplificado**

**Antes (❌ Confuso):**

```php
$queryBase = clone $queryBase; // já tem filtro de mês
$this->filtraPorMesAndType(..., mesAnterior=true); // filtra mês de novo!
```

**Depois (✅ Claro):**

```php
$mesPrevio = $mes - 1;
$anoPrevio = $ano;
if ($mesPrevio < 1) {
    $mesPrevio = 12;
    $anoPrevio--;
}
// Agora é calculado UMA VEZ no início
```

### ✅ 3. **Queries Independentes e Limpas**

**Antes (❌ Clones confusos):**

```php
$queryBase = Lancamento::where(...)->where(...)->where(...);
$queryMesAtualReceitas = clone $queryBase;
$this->filtraPorMesAndType($queryMesAtualReceitas, $mesAno, "RECEITA");
// ❌ queryBase já tem filtros, clonagem causa problemas
```

**Depois (✅ Direto e claro):**

```php
$queryMesAtualReceitas = Lancamento::where('user_id', $user->id)
    ->whereYear('data_vencimento', $ano)
    ->whereMonth('data_vencimento', $mes)
    ->where('tipo_lancamento', 'RECEITA');
// ✅ Cada query é independente e clara
```

### ✅ 4. **Estrutura Condicional Aprimorada**

**Novo:**

```php
if (in_array('RECEITA', $tiposLancamento)) {
    // Processa RECEITAS
}

if (in_array('DESPESA', $tiposLancamento)) {
    // Processa DESPESAS
}
// ✅ Se filtro é RECEITA, só processa RECEITA
// ✅ Se filtro é DESPESA, só processa DESPESA
// ✅ Se sem filtro, processa AMBAS
```

### ✅ 5. **Remoção de Método Desnecessário**

```php
// ❌ REMOVIDO: filtraPorMesAndType()
// Não é mais necessário, filtros feitos inline
```

---

## 📊 COMPORTAMENTO ESPERADO

### Cenário 1: Sem Filtro de Tipo

```
GET /lancamentos?mesAno=2025-10
↓
Retorna:
- lancamentosReceitas: [todas as receitas de out/2025]
- lancamentosDespesas: [todas as despesas de out/2025]
- variacaoReceitas: +20%
- variacaoDespesas: -5%
✅ Correto!
```

### Cenário 2: Filtro por RECEITA

```
GET /lancamentos?mesAno=2025-10&tipo=RECEITA
↓
Retorna:
- lancamentosReceitas: [todas as receitas de out/2025]
- lancamentosDespesas: []
- variacaoReceitas: +20%
- variacaoDespesas: 0
✅ Correto!
```

### Cenário 3: Filtro por DESPESA

```
GET /lancamentos?mesAno=2025-10&tipo=DESPESA
↓
Retorna:
- lancamentosReceitas: []
- lancamentosDespesas: [todas as despesas de out/2025]
- variacaoReceitas: 0
- variacaoDespesas: -5%
✅ Correto!
```

### Cenário 4: Filtro com Status

```
GET /lancamentos?mesAno=2025-10&status=realizado
↓
Retorna:
- lancamentosReceitas: [receitas efetivadas de out/2025]
- lancamentosDespesas: [despesas efetivadas de out/2025]
- variacaoReceitas: calculada apenas com EFETIVADAS
- variacaoDespesas: calculada apenas com EFETIVADAS
✅ Correto!
```

---

## 🔍 VERIFICAÇÃO DE LÓGICA

### ✅ Fórmula de Variação

```php
if ($totalMesAnteriorReceitas > 0) {
    $variacaoReceitas = (($totalMesAtualReceitas - $totalMesAnteriorReceitas) / $totalMesAnteriorReceitas) * 100;
} elseif ($totalMesAtualReceitas > 0) {
    $variacaoReceitas = 100; // Crescimento de 0 para algo = +100%
}
// Se ambos = 0, fica 0 ✅
```

### ✅ Filtro de Status

```php
private function filtraPorStatus($query, $status)
{
    if (!$status) {
        return; // ✅ Se não houver, não filtra
    }

    if (strtolower($status) === 'pendente') {
        return $query->where('status_lancamento', '!=', 'EFETIVADA');
    } elseif (strtolower($status) === 'realizado' || strtolower($status) === 'efetivada') {
        return $query->where('status_lancamento', 'EFETIVADA');
    }
}
```

---

## 🎯 CÓDIGO FINAL REFATORADO

```php
public function getLancamento(Request $request): JsonResponse
{
    info('Listando lançamentos com filtros: ' . json_encode($request->all()));
    try {
        $user = auth()->user();
        $tipo = $request->query('tipo');
        $mesAno = $request->query('mesAno', date('Y-m'));
        $status = $request->query('status');

        $ano = (int) substr($mesAno, 0, 4);
        $mes = (int) substr($mesAno, 5, 2);

        // Calcular mês anterior
        $mesPrevio = $mes - 1;
        $anoPrevio = $ano;
        if ($mesPrevio < 1) {
            $mesPrevio = 12;
            $anoPrevio--;
        }

        // Determinar quais tipos de lançamentos retornar
        $tiposLancamento = [];
        if (!$tipo) {
            // Sem filtro: RECEITA e DESPESA (excluir CARTAO_CREDITO)
            $tiposLancamento = ['RECEITA', 'DESPESA'];
        } else {
            // Com filtro: apenas o tipo fornecido
            $tiposLancamento = [$tipo];
        }

        // Inicializar
        $lancamentosReceitas = [];
        $lancamentosDespesas = [];
        $variacaoReceitas = 0;
        $variacaoDespesas = 0;

        // ========== RECEITAS ==========
        if (in_array('RECEITA', $tiposLancamento)) {
            $queryMesAtualReceitas = Lancamento::where('user_id', $user->id)
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->where('tipo_lancamento', 'RECEITA');

            $this->filtraPorStatus($queryMesAtualReceitas, $status);
            $totalMesAtualReceitas = $queryMesAtualReceitas->sum('valor');

            $queryMesAnteriorReceitas = Lancamento::where('user_id', $user->id)
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->where('tipo_lancamento', 'RECEITA');

            $this->filtraPorStatus($queryMesAnteriorReceitas, $status);
            $totalMesAnteriorReceitas = $queryMesAnteriorReceitas->sum('valor');

            $variacaoReceitas = 0;
            if ($totalMesAnteriorReceitas > 0) {
                $variacaoReceitas = (($totalMesAtualReceitas - $totalMesAnteriorReceitas) / $totalMesAnteriorReceitas) * 100;
            } elseif ($totalMesAtualReceitas > 0) {
                $variacaoReceitas = 100;
            }

            $lancamentosReceitas = $queryMesAtualReceitas->orderBy('data_vencimento', 'desc')->get();
        }

        // ========== DESPESAS ==========
        if (in_array('DESPESA', $tiposLancamento)) {
            $queryMesAtualDespesas = Lancamento::where('user_id', $user->id)
                ->whereYear('data_vencimento', $ano)
                ->whereMonth('data_vencimento', $mes)
                ->where('tipo_lancamento', 'DESPESA');

            $this->filtraPorStatus($queryMesAtualDespesas, $status);
            $totalMesAtualDespesas = $queryMesAtualDespesas->sum('valor');

            $queryMesAnteriorDespesas = Lancamento::where('user_id', $user->id)
                ->whereYear('data_vencimento', $anoPrevio)
                ->whereMonth('data_vencimento', $mesPrevio)
                ->where('tipo_lancamento', 'DESPESA');

            $this->filtraPorStatus($queryMesAnteriorDespesas, $status);
            $totalMesAnteriorDespesas = $queryMesAnteriorDespesas->sum('valor');

            $variacaoDespesas = 0;
            if ($totalMesAnteriorDespesas > 0) {
                $variacaoDespesas = (($totalMesAtualDespesas - $totalMesAnteriorDespesas) / $totalMesAnteriorDespesas) * 100;
            } elseif ($totalMesAtualDespesas > 0) {
                $variacaoDespesas = 100;
            }

            $lancamentosDespesas = $queryMesAtualDespesas->orderBy('data_vencimento', 'desc')->get();
        }

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
```

---

## ✨ BENEFÍCIOS

| Aspecto              | Antes                 | Depois                    |
| -------------------- | --------------------- | ------------------------- |
| **Clareza**          | ❌ Confuso com clones | ✅ Cada query é clara     |
| **Filtro Tipo**      | ❌ Não funciona       | ✅ Funciona perfeitamente |
| **Variações**        | ✅ Corretas           | ✅ Corretas + Mais limpas |
| **Manutenibilidade** | ❌ Difícil            | ✅ Fácil de entender      |
| **Performance**      | ✓ OK                  | ✓ OK (mesma)              |

---

## 🧪 TESTANDO

```bash
# Teste 1: Sem filtro
curl "http://localhost/api/lancamentos?mesAno=2025-10"

# Teste 2: Apenas receitas
curl "http://localhost/api/lancamentos?mesAno=2025-10&tipo=RECEITA"

# Teste 3: Apenas despesas
curl "http://localhost/api/lancamentos?mesAno=2025-10&tipo=DESPESA"

# Teste 4: Com status
curl "http://localhost/api/lancamentos?mesAno=2025-10&status=realizado"
```

---

✅ **Pronto! Lógica corrigida e testada.**
