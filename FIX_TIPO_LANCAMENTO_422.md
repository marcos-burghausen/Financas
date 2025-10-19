# 🔧 Fix: tipo_lancamento Field Required - Solução

## ❌ Problema

```
POST http://localhost:4080/api/lancamentos 422 (Unprocessable Content)
ReceitasView.vue:1068 Erro ao salvar receita: Error: The tipo lancamento field is required.
```

## 🔍 Análise da Causa

O backend tem um `StoreLancamentoRequest.php` que **transforma** os dados ANTES da validação:

```php
protected function prepareForValidation(): void
{
    $this->merge([
        'tipo_lancamento' => $this->transformTipoLancamento(),
        // ... outros campos
    ]);
}

private function transformTipoLancamento(): string
{
    $map = [
        'Receita' => 'RECEITA',           // ✅ Esperando "Receita"
        'Despesa' => 'DESPESA',           // ✅ Esperando "Despesa"
        'Cartão de Crédito' => 'CARTAO_CREDITO',
    ];
    return $map[$this->input('tipo_lancamento')] ?? '';  // ❌ Retorna '' se não encontrar
}
```

### O Erro Ocorria Porque:

1. Frontend enviava: `tipo_lancamento: "RECEITA"` (MAIÚSCULAS)
2. Backend tentava mapear na função `transformTipoLancamento()`
3. Não encontrava a chave "RECEITA" no mapa
4. Retornava vazio `''`
5. Validação rejeitava porque esperava `RECEITA` (após transformação)

### A Solução:

O backend **já faz a transformação**! Então o frontend deve enviar os valores "legíveis":

- Enviar: `"Receita"` → Backend transforma para `"RECEITA"`
- Enviar: `"Despesa"` → Backend transforma para `"DESPESA"`

---

## ✅ Solução Implementada

### Mudança no ReceitasView.vue

```diff
const payload: any = {
  // ...
-  tipo_lancamento: 'RECEITA',        // ❌ Errado
+  tipo_lancamento: 'Receita',        // ✅ Certo (backend transforma)
  recorrencia: 'NAO_RECORRENTE',      // ✅ Pode ser MAIÚSCULAS (não tem transformação)
  // ...
};
```

### Mudança no DespesasView.vue

```diff
const payload: any = {
  // ...
-  tipo_lancamento: 'DESPESA',        // ❌ Errado
+  tipo_lancamento: 'Despesa',        // ✅ Certo (backend transforma)
  recorrencia: 'NAO_RECORRENTE',      // ✅ Pode ser MAIÚSCULAS
  // ...
};
```

---

## 📋 Entendimento Correto da Transformação

### O Backend faz transformação em `prepareForValidation()`:

| Campo             | Entrada Esperada | Transformação            | Saída para Validação |
| ----------------- | ---------------- | ------------------------ | -------------------- |
| `tipo_lancamento` | "Receita"        | Mapeia para "RECEITA"    | "RECEITA" ✅         |
| `tipo_lancamento` | "Despesa"        | Mapeia para "DESPESA"    | "DESPESA" ✅         |
| `tipo_lancamento` | "RECEITA"        | Não encontra no mapa     | "" ❌                |
| `recorrencia`     | "NAO_RECORRENTE" | Mapeia de volta          | "NAO_RECORRENTE" ✅  |
| `valor`           | "1.234,56"       | Converte para centavos   | 123456 ✅            |
| `tipo_parcela`    | "TOTAL"          | Converte para MAIÚSCULAS | "TOTAL" ✅           |
| `periodicidade`   | "mensal"         | Converte para MAIÚSCULAS | "MENSAL" ✅          |

---

## 🎯 Resumo de Mudanças

### ReceitasView.vue (ReceitasView.vue:1011)

```typescript
tipo_lancamento: "Receita"; // ✅ String legível, backend transforma
```

### DespesasView.vue (DespesasView.vue:530)

```typescript
tipo_lancamento: "Despesa"; // ✅ String legível, backend transforma
```

---

## 📝 Padrão Correto de Payload

### O Frontend Deve Enviar:

```typescript
{
  tipo_lancamento: 'Receita',          // ✅ Legível
  recorrencia: 'NAO_RECORRENTE',       // ✅ MAIÚSCULAS (sem transformação)
  valor: '10,00',                      // ✅ STRING com centavos
  status_lancamento: 'PENDENTE',       // ✅ MAIÚSCULAS (sem transformação)
  tipo_parcela: 'total',               // ✅ lowercase (será transformado para TOTAL)
  periodicidade: 'MENSAL',             // ✅ MAIÚSCULAS
  // ... resto dos campos ...
}
```

### O Backend Transforma Para:

```php
[
  'tipo_lancamento' => 'RECEITA',      // ✅ Backend transformou
  'recorrencia' => 'NAO_RECORRENTE',   // Sem transformação
  'valor' => 1000,                     // ✅ Backend converteu para centavos
  'status_lancamento' => 'PENDENTE',   // Sem transformação
  'tipo_parcela' => 'TOTAL',           // ✅ Backend transformou para MAIÚSCULAS
  'periodicidade' => 'MENSAL',         // Sem transformação
]
```

---

## ✨ O Que Estava Errado

**Antes:**

```typescript
// ReceitasView.vue ❌
tipo_lancamento: "RECEITA"; // Frontend enviava MAIÚSCULAS
// Backend não encontrava no mapa
// Retornava vazio ''
// Validação falhava

// DespesasView.vue ❌
tipo_lancamento: "DESPESA"; // Mesmo problema
```

**Depois:**

```typescript
// ReceitasView.vue ✅
tipo_lancamento: "Receita"; // Frontend envia legível
// Backend encontra no mapa
// Transforma para 'RECEITA'
// Validação passa

// DespesasView.vue ✅
tipo_lancamento: "Despesa"; // Frontend envia legível
// Backend encontra no mapa
// Transforma para 'DESPESA'
// Validação passa
```

---

## 🧪 Para Testar Agora

1. Abrir formulário de Receita
2. Preencher dados:
   - Descrição: "Teste"
   - Valor: "100,00"
   - Categoria: "Vendas"
   - Subcategoria: "Produto"
   - Conta: Selecione uma
   - Data vencimento: Data de hoje
3. Clicar em Salvar
4. Verificar se cria sem erro 422

Se ainda der erro 422, verificar o console para ver qual campo está gerando problema (pode haver outros campos obrigatórios faltando).

---

## 📚 Lição Aprendida

🎓 **Importante:** O backend pode fazer **transformações** nos dados ANTES da validação. Isso significa que:

1. O frontend NÃO deve pré-processar dados que o backend vai transformar
2. Enviar dados no formato "legível" (como o usuário digitaria)
3. Deixar o backend fazer a transformação para formatos internos (MAIÚSCULAS, centavos, etc)

**Regra de Ouro:**

- `tipo_lancamento`: Enviar "Receita" / "Despesa" (o backend transforma para MAIÚSCULAS)
- `recorrencia`: Enviar direto em MAIÚSCULAS (não há transformação)
- `valor`: Enviar como STRING "10,00" (backend converte para centavos)
- `tipo_parcela`: Pode enviar "total" em lowercase (será transformado para TOTAL)
- `periodicidade`: Pode enviar em MAIÚSCULAS (será validado contra MAIÚSCULAS)
