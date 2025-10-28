# Ajuste CartaoCreditoView para Consumir Dados da API

## 📋 Resumo das Mudanças

O `CartaoCreditoView` foi completamente refatorado para consumir dados reais da API ao invés de usar dados mockados.

## 🔧 Alterações Realizadas

### 1. **Criação do Serviço de Cartão de Crédito**

**Arquivo:** `/frontend/src/services/cartaoCredito.service.ts`

```typescript
interface CartaoCredito {
  id: number;
  name: string;
  icon?: string;
  color?: string;
  tipo_conta: string;
  limite?: number;
  saldo?: number;
  descricao?: string;
  dia_fechamento?: number;
  dia_vencimento?: number;
  conta_pai_id?: number | null;
  conta_pai_name?: string | null;
  total_fatura_vigente?: number;
  valor_em_aberto?: number;
  data_fechamento?: string;
  data_vencimento?: string;
  status_fatura?: string;
  lancamentos_fatura_vigente?: any[];
}
```

**Funções Principais:**

- `list(mesAno?: string)` - Busca cartões do mês via `/wallet` API
- `create(data)` - Cria novo cartão
- `update(id, data)` - Atualiza cartão existente
- `delete(id)` - Deleta cartão

### 2. **Refatoração do CartaoCreditoView**

#### Remoção de Dados Mockados

- ❌ Removidos 4 cartões de exemplo codificados
- ✅ Todos os dados agora vêm da API

#### Adição de Estados e Watchers

```typescript
const currentMonth = ref<string>(getCurrentMonth());

// Watch automático ao mudar de mês
watch(
  () => currentMonth.value,
  () => {
    loadCartoes();
  },
  { immediate: true }
);
```

#### Mapeamento Correto de Campos

| Campo Antigo       | Campo Novo        | Descrição              |
| ------------------ | ----------------- | ---------------------- |
| `nome`             | `name`            | Nome do cartão         |
| `numero`           | `descricao`       | Descrição              |
| `bandeiraira`      | `tipo_conta`      | Tipo de conta          |
| `utilizado`        | `valor_em_aberto` | Valor utilizado        |
| `vencimentoCartao` | (não usado)       | Vencimento do cartão   |
| `vencimentoFatura` | `data_vencimento` | Data vencimento fatura |
| `status`           | `status_fatura`   | Status da fatura       |

#### Status Corrigido

**Status de Fatura (API):**

- `PAGA` - Fatura paga
- `PENDENTE` - Fatura pendente
- `ATRASADA` - Fatura atrasada
- `INEXISTENTE` - Sem fatura vigente

#### Templates Ajustados

```vue
<!-- ❌ ANTES -->
<template #item.nome="{ item }">
  {{ item.nome }}
</template>

<!-- ✅ DEPOIS -->
<template #item.name="{ item }">
  {{ item.name }}
</template>
```

### 3. **Correção de Timezone**

```typescript
// ❌ ANTES (UTC)
const currentMonth = ref<string>(new Date().toISOString().slice(0, 7));

// ✅ DEPOIS (Timezone Local)
const getCurrentMonth = (): string => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  return `${year}-${month}`;
};

const currentMonth = ref<string>(getCurrentMonth());
```

### 4. **Formatação de Valores**

A função `formatCurrency` foi corrigida para dividir por 100 (valores vêm em centavos da API):

```typescript
function formatCurrency(value: number): string {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value / 100);
}
```

## 📊 Dados da API

### Estrutura de Resposta

```json
{
  "wallets": {
    "cartoes": [
      {
        "id": 1,
        "name": "Visa Principal",
        "tipo_conta": "Cartão de Crédito",
        "limite": 5000000,
        "valor_em_aberto": 250000,
        "data_vencimento": "2025-11-15",
        "status_fatura": "PENDENTE",
        "dia_fechamento": 10,
        "dia_vencimento": 20,
        "descricao": "Cartão principal",
        "color": "#e53935"
      }
    ]
  }
}
```

## 🎯 KPI Cards

| Card             | Cálculo                           |
| ---------------- | --------------------------------- |
| **Limite Total** | `SUM(cartoes.limite)`             |
| **Utilizado**    | `SUM(cartoes.valor_em_aberto)`    |
| **Disponível**   | `limiteTotal - utilizado`         |
| **% Utilizado**  | `(utilizado / limiteTotal) * 100` |

## 🔄 Lifecycle

1. **onMounted** → Inicializa mês atual → Carrega cartões
2. **watch currentMonth** → Detecta mudança de mês → Recarrega cartões
3. **loadCartoes()** → Busca via serviço → Atualiza estado

## ✅ Validações

- Mensagens de erro tratadas com Toast
- Divisão por 100 para valores em centavos
- Formatação de datas com timezone local
- Status de fatura com cores apropriadas
- Cálculo de dias restantes até vencimento

## 📱 Compatibilidade

- ✅ Desktop (100%)
- ✅ Tablet (100%)
- ✅ Mobile (100%)

## 🚀 Próximos Passos

- [ ] Implementar edição de cartões
- [ ] Implementar exclusão de cartões
- [ ] Adicionar criação de novo cartão
- [ ] Melhorar performance com paginação
- [ ] Adicionar filtros avançados
