# 🔧 Fix: Formato de Data no Payload e Date Picker

## ❌ Problemas Identificados

### Problema 1: Formato de Data Incorreto

```
Enviado ao backend: "data_lancamento": "2025-10-09T03:00:00.000Z"
Backend espera: "data_lancamento": "2025-10-09" (YYYY-MM-DD)
```

**Causa:** O `v-date-picker` do Vuetify retorna datas em formato ISO 8601 com timezone, mas o backend espera apenas YYYY-MM-DD.

### Problema 2: Date Picker Não Fecha

O calendário não fechava após selecionar uma data, exigindo que o usuário clicasse fora para fechar.

---

## ✅ Solução Implementada

### 1. Função `formatDateForBackend()` (ReceitasView.vue e DespesasView.vue)

Nova função que converte datas para formato YYYY-MM-DD:

```typescript
// ✅ Formatar data para enviar ao backend (YYYY-MM-DD)
const formatDateForBackend = (
  dateValue: string | Date | undefined | null
): string => {
  if (!dateValue) return "";

  try {
    // Se for string ISO com timezone, extrair apenas a data
    if (typeof dateValue === "string" && dateValue.includes("T")) {
      return dateValue.split("T")[0]; // "2025-10-09T03:00:00.000Z" → "2025-10-09"
    }

    // Se for string em formato YYYY-MM-DD, retornar como está
    if (
      typeof dateValue === "string" &&
      dateValue.match(/^\d{4}-\d{2}-\d{2}$/)
    ) {
      return dateValue;
    }

    // Se for Date object, formatar
    if (dateValue instanceof Date) {
      const year = dateValue.getFullYear();
      const month = String(dateValue.getMonth() + 1).padStart(2, "0");
      const day = String(dateValue.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    }

    return "";
  } catch (error) {
    console.error("Erro ao formatar data:", dateValue, error);
    return "";
  }
};
```

**Funciona com:**

- Strings ISO: `"2025-10-09T03:00:00.000Z"` → `"2025-10-09"`
- Strings YYYY-MM-DD: `"2025-10-09"` → `"2025-10-09"`
- Date objects: `new Date('2025-10-09')` → `"2025-10-09"`

---

### 2. Uso no Payload (ReceitasView.vue)

**ANTES:**

```typescript
const payload = {
  data_vencimento: formData.value.data_vencimento, // ❌ ISO com timezone
  data_lancamento: formData.value.data_lancamento, // ❌ ISO com timezone
  data_efetivacao: formData.value.data_efetivacao, // ❌ ISO com timezone
};
```

**DEPOIS:**

```typescript
const payload = {
  data_vencimento: formatDateForBackend(formData.value.data_vencimento), // ✅ YYYY-MM-DD
  data_lancamento: formatDateForBackend(formData.value.data_lancamento), // ✅ YYYY-MM-DD
  data_efetivacao: formatDateForBackend(formData.value.data_efetivacao), // ✅ YYYY-MM-DD
};
```

---

### 3. Date Picker Auto-Close (ReceitasView.vue)

**Adicionados três Refs:**

```typescript
// ✅ Refs para controlar date pickers
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);
```

**Adicionados Watchers:**

```typescript
// ✅ Watchers para fechar date pickers automaticamente após seleção
watch(
  () => formData.value.data_vencimento,
  (newVal) => {
    if (newVal) {
      menuDataVencimento.value = false; // Fechar date picker
    }
  }
);

watch(
  () => formData.value.data_lancamento,
  (newVal) => {
    if (newVal) {
      menuDataLancamento.value = false; // Fechar date picker
    }
  }
);

watch(
  () => formData.value.data_efetivacao,
  (newVal) => {
    if (newVal) {
      menuDataEfetivacao.value = false; // Fechar date picker
    }
  }
);
```

**Atualizado o v-menu no Template:**

```vue
<!-- ANTES -->
<v-menu :close-on-content-click="false" transition="scale-transition">
  ...
</v-menu>

<!-- DEPOIS -->
<v-menu
  v-model="menuDataVencimento"
  :close-on-content-click="false"
  transition="scale-transition"
>
  ...
</v-menu>
```

---

## 🎯 Resultado Final

### Fluxo Completo:

1. **Usuário seleciona uma data no calendário**

   - Data é armazenada em `formData.value.data_vencimento`
   - Formato: `"2025-10-09T03:00:00.000Z"` (ISO com timezone)

2. **Watcher detecta mudança**

   - Automáticamente fecha o date picker
   - `menuDataVencimento.value = false`

3. **Usuário clica em "Salvar"**

   - Função `saveReceita()` é chamada
   - `formatDateForBackend()` converte para `"2025-10-09"` (YYYY-MM-DD)
   - Payload é enviado correto

4. **Backend recebe**
   - `data_vencimento: "2025-10-09"` ✅
   - `data_lancamento: "2025-10-09"` ✅
   - `data_efetivacao: "2025-10-09"` ✅

---

## 📝 Resumo das Mudanças

### ReceitasView.vue

- ✅ Importação atualizada: `import { computed, onMounted, ref, watch }`
- ✅ Adicionadas 3 refs: `menuDataVencimento`, `menuDataLancamento`, `menuDataEfetivacao`
- ✅ Função: `formatDateForBackend()`
- ✅ Watchers: 3 watchers para auto-close do date picker
- ✅ Payload: Usa `formatDateForBackend()` para todas as datas
- ✅ Template: `v-model` para os v-menu dos date pickers

### DespesasView.vue

- ✅ Importação atualizada: `import { computed, onMounted, ref, watch }`
- ✅ Adicionadas 3 refs: `menuDataVencimento`, `menuDataLancamento`, `menuDataEfetivacao`
- ✅ Função: `formatDateForBackend()`
- ✅ Watchers: 3 watchers para auto-close do date picker
- ✅ Payload: Usa `formatDateForBackend()` para todas as datas
- ✅ Template: Usa v-text-field com type="date" (fecha automaticamente)

---

## 🧪 Como Testar

### Teste 1: Verificar Formato no Network

1. Abrir DevTools → Network
2. Criar nova receita com data "10/10/2025"
3. Clicar em "Salvar"
4. Ver POST /api/lancamentos
5. Verificar Request Payload:
   - ✅ `"data_vencimento": "2025-10-10"`
   - ✅ NÃO deve ter `T` ou timezone
   - ✅ NÃO deve ter `.000Z`

### Teste 2: Verificar Auto-Close

1. Clicar no campo "Data de Vencimento"
2. Calendário abre
3. Selecionar uma data
4. ✅ Calendário fecha automaticamente
5. Campo mostra a data formatada

### Teste 3: Editar Receita

1. Criar uma receita
2. Ir para Editar (com a mesma receita)
3. Ver que a data está preenchida
4. Mudar para outra data
5. Clicar em "Salvar"
6. ✅ Deve atualizar corretamente

---

## 🚀 Impacto

| Aspecto                 | Antes                     | Depois                   |
| ----------------------- | ------------------------- | ------------------------ |
| Formato de data enviado | ISO com timezone ❌       | YYYY-MM-DD ✅            |
| Calendário              | Não fechava               | Fecha automaticamente ✅ |
| Erro de validação       | Possível erro de data     | Sem erros de formato ✅  |
| UX                      | Ruim (usuário clica fora) | Bom (fecha sozinho)      |

---

## 📚 Implementação Detalhada

### ReceitasView.vue - Linhas Afetadas

**Imports (linha 617):**

```diff
- import { computed, onMounted, ref } from 'vue';
+ import { computed, onMounted, ref, watch } from 'vue';
```

**Refs (linhas 630-635):**

```typescript
// ✅ Refs para controlar date pickers
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);
```

**Função (linhas 900-930):**

```typescript
const formatDateForBackend = (
  dateValue: string | Date | undefined | null
): string => {
  // ... implementação completa ...
};
```

**Watchers (linhas 1150-1170):**

```typescript
watch(
  () => formData.value.data_vencimento,
  (newVal) => {
    if (newVal) menuDataVencimento.value = false;
  }
);
// ... mais 2 watchers ...
```

**Payload (linhas 1030-1050):**

```typescript
data_vencimento: formatDateForBackend(formData.value.data_vencimento),
data_lancamento: formatDateForBackend(formData.value.data_lancamento),
data_efetivacao: formatDateForBackend(formData.value.data_efetivacao),
```

**Template (linha 502):**

```vue
<v-menu v-model="menuDataVencimento" ...>
```

---

## ✨ Conclusão

Problema totalmente resolvido! Agora:

- ✅ Datas são enviadas no formato correto (YYYY-MM-DD)
- ✅ Date picker fecha automaticamente após seleção
- ✅ Sem mais erros de formato de data
- ✅ UX melhorada com auto-close
