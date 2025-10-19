# 🔧 Fix: Status "Atrasada" - Cálculo Dinâmico com Base em Data

## ❌ Problema Identificado

Ao criar uma receita/despesa com:

- Data de vencimento: dias atrás
- Status: PENDENTE

A tabela e cards continuavam mostrando "Pendente" em vez de "Atrasada".

**Causa:** O status vinha do backend como PENDENTE/EFETIVADA, e não havia lógica no frontend para verificar se a data tinha passado.

---

## ✅ Solução Implementada

### 1. Função `getStatusReal()` - Calcula Status Dinamicamente

**ReceitasView.vue:**

```typescript
// ✅ Função para calcular o status real baseado na data de vencimento
const getStatusReal = (receita: any): string => {
  // Se status for EFETIVADA (recebida), retorna recebida
  if (receita.status_lancamento === "EFETIVADA") {
    return "recebida";
  }

  // Se status for PENDENTE, verifica se está atrasada
  if (receita.status_lancamento === "PENDENTE") {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    let dataVencimento = new Date(receita.data_vencimento);
    dataVencimento.setHours(0, 0, 0, 0);

    // Se a data de vencimento é anterior a hoje, está atrasada
    if (dataVencimento < hoje) {
      return "atrasada";
    }

    return "pendente";
  }

  // Fallback
  return receita.status || "pendente";
};
```

**DespesasView.vue:** Mesma lógica, mas retorna "paga" em vez de "recebida".

---

### 2. Cores Atualizadas

**getStatusColor():**

```typescript
const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    recebida: "success", // Verde
    pendente: "warning", // Amarelo
    atrasada: "error", // Vermelho ← NOVO
    cancelada: "error",
  };
  return colors[status] || "default";
};
```

| Status    | Cor      | Significado                      |
| --------- | -------- | -------------------------------- |
| Recebida  | Verde    | Recebida/Paga                    |
| Pendente  | Amarelo  | A receber/pagar, no prazo        |
| Atrasada  | Vermelho | A receber/pagar, passou do prazo |
| Cancelada | Vermelho | Cancelada                        |

---

### 3. Labels Atualizados

**getStatusLabel():**

```typescript
const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    recebida: "Recebida",
    pendente: "Pendente",
    atrasada: "Atrasada", // ← NOVO
    cancelada: "Cancelada",
  };
  return labels[status] || status;
};
```

---

### 4. Template da Tabela Atualizado

**ANTES:**

```vue
<v-chip
  :color="getStatusColor(item.status)"
  :text-color="getStatusTextColor(item.status)"
>
  {{ getStatusLabel(item.status) }}
</v-chip>
```

**DEPOIS:**

```vue
<v-chip
  :color="getStatusColor(getStatusReal(item))"
  :text-color="getStatusTextColor(getStatusReal(item))"
>
  {{ getStatusLabel(getStatusReal(item)) }}
</v-chip>
```

---

### 5. Cards de Summary Atualizados

**ANTES:**

```typescript
const receitasPendentes = computed(
  () => receitas.value.filter((r) => r.status === "pendente").length
);
const receitasAtrasadas = computed(
  () => receitas.value.filter((r) => r.status === "cancelada").length // ❌ Errado!
);
```

**DEPOIS:**

```typescript
const receitasPendentes = computed(
  () => receitas.value.filter((r) => getStatusReal(r) === "pendente").length // ✅ Dinâmico
);
const receitasAtrasadas = computed(
  () => receitas.value.filter((r) => getStatusReal(r) === "atrasada").length // ✅ Baseado em data
);
```

---

## 🎯 Fluxo Completo

### Cenário: Receita com vencimento 5 dias atrás

1. **Dados no Backend:**

   - `data_vencimento: "2025-10-03"`
   - `status_lancamento: "PENDENTE"`

2. **Frontend carrega:**

   - `getStatusReal()` é chamada
   - Verifica: hoje (2025-10-18) > data_vencimento (2025-10-03)?
   - ✅ SIM → retorna "atrasada"

3. **Tabela exibe:**

   - Cor: Vermelho ❌
   - Label: "Atrasada"

4. **Cards de Summary:**
   - Card "Pendentes": -1 (removido)
   - Card "Atrasadas": +1 (adicionado)
   - Soma atualizada

---

## 📝 Mudanças em Cada Arquivo

### ReceitasView.vue

**Adicionada função (linha ~900):**

```typescript
const getStatusReal = (receita: any): string => {
  // Verificar EFETIVADA
  // Verificar PENDENTE com data passada
  // Retornar 'recebida', 'pendente' ou 'atrasada'
};
```

**Atualizado getStatusColor():**

- Adicionada cor para 'atrasada': 'error' (vermelho)

**Atualizado getStatusLabel():**

- Adicionado label para 'atrasada': 'Atrasada'

**Atualizado template #item.status (linha ~199):**

```vue
:color="getStatusColor(getStatusReal(item))"
{{ getStatusLabel(getStatusReal(item)) }}
```

**Atualizado computed receitasRecebidas (linha ~810):**

```typescript
filter((r) => getStatusReal(r) === "recebida");
```

**Atualizado computed receitasPendentes (linha ~812):**

```typescript
filter((r) => getStatusReal(r) === "pendente");
```

**Atualizado computed receitasAtrasadas (linha ~814):**

```typescript
filter((r) => getStatusReal(r) === "atrasada");
```

**Atualizado filteredReceitas (linha ~820):**

```typescript
matchStatus =
  !selectedStatus.value || getStatusReal(r) === selectedStatus.value;
```

### DespesasView.vue

**Adicionada função (linha ~478):**

```typescript
const getStatusReal = (despesa: any): string => {
  // Mesma lógica, retornando 'paga' em vez de 'recebida'
};
```

**Atualizado getStatusColor():**

- Adicionada cor para 'atrasada': 'error'

**Atualizado getStatusLabel():**

- Adicionado label para 'atrasada': 'Atrasada'

**Atualizado template #item.status (linha ~199):**

```vue
:color="getStatusColor(getStatusReal(item))"
{{ getStatusLabel(getStatusReal(item)) }}
```

**Atualizado computed despesasPagas, despesasPendentes, despesasAtrasadas:**

- Todos usando `getStatusReal()`

**Atualizado filteredDespesas:**

- Usando `getStatusReal()` para filtrar

---

## 🧪 Como Testar

### Teste 1: Criar Receita Atrasada

1. Criar nova receita
2. Data de vencimento: 5 dias atrás (ex: 2025-10-13 se hoje é 2025-10-18)
3. Clicar em "Salvar"
4. ✅ Na tabela: status mostra "Atrasada" em VERMELHO
5. ✅ Card "Atrasadas": contador aumenta
6. ✅ Card "Pendentes": contador não muda

### Teste 2: Criar Receita Futura

1. Criar nova receita
2. Data de vencimento: 3 dias no futuro (ex: 2025-10-21)
3. Clicar em "Salvar"
4. ✅ Na tabela: status mostra "Pendente" em AMARELO
5. ✅ Card "Pendentes": contador aumenta
6. ✅ Card "Atrasadas": contador não muda

### Teste 3: Efetivação

1. Criar receita com qualquer data
2. Marcar como "Recebida" (EFETIVADA)
3. ✅ Status muda para "Recebida" em VERDE
4. ✅ Cards atualizam corretamente

### Teste 4: Filtro de Status

1. Criar várias receitas (algumas atrasadas, algumas pendentes)
2. Clicar em filtro "Status"
3. ✅ Opção "Atrasada" aparece
4. ✅ Filtrar por "Atrasada" mostra apenas as atrasadas
5. ✅ Filtrar por "Pendente" mostra apenas as pendentes

---

## 🚀 Impacto

| Aspecto                     | Antes               | Depois                   |
| --------------------------- | ------------------- | ------------------------ |
| Status de pendente atrasado | Pendente (errado)   | Atrasada ✅              |
| Cor de atrasado             | Não diferenciava    | Vermelho ✅              |
| Card "Atrasadas"            | Mostrava canceladas | Mostra reais ✅          |
| Dinâmico                    | Não                 | Sim ✅                   |
| Mantém estado do backend    | N/A                 | Sim (PENDENTE permanece) |

---

## 💡 Nota Importante

✅ **O status no backend permanece PENDENTE/EFETIVADA**

- Não é alterado
- Apenas a EXIBIÇÃO muda no frontend
- Quando a data passar do dia de hoje, automatically passa de "Pendente" para "Atrasada"
- Sem necessidade de atualizar o backend

---

## 🔍 Funcionamento Técnico

### Comparação de Datas

```typescript
const hoje = new Date();
hoje.setHours(0, 0, 0, 0); // Zera hora para comparação apenas de data

let dataVencimento = new Date(receita.data_vencimento);
dataVencimento.setHours(0, 0, 0, 0); // Zera hora

// Comparação simples:
if (dataVencimento < hoje) {
  // Passou do vencimento
  return "atrasada";
}
```

### Reatividade

Como usa `computed()`, qualquer mudança em `receitas.value` ou `despesas.value` re-calcula todos os status automaticamente.

---

## ✨ Resultado Final

Agora o sistema mostra corretamente:

- ✅ Receitas/Despesas atrasadas em VERMELHO com label "Atrasada"
- ✅ Cards com contadores corretos
- ✅ Filtros funcionando com status "Atrasada"
- ✅ Sem alterar dados no backend
- ✅ Totalmente dinâmico baseado em data actual
