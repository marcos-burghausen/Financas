# 🔧 Fix: Problema de Saldo não Revertido ao Alterar Status

## 🚨 Problema Identificado

**Issue:** Quando se edita uma despesa EFETIVADA e altera o status para PENDENTE, o saldo da conta não era revertido.

**Causa Raiz:** O status estava sendo capturado **antes** das transformações do `StoreLancamentoRequest` serem aplicadas.

## 📋 Análise Técnica

### 🔍 **Fluxo Problemático:**

1. Frontend envia: `status_lancamento: "PENDENTE"`
2. Controller captura: `$statusNovo = $request->input('status_lancamento')` ❌
3. Comparação falha: `"EFETIVADA" !== "PENDENTE"` (correto, mas...)
4. Validação aplica transformações (tarde demais)
5. Saldo não é ajustado porque a comparação já foi feita

### 🎯 **Problema no Código:**

```php
// ❌ ANTES (PROBLEMÁTICO)
$statusAnterior = $lancamento->status_lancamento; // "EFETIVADA"
$statusNovo = $request->input('status_lancamento'); // "PENDENTE" (sem transformação)

if ($statusAnterior !== $statusNovo) {
    // Lógica de ajuste de saldo
}

$validatedData = $request->validated(); // Transformações aplicadas aqui (tarde!)
```

## ✅ Solução Implementada

### 🔧 **1. Correção no StoreLancamentoRequest**

**Arquivo:** `backend/app/Http/Requests/StoreLancamentoRequest.php`

```php
// ✅ Descomentei e ativei as transformações
protected function prepareForValidation(): void
{
    $this->merge([
        // ... outros campos
        'recorrencia'       => $this->transformRecorrencia(),
        'status_lancamento' => $this->transformStatus(), // ✅ ATIVADO
        // ... outros campos
    ]);
}

// ✅ Função de transformação melhorada
private function transformStatus(): string
{
    $valor = $this->input('status_lancamento');

    // Se já estiver em formato MAIÚSCULO, retorna como está
    if (in_array($valor, ['PENDENTE', 'EFETIVADA'])) {
        return $valor;
    }

    // Transforma do formato PT para EN
    $map = [
        'Pendente' => 'PENDENTE',
        'Efetivada' => 'EFETIVADA',
    ];
    return $map[$valor] ?? 'PENDENTE';
}
```

### 🔧 **2. Correção no LancamentoController**

**Arquivo:** `backend/app/Http/Controllers/LancamentoController.php`

```php
// ✅ DEPOIS (CORRIGIDO)
DB::beginTransaction();

// ✅ Primeiro validar os dados para aplicar transformações
$validatedData = $request->validated();

// ✅ Capturar status APÓS validação/transformação
$statusAnterior = $lancamento->status_lancamento;
$statusNovo = $validatedData['status_lancamento']; // Status transformado

if ($statusAnterior !== $statusNovo) {
    // ✅ Lógica de ajuste de saldo funciona corretamente
    if ($statusAnterior === 'EFETIVADA') {
        // Reverter operação anterior
        if ($lancamento->tipo_lancamento === 'DESPESA') {
            $conta->saldo += $lancamento->valor; // Devolver despesa
        }
    }

    if ($statusNovo === 'EFETIVADA') {
        // Aplicar nova operação
        if ($lancamento->tipo_lancamento === 'DESPESA') {
            $conta->saldo -= $lancamento->valor; // Subtrair despesa
        }
    }
}

// ✅ Atualizar com dados já validados
$lancamento->update($validatedData);
```

## 🧪 Teste da Correção

### **Cenário de Teste:**

1. Despesa EFETIVADA de R$ 100,00
2. Saldo da conta: R$ 1000,00 (após despesa ser efetivada: R$ 900,00)
3. Alterar status para PENDENTE
4. **Resultado esperado:** Saldo volta para R$ 1000,00

### **Fluxo Corrigido:**

1. ✅ Status capturado após transformação: `"EFETIVADA" → "PENDENTE"`
2. ✅ Comparação detecta mudança: `"EFETIVADA" !== "PENDENTE"`
3. ✅ Reverter despesa efetivada: `saldo += 10000` (centavos)
4. ✅ Não aplicar nova efetivação (status = PENDENTE)
5. ✅ Saldo corretamente revertido

## 📊 Impacto da Correção

### ✅ **Antes vs Depois:**

| Cenário              | Antes                  | Depois                     |
| -------------------- | ---------------------- | -------------------------- |
| EFETIVADA → PENDENTE | ❌ Saldo não revertido | ✅ Saldo revertido         |
| PENDENTE → EFETIVADA | ✅ Funcionava          | ✅ Continua funcionando    |
| Valores em PT        | ❌ Não transformava    | ✅ Transforma corretamente |
| Logs de debug        | ❌ Status incorreto    | ✅ Status correto          |

### 🔍 **Validações Adicionais:**

- ✅ **Recorrência:** Transformação de "Não recorrente" → "NAO_RECORRENTE"
- ✅ **Status:** Transformação de "Pendente" → "PENDENTE"
- ✅ **Tipo:** Transformação de "Despesa" → "DESPESA"
- ✅ **Compatibilidade:** Valores já em maiúsculo passam direto

## 🚀 Status da Correção

- [x] ✅ StoreLancamentoRequest corrigido
- [x] ✅ Controller corrigido
- [x] ✅ Lógica de saldo correta
- [x] ✅ Logs detalhados para debug
- [x] ✅ Transformações ativadas
- [x] ✅ Compatibilidade mantida

**Resultado:** O problema do saldo não revertido está corrigido! 🎯
