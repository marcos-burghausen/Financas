# Adição de Campo "Conta Vinculada" no CartaoCreditoView

## 📋 Resumo das Alterações

Foi implementada a funcionalidade de vincular contas aos cartões de crédito, permitindo que cada cartão tenha uma conta pai associada e herde sua cor automaticamente.

## 🔧 Mudanças Realizadas

### 1. **Importações Adicionadas**

- `import contasService from '@/services/contas.service'`
- `import { getBankIcon } from '@/utils/iconMapper'`

### 2. **Novas Interfaces**

```typescript
interface Conta {
  id: number;
  name: string;
  icon?: string;
  color?: string;
  tipo_conta?: string;
}
```

### 3. **Nova Propriedade na Interface Cartao**

```typescript
conta_pai_id?: number | null
conta_pai_name?: string | null
```

### 4. **Novo Estado**

```typescript
const contas = ref<Conta[]>([]); // Lista de contas disponíveis
const menuContaPai = ref(false); // Controle do menu de seleção
```

### 5. **Novos Computed Properties**

```typescript
// Conta pai vinculada atualmente selecionada
const contaPaiSelecionada = computed(() => {
  if (!editingData.value.conta_pai_id) return null;
  return contas.value.find((c) => c.id === editingData.value.conta_pai_id);
});

// Cor da conta pai (herdada automaticamente)
const corContaPai = computed(() => {
  return contaPaiSelecionada.value?.color || "#e53935";
});
```

### 6. **Novo Método `loadContas()`**

```typescript
const loadContas = async () => {
  try {
    const mesAno = currentMonth.value;
    const data = await contasService.list(mesAno);
    // Filtrar apenas contas, não cartões
    contas.value = data.filter(
      (c) => c.tipo_conta && !c.tipo_conta.toLowerCase().includes("cartão")
    );
    console.log("Contas carregadas:", contas.value);
  } catch (error: any) {
    console.error("Erro ao carregar contas:", error);
  }
};
```

### 7. **Atualização do Lifecycle**

- `onMounted`: Adicionado `loadContas()`
- `watch`: Adicionado `loadContas()` no watch do mês

### 8. **Alteração no Campo "Apelido do Cartão"**

- **Antes**: Exibia um color picker no slot `append-inner`
- **Depois**: Exibe apenas o ícone da conta vinculada (sem permitir edição)
- O ícone é obtido através da função `getBankIcon()` baseado no campo `icon` da conta

### 9. **Novo Campo de Seleção "Conta Vinculada"**

Adicionado novo `v-select` com as seguintes características:

- Label: "Conta Vinculada"
- Items: Lista de contas filtradas
- Template de seleção: Exibe ícone + nome da conta
- Template de item: Exibe ícone + nome em lista
- Validação: Campo obrigatório
- Variant: underlined (consistente com design existente)

### 10. **Atualização do Método `saveCartao()`**

```typescript
function saveCartao() {
  // Validações
  if (!editingData.value.name) {
    toastStore.error("Nome do cartão é obrigatório");
    return;
  }

  // Nova validação: conta pai é obrigatória
  if (!editingData.value.conta_pai_id) {
    toastStore.error("Selecione uma conta vinculada");
    return;
  }

  // Usar automaticamente a cor da conta pai
  const cartaoData = {
    ...editingData.value,
    color: corContaPai.value,
    conta_pai_name: contaPaiSelecionada.value?.name,
  };

  // ... resto da lógica
}
```

### 11. **Atualização do Estado Inicial**

- `openAddDialog()`: Adicionado `conta_pai_id: null`
- `closeDialog()`: Adicionado `conta_pai_id: null`

## 🎨 Mudanças de UI/UX

### Campo "Apelido do Cartão"

- **Antes**: Color picker circulante no final do campo
- **Depois**: Ícone da conta vinculada (apenas exibição)
- O color picker foi removido, a cor agora é automática baseada na conta

### Novo Campo "Conta Vinculada"

- Posicionado entre "Apelido" e "Limite"
- Exibe seletor com ícone + nome da conta
- Campo obrigatório (validado com `rules.required`)
- Utiliza `getBankIcon()` para exibir ícones consistentes com o sistema

## ✅ Benefícios

1. **Organização**: Cada cartão vinculado a uma conta específica
2. **Consistência Visual**: Cor do cartão herda da conta pai
3. **Rastreabilidade**: Fácil identificar qual conta cada cartão pertence
4. **Filtração**: Possibilita filtrar cartões por conta vinculada (para futuras implementações)
5. **Documentação**: Campo `conta_pai_name` facilita referência sem pesquisar

## 📝 Validações Implementadas

- ✅ Nome do cartão é obrigatório
- ✅ Conta vinculada é obrigatória
- ✅ Apenas contas (não cartões) são listadas
- ✅ Cor herdada automaticamente da conta pai

## 🔄 Fluxo de Uso

1. Usuário clica em "Novo Cartão"
2. Form abre com campo "Conta Vinculada" vazio
3. Usuário seleciona uma conta da lista
4. Ícone da conta aparece no campo "Apelido"
5. Usuário preenche demais campos (nome, limite, bandeira, etc)
6. Ao salvar, cor é aplicada automaticamente da conta
7. Campo `conta_pai_name` é preenchido para referência

## 🧪 Testes Recomendados

- [ ] Criar novo cartão e verificar se requer conta vinculada
- [ ] Verificar se ícone da conta aparece após seleção
- [ ] Editar cartão e manter seleção de conta
- [ ] Deletar cartão vinculado e criar novo
- [ ] Verificar responsividade no mobile
- [ ] Testar com diferentes tipos de contas (corrente, poupança, etc)

## 📦 Dependências

- Serviço: `contasService.list()`
- Utility: `getBankIcon()`
- Store: `toastStore.error()`
