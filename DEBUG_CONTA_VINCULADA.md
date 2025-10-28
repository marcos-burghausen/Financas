# Debug: Conta Vinculada não exibindo contas

## 🔍 Problema Identificado

O campo "Conta Vinculada" não está mostrando as contas do usuário no dropdown.

## 🔧 Causas Identificadas

### 1. **Campo `tipo_conta` não estava sendo mapeado no serviço**

**Arquivo**: `/frontend/src/services/contas.service.ts`

O método `list()` estava retornando contas mas **não incluía o campo `tipo_conta`**, que é essencial para o filtro no CartaoCreditoView.

```typescript
// ANTES - Faltava tipo_conta e icon
return contasData.map((c: any) => ({
  id: c.id,
  color: c.color || "#163dc0",
  name: c.name,
  // ... outros campos
  // ❌ FALTAVA: tipo_conta: c.tipo_conta
  // ❌ FALTAVA: icon: c.icon
}));

// DEPOIS - Incluindo tipo_conta e icon
return contasData.map((c: any) => ({
  id: c.id,
  color: c.color || "#163dc0",
  name: c.name,
  icon: c.icon || "", // ✅ Adicionado
  tipo_conta: c.tipo_conta || "", // ✅ Adicionado para filtro
  // ... outros campos
}));
```

### 2. **Filtro estava silenciosamente falhando**

O filtro no `loadContas()` estava:

```typescript
contas.value = data.filter(
  (c) => c.tipo_conta && !c.tipo_conta.toLowerCase().includes("crédito")
);
```

Como `tipo_conta` era `undefined`, o filtro retornava array vazio silenciosamente.

## ✅ Solução Implementada

### 1. **Adicionar mapeamento de `tipo_conta` e `icon` no serviço**

```typescript
// contas.service.ts - método list()
return contasData.map((c: any) => ({
  id: c.id,
  color: c.color || "#163dc0",
  name: c.name,
  number: c.number || "",
  agency: c.agency || "",
  bank: c.bank || "Banco",
  icon: c.icon || "", // ✅ NOVO
  tipo_conta: c.tipo_conta || "", // ✅ NOVO
  type: c.tipo_conta?.toLowerCase().includes("poupança")
    ? "poupanca"
    : c.tipo_conta?.toLowerCase().includes("investimento")
    ? "investimento"
    : "corrente",
  balance: c.saldo || 0,
  limit: c.limite || 0,
  status: c.ativo === false || c.status === "inativa" ? "inativa" : "ativa",
  description: c.descricao || "",
  opening_date: c.data_abertura || "",
}));
```

### 2. **Melhorar debug no CartaoCreditoView**

```typescript
const loadContas = async () => {
  try {
    const mesAno = currentMonth.value;
    const data = await contasService.list(mesAno);

    console.log("=== DEBUG loadContas ===");
    console.log("Todas as contas recebidas:", data);
    console.log("Contagem de contas:", data.length);

    // Filtrar com debug detalhado
    const contasFiltradas = data.filter((c) => {
      const tipo = c.tipo_conta?.toLowerCase() || "";
      const isNotCreditCard =
        !tipo.includes("crédito") && !tipo.includes("credit");
      console.log(
        `Conta: ${c.name} | Tipo: "${c.tipo_conta}" | Include: ${isNotCreditCard}`
      );
      return isNotCreditCard;
    });

    contas.value = contasFiltradas;
    console.log("Contas filtradas (final):", contas.value);
    console.log("=== FIM DEBUG ===");
  } catch (error: any) {
    console.error("Erro ao carregar contas:", error);
    toastStore.error("Erro ao carregar contas");
  }
};
```

## 🧪 Como Testar

### Passo 1: Abrir DevTools do Navegador

1. Abrir aplicação em `http://localhost:5173` (ou sua URL)
2. Pressionar `F12` para abrir DevTools
3. Ir para a aba **Console**

### Passo 2: Navegar para Cartões de Crédito

1. Clique em "Meus Cartões de Crédito"
2. Veja os logs no console:

```
=== DEBUG loadContas ===
Todas as contas recebidas: Array(3)
Contagem de contas: 3
Conta: Conta Corrente | Tipo: "Corrente" | Include: true
Conta: Conta Poupança | Tipo: "Poupança" | Include: true
Conta: Meu Cartão | Tipo: "Cartão de Crédito" | Include: false
Contas filtradas (final): Array(2)
  0: {id: 1, name: "Conta Corrente", icon: "mdi-bank", tipo_conta: "Corrente", color: "#163dc0", ...}
  1: {id: 2, name: "Conta Poupança", icon: "mdi-piggy-bank", tipo_conta: "Poupança", color: "#163dc0", ...}
=== FIM DEBUG ===
```

### Passo 3: Testar o Dropdown

1. Clique em "Novo Cartão"
2. Clique no campo "Conta Vinculada"
3. ✅ Deve mostrar: "Conta Corrente" e "Conta Poupança"
4. ✅ Deve mostrar ícones dos bancos
5. ✅ Ao selecionar, deve atualizar cor automaticamente

## 📊 Estrutura de Dados Esperada

### Interface Conta (antes e depois)

```typescript
interface Conta {
  id: number;
  name: string;
  icon?: string; // ✅ AGORA INCLUÍDO
  color?: string;
  number?: string;
  agency?: string;
  bank?: string;
  type?: "corrente" | "poupanca" | "investimento";
  balance?: number;
  limit?: number;
  status?: "ativa" | "inativa";
  description?: string | null;
  opening_date?: string;
  saldo_inicial?: string;
  incluir_em_soma_inicial?: boolean;
  tipo_conta?: string; // ✅ AGORA INCLUÍDO
  conta_pai_id?: number | null;
  dia_fechamento?: number | null;
  dia_vencimento?: number | null;
}
```

## 🔄 Fluxo de Dados (Após Fix)

```
1. onMounted() → loadContas()
   ↓
2. contasService.list(mesAno)
   → GET /wallet
   ↓
3. API retorna: { data: { wallets: { contas: [...] } } }
   ↓
4. Mapeamento (agora com tipo_conta e icon):
   contas = [
     { id: 1, name: "Corrente", tipo_conta: "Corrente", icon: "...", ... },
     { id: 2, name: "Poupança", tipo_conta: "Poupança", icon: "...", ... },
   ]
   ↓
5. Filtro remove cartões de crédito:
   contas = [
     { id: 1, name: "Corrente", tipo_conta: "Corrente", ... },
     { id: 2, name: "Poupança", tipo_conta: "Poupança", ... },
   ]
   ↓
6. v-select renderiza com :items="contas"
   ✅ Dropdown exibe as contas
```

## 📁 Arquivos Modificados

1. **`/frontend/src/services/contas.service.ts`**

   - Linha: ~44
   - Adicionado: `icon: c.icon || ''`
   - Adicionado: `tipo_conta: c.tipo_conta || ''`

2. **`/frontend/src/views/cartaoCredito/CartaoCreditoView.vue`**
   - Linha: ~622
   - Melhorado: Debug logs detalhados
   - Melhorado: Filtro com console.log por conta

## ✨ Resultado Esperado

Após essas mudanças:

✅ **Contas visíveis no dropdown**: O campo "Conta Vinculada" exibirá todas as contas do usuário (exceto cartões de crédito)

✅ **Ícones exibidos**: Cada conta mostrará seu ícone correspondente

✅ **Cores herdadas**: Ao selecionar uma conta, a cor do cartão será atualizada automaticamente

✅ **Debug fácil**: Os logs no console ajudam a ver o que está acontecendo

## 🚀 Próximas Ações

1. ✅ Abrir DevTools e testar os logs
2. ✅ Verificar se o dropdown exibe as contas
3. ✅ Testar seleção de conta
4. ✅ Verificar herança de cor
5. ✅ Remover os logs de debug após confirmação

---

**Status**: ✅ PRONTO PARA TESTES
