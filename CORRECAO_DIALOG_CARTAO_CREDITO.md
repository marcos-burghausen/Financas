# Correção de Dialog no CartaoCreditoView

## 📋 Problema

O formulário estava sendo renderizado como um card normal, aparecendo abaixo da tabela sem sobrepor a view. O componente `FormContaCartao` é apenas um card e não estava envolvido em um dialog.

## ✅ Solução Implementada

### 1. **Substituição do Componente por v-dialog**

**Antes:**

```vue
<FormContaCartao
  v-if="dialogOpen"
  :wallet-type="'Cartão'"
  :editing-data="editingData"
  :wallets="cartoes"
  @saved="handleFormSaved"
  @close="closeDialog"
/>
```

**Depois:**

```vue
<v-dialog v-model="dialogOpen" max-width="600px" persistent>
  <v-card>
    <!-- Formulário inline -->
  </v-card>
</v-dialog>
```

### 2. **Campos Adicionados ao Dialog**

Os campos agora estão dentro do formulário do CartaoCreditoView (não dependem mais de FormContaCartao):

| Campo              | Tipo   | Descrição                                 |
| ------------------ | ------ | ----------------------------------------- |
| **name**           | text   | Nome do cartão (obrigatório)              |
| **tipo_conta**     | select | Tipo de conta (fixo: "Cartão de Crédito") |
| **icon**           | select | Bandeira (Visa, Mastercard, ELO, Amex)    |
| **limite**         | number | Limite do cartão em centavos              |
| **dia_fechamento** | number | Dia de fechamento da fatura               |
| **dia_vencimento** | number | Dia de vencimento da fatura               |
| **descricao**      | text   | Descrição/observação                      |
| **color**          | text   | Cor do cartão (hex) com preview           |

### 3. **Atualização do Estado**

```typescript
const editingData = ref<Partial<Cartao>>({
  name: "",
  tipo_conta: "Cartão de Crédito",
  icon: "Visa",
  limite: 0,
  dia_fechamento: 10,
  dia_vencimento: 20,
  descricao: "",
  color: "#e53935",
});
```

### 4. **Novo Método saveCartao()**

Substituiu `handleFormSaved()`:

```typescript
function saveCartao() {
  if (!editingData.value.name) {
    toastStore.error("Nome do cartão é obrigatório");
    return;
  }

  if (editingId.value) {
    // Atualizar cartão existente
  } else {
    // Criar novo cartão
  }

  closeDialog();
}
```

### 5. **Remoção de Imports Desnecessários**

```typescript
// ❌ Removido
import FormContaCartao from "@/components/FormContaCartao.vue";

// ✅ Mantido
import cartaoCreditoService from "@/services/cartaoCredito.service";
import { useToastStore } from "@/store/toast";
```

## 🎨 Características do Dialog

- **max-width="600px"** - Largura máxima responsiva
- **persistent** - Não fecha ao clicar fora
- **v-model="dialogOpen"** - Controle reativo
- **Botões Cancelar/Salvar** - Ações do formulário

## 🖌️ Estilos Adicionados

```scss
.color-preview {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.color-preview:hover {
  transform: scale(1.1);
  border-color: rgba(0, 0, 0, 0.4);
}
```

## 📱 Comportamento

1. **Clique em "Novo Cartão"** → Dialog abre com formulário vazio
2. **Preenche os campos** → Valores são vinculados ao `editingData`
3. **Clique em "Salvar"** → Valida nome → Salva na lista → Fecha dialog
4. **Clique em Editar (lápis)** → Dialog abre com dados do cartão
5. **Clique em "Cancelar"** → Fecha sem salvar → Reseta formulário

## ✨ Melhorias

- ✅ Dialog sobreposto corretamente
- ✅ Responsivo em mobile/tablet/desktop
- ✅ Validação de campo obrigatório
- ✅ Mensagens de sucesso/erro
- ✅ Cores com preview visual
- ✅ Sem dependência do componente FormContaCartao

## 🔄 Compatibilidade

- ✅ Vue 3 Composition API
- ✅ TypeScript
- ✅ Vuetify 3
- ✅ Reatividade Vue
