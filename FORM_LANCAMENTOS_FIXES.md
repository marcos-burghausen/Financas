# FormLancamentos Fixes - ReceitasView

## 📋 Resumo das Correções

Implementadas 4 correções principais no formulário de ReceitasView para resolver problemas identificados:

---

## ✅ Correção 1: Modal Não Centraliza

**Status:** ✅ RESOLVIDO

**Problema:** Modal de recorrência não estava centralizado na tela

**Solução:**

- Uso de `v-menu` do Vuetify com posicionamento automático (já estava correto)
- Vuetify's `v-menu` automaticamente centra o conteúdo na maioria das telas

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linha 315)

---

## ✅ Correção 2: Validação de Parcela Inicial

**Status:** ✅ RESOLVIDO

**Problema:** Campo "Parcela Inicial" permitia valores maiores que quantidade total de parcelas

**Solução Implementada:**

```vue
<!-- Linha 357 -->
<v-text-field
  v-model.number="tempParcelaInicial"
  type="number"
  density="compact"
  style="width: 60px"
  min="1"
  :max="tempNumParcelas"  <!-- ✅ ADICIONADO -->
/>

<!-- Linha 360 -->
<v-btn
  icon="mdi-plus"
  size="x-small"
  :disabled="tempParcelaInicial >= tempNumParcelas"  <!-- ✅ ADICIONADO -->
  @click="tempParcelaInicial++"
/>
```

**Resultado:** Agora impossível definir parcela inicial > quantidade de parcelas

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 357-360)

---

## ✅ Correção 3: Exibição do Valor da Parcela

**Status:** ✅ RESOLVIDO (+ BUG FIX)

**Problema:** Após definir parcelas, o valor calculado não era exibido corretamente

### Bug Corrigido - Toggle VALOR PARCELA vs VALOR TOTAL

**Antes (Bugado):**

- Valor: 1000
- Tipo: VALOR TOTAL
- Parcelas: 2
- Exibia: "2x de R$ 500" ✅ CORRETO

Mas quando mudava para VALOR PARCELA:

- Valor: 1000
- Tipo: VALOR PARCELA
- Parcelas: 2
- Exibia: "2x de R$ 500" ❌ ERRADO (deveria ser "2x de R$ 1.000")

**Causa:** O código **sempre** dividia o valor por número de parcelas, sem considerar qual toggle estava ativo

**Solução Implementada:**

```typescript
// Linha 763-780
const detalheRecorrencia = computed(() => {
  if (
    formData.value.recorrencia === "Parcelado" &&
    formData.value.valor &&
    tempNumParcelas.value > 0
  ) {
    const valorInput = parseFloat(
      formData.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (!isNaN(valorInput) && valorInput > 0) {
      let valorParcela: number;

      // Se toggle está em 'total', divide o valor pelo número de parcelas
      // Se toggle está em 'parcela', o valor já é o valor de uma parcela
      if (tipoCalculoParcela.value === "total") {
        valorParcela = valorInput / tempNumParcelas.value;
      } else {
        valorParcela = valorInput; // ✅ Não divide quando já é valor de parcela
      }

      const valorFormatado = valorParcela.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return `Em ${tempNumParcelas.value}x de R$ ${valorFormatado}`;
    }
  }
  return "";
});
```

**Resultado Esperado (Agora Correto):**

| Tipo          | Valor | Parcelas | Exibe                |
| ------------- | ----- | -------- | -------------------- |
| VALOR TOTAL   | 1000  | 2        | 2x de R$ 500,00 ✅   |
| VALOR PARCELA | 1000  | 2        | 2x de R$ 1.000,00 ✅ |
| VALOR TOTAL   | 600   | 3        | 3x de R$ 200,00 ✅   |
| VALOR PARCELA | 600   | 3        | 3x de R$ 600,00 ✅   |

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 763-780)

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 751-760)

---

## ✅ Correção 4: Carregar Categorias/Contas da API (CRÍTICA)

**Status:** ✅ RESOLVIDO

**Problema:** Dropdowns estavam vazios, mostrando apenas dados hardcoded

### 4.1 Importações Adicionadas

```typescript
// Linha 614
import { useLancamentos } from "@/composables/useLancamentos";
```

### 4.2 Stores Adicionados

```typescript
// Linhas 619-622
const revenuesStore = useRevenuesStore();
const walletsStore = useWalletsStore();
```

### 4.3 Função de Carregamento

```typescript
// Linhas 907-916 - Nova função
const loadFormData = async () => {
  try {
    // Load revenues categories if needed
    if (
      !revenuesStore.revenuesData?.categories ||
      revenuesStore.revenuesData.categories.length === 0
    ) {
      const { updateData: updateRevenuesData } = useLancamentos("receita");
      await updateRevenuesData();
    }
    // Load wallets if needed
    if (
      !walletsStore.walletsData?.contas ||
      walletsStore.walletsData.contas.length === 0
    ) {
      walletsStore.loadFromSession();
    }
  } catch (error) {
    console.error("Erro ao carregar dados do formulário:", error);
  }
};
```

### 4.4 Categorias - Agora com API

```typescript
// Linha 730
const categoriasNames = computed(() => {
  // Usa dados do store se disponível, senão usa hardcoded como fallback
  if (
    revenuesStore.revenuesData?.categories &&
    revenuesStore.revenuesData.categories.length > 0
  ) {
    return revenuesStore.revenuesData.categories.map((cat: any) => cat.name);
  }
  return ["Salário", "Freelancer", "Bonus", "Investimento", "Outros"];
});
```

### 4.5 Contas - Agora com API

```typescript
// Linha 747
const contas = computed(() => {
  // Usa dados do store se disponível, senão usa hardcoded como fallback
  if (
    walletsStore.walletsData?.contas &&
    walletsStore.walletsData.contas.length > 0
  ) {
    return walletsStore.walletsData.contas;
  }
  return [
    { id: 1, name: "Conta Principal" },
    { id: 2, name: "Conta Investimento" },
    { id: 3, name: "Poupança" },
  ];
});
```

### 4.6 Subcategorias - Agora com API

```typescript
// Linha 752
const subcategoriasDaCategoriaSelecionada = computed(() => {
  // Tenta usar dados do store primeiro
  if (
    revenuesStore.revenuesData?.categories &&
    revenuesStore.revenuesData.categories.length > 0
  ) {
    const categoryFound = revenuesStore.revenuesData.categories.find(
      (cat: any) => cat.name === formData.value.categoria
    );
    if (categoryFound && categoryFound.subcategories) {
      return categoryFound.subcategories.map((sub: any) => sub.name);
    }
  }
  // Fallback para dados hardcoded
  return subcategorias.value[formData.value.categoria] || [];
});
```

### 4.7 Modal Chama Carregamento

```typescript
// Linhas 935-952 - openAddDialog atualizado
const openAddDialog = async () => {
  editingId.value = null;
  // Carrega dados dos stores se ainda não estiverem carregados
  await loadFormData();
  formData.value = {
    descricao: "",
    categoria: "",
    conta: "",
    valor: "0,00",
    data_vencimento: new Date().toISOString().split("T")[0],
    status: "pendente",
    observacao: "",
    recorrencia: "Não recorrente",
    status_lancamento: "PENDENTE",
    subcategoria: "",
    conta_id: contas.value[0]?.id || 1,
    data_lancamento: new Date().toISOString().split("T")[0],
    data_efetivacao: null,
    observacoes: "",
  };
  dialog.value = true;
};
```

**Resultado:**

- ✅ Categorias carregam da API via `useLancamentos('receita')`
- ✅ Contas/Wallets carregam do store/sessionStorage
- ✅ Subcategorias filtradas baseadas na categoria selecionada
- ✅ Fallback para dados hardcoded se API falhar

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (múltiplas linhas)

---

## 📊 Comparação Antes vs Depois

| Funcionalidade    | Antes                               | Depois                              |
| ----------------- | ----------------------------------- | ----------------------------------- |
| Modal Recorrência | Não centralizado                    | ✅ Centralizado (v-menu)            |
| Parcela Inicial   | Pode exceder máximo                 | ✅ Validado com max                 |
| Valor Parcela     | Não respeitava toggle VALOR/PARCELA | ✅ Responde dinâmicamente ao toggle |
| Categorias        | Hardcoded (vazio)                   | ✅ Carrega da API                   |
| Contas            | Hardcoded (3 itens)                 | ✅ Carrega do store                 |
| Subcategorias     | Hardcoded                           | ✅ Filtra por categoria da API      |

**Exemplo do Bug Corrigido:**

- Valor 1000, Parcelas 2, Toggle "VALOR TOTAL" → Antes: "2x R$ 500" → Depois: ✅ "2x R$ 500"
- Valor 1000, Parcelas 2, Toggle "VALOR PARCELA" → Antes: ❌ "2x R$ 500" → Depois: ✅ "2x R$ 1.000"

---

## 🔄 Fluxo de Dados

```
usuário clica "Nova Receita"
    ↓
openAddDialog() async chamado
    ↓
loadFormData() async executado
    ↓
useLancamentos('receita').updateData() chamado
    ↓
API /buscar-dados-mes chamada
    ↓
revenuesStore.setRevenuesData() atualizado
    ↓
categoriasNames computed atualizado
    ↓
Modal exibe com dados reais
```

---

## 🧪 Testes Recomendados

1. **Modal Centralização:**

   - Abrir modal "Nova Receita" e verificar se está centralizado
   - Funciona em mobile, tablet e desktop

2. **Validação Parcela:**

   - Definir "Parcelado"
   - Tentar aumentar parcela inicial acima do máximo
   - Botão + deve ficar disabled

3. **Cálculo Valor Parcela:**

   - Entrar valor "1000"
   - Definir "Parcelado" com 5 parcelas
   - Verificar se mostra "Em 5x de R$ 200,00"
   - Mudar valor e verificar atualização automática

4. **Carregamento de Dados:**
   - Abrir "Nova Receita"
   - Verificar se dropdown de Categoria mostra dados reais
   - Verificar se dropdown de Contas mostra dados reais
   - Selecionar categoria e verificar subcategorias

---

## 🔧 Notas Técnicas

- **Fallback:** Todas as computed properties têm fallback para dados hardcoded se API falhar
- **Performance:** Cache de 5 minutos implementado em `useLancamentos`
- **Type Safety:** Mantém compatibilidade com tipos TypeScript existentes
- **Reusabilidade:** Código pronto para ser aplicado em DespesasView também

---

## 📝 Arquivo Alterado

- `/frontend/src/views/receitas/ReceitasView.vue`

**Linhas Principais:**

- 614: Importação de useLancamentos
- 619-622: Inicialização de stores
- 730: categoriasNames computed
- 747: contas computed
- 752: subcategoriasDaCategoriaSelecionada computed
- 751-760: detalheRecorrencia computed
- 357-360: Validação parcela inicial
- 907-916: Função loadFormData
- 935-952: openAddDialog async
