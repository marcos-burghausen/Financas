# 🎉 Funcionalidades Completas de Recorrência - CartaoCreditoView

## ✅ Implementação Concluída

Todas as funcionalidades de recorrência do `DespesasView` foram migradas e integradas no formulário de lançamentos do `CartaoCreditoView`.

---

## 📋 Funcionalidades Implementadas

### 1. **Seletor de Recorrência (Modal)**

- 3 opções: "Não recorrente", "Fixa", "Parcelado"
- Visual com radiobox/checkbox
- Abre automaticamente modal de parcelas quando "Parcelado" é selecionado

```typescript
// Refs
const openRecorrenciaModalTransaction = ref(false);
const tiposRecorrencia = ref(["Não recorrente", "Fixa", "Parcelado"]);

// Função
function selecionarRecorrenciaTransaction(item: string);
```

---

### 2. **Modal de Parcelas (Parcelado)**

Configuração completa com 4 campos:

#### **A. Parcela Inicial**

- Input numérico com botões +/-
- Range: 1 a quantidade total de parcelas
- Buttons desabilitam automaticamente nos limites

#### **B. Quantidade de Parcelas**

- Input numérico com botões +/-
- Mínimo: 2 parcelas
- Máximo: ilimitado
- Recalcula automaticamente o valor da parcela

#### **C. Periodicidade**

- Select com 4 opções: "Mensal", "Semanal", "Quinzenal", "Bimestral"
- Enviada ao backend em MAIÚSCULAS: "MENSAL", "SEMANAL", etc.

#### **D. Botões de Ação**

- "Cancelar" - fecha o modal sem salvar
- "Concluído" - fecha o modal e mantém configurações

```typescript
// Refs de controle
const openParcelasTransaction = ref(false);
const tempParcelaInicialTransaction = ref(1);
const tempNumParcelasTransaction = ref(2);
const tempPeriodicidadeTransaction = ref("Mensal");

// Função
function concluirParcelasTransaction();
```

---

### 3. **Toggle de Cálculo de Valor**

Aparece apenas quando "Parcelado" é selecionado:

- **Valor total**: Divide o valor informado pela quantidade de parcelas
- **Valor parcela**: O valor informado é o de UMA parcela

```typescript
const tipoCalculoParcelaTransaction = ref("total");

// Exibição no template:
// v-if="transactionData.recorrencia === 'Parcelado'"
```

---

### 4. **Detalhe da Recorrência**

Exibe resumo dinâmico abaixo do seletor quando "Parcelado":

**Formato**: `"3x de R$ 50,00"`

```typescript
// Computed
const detalheRecorrenciaTransaction = computed(() => {
  if (transactionData.value.recorrencia === "Parcelado" && ...) {
    return `${tempNumParcelasTransaction.value}x de R$ ${valorParcela.toFixed(2).replace(".", ",")}`
  }
  return null
})
```

---

## 🔧 Integração com Payload Backend

### Campos Adicionados ao Payload:

```typescript
const payload = {
  // Campos existentes...
  recorrencia: recorrenciaMap[transactionData.value.recorrencia], // NAO_RECORRENTE | FIXA | PARCELADO

  // Novos campos de parcelas (quando Parcelado)
  qtd_parcelas: tempNumParcelasTransaction.value,
  num_parcela: tempParcelaInicialTransaction.value,
  tipo_parcela: tipoCalculoParcelaTransaction.value?.toLowerCase(), // "total" ou "parcela"
  periodicidade: tempPeriodicidadeTransaction.value?.toUpperCase(), // "MENSAL", "SEMANAL", etc.

  // Nulo quando não é Parcelado
  qtd_parcelas: null,
  num_parcela: null,
  tipo_parcela: null,
  periodicidade: null,
};
```

### Mapeamento de Recorrência:

```typescript
const recorrenciaMap = {
  "Não recorrente": "NAO_RECORRENTE",
  Fixa: "FIXA",
  Parcelado: "PARCELADO",
};
```

---

## 🎨 Componentes de UI/UX

### **Custom Input Container**

- Classe: `.custom__input__container`
- Contém ícone, label, espaçador e ícone de editar (quando Parcelado)
- Underline estilizado: `.custom__underline`
- Hover effect melhorado

### **Ícones Utilizados**

- **Recorrência**: `mdi-refresh`
- **Editar Parcelas**: `mdi-pencil` (icon-small, aparece ao lado)

### **Estilos Aplicados**

```scss
.custom__input__content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  cursor: pointer;

  &:hover {
    background: rgba(0, 0, 0, 0.02);
  }
}

.edit__icon {
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    color: rgb(var(--v-theme-primary));
    transform: scale(1.2);
  }
}

.detalhe__parcela__interno {
  font-size: 0.75rem;
  color: rgba(0, 0, 0, 0.6);
  font-weight: 500;
}

:deep(.parcela__toggle) {
  width: 100%;
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.04);

  .toggle__btn {
    flex: 1;
    border-radius: 6px;
  }
}
```

---

## 📐 Layout do Formulário

```
Row 1: Descrição
Row 2: Valor
Row 3: Recorrência (Custom Input + Modal)
        ├─ Detalhe Parcela (se Parcelado)
        └─ Toggle Valor Total/Parcela (se Parcelado)
Row 4: Cartão de Crédito (Display)
Row 5: Fatura (Select 25 meses)
Row 6: Categoria | Subcategoria
Row 7: Conta (Display não-editável)
Row 8: Observações
```

---

## 🔄 Fluxo de Uso

### **Cenário 1: Não Recorrente**

1. Usuário clica em "Recorrência"
2. Modal mostra 3 opções
3. Seleciona "Não recorrente"
4. Modal fecha
5. Recorrência fica: "Não recorrente"
6. Nenhum campo extra aparece

### **Cenário 2: Fixa**

1. Usuário clica em "Recorrência"
2. Seleciona "Fixa"
3. Modal fecha
4. Recorrência fica: "Fixa"
5. Nenhum campo extra aparece
6. Dados de parcela não são enviados (null)

### **Cenário 3: Parcelado**

1. Usuário clica em "Recorrência"
2. Seleciona "Parcelado"
3. Modal de recorrência fecha
4. **Modal de Parcelas abre automaticamente**
5. Usuário configura:
   - Parcela inicial: 1
   - Quantidade: 3
   - Periodicidade: "Mensal"
6. Clica "Concluído"
7. Recorrência exibe: "Parcelado" + detalhe "3x de R$ 50,00"
8. Toggle de "Valor total/Valor parcela" aparece
9. Usuário seleciona "Valor total" ou "Valor parcela"
10. Ao salvar, envia todos os dados de parcelas

---

## 🔌 Endpoints Esperados

### POST `/lancamentos`

**Headers:**

```
Content-Type: application/json
Authorization: Bearer {token}
```

**Body Completo (Parcelado):**

```json
{
  "cartao_credito_id": 1,
  "descricao": "Compra em loja X",
  "valor": "150,00",
  "categoria": "Compras",
  "subcategoria": "Roupas",
  "conta_id": 5,
  "recorrencia": "PARCELADO",
  "fatura_vigente": "12/2024",
  "data_vencimento": "2024-12-15",
  "data_lancamento": "2024-11-28",
  "data_efetivacao": null,
  "observacoes": "Compra parcelada",
  "tipo_lancamento": "CARTAO_CREDITO",
  "status_lancamento": "PENDENTE",
  "qtd_parcelas": 3,
  "num_parcela": 1,
  "tipo_parcela": "total",
  "periodicidade": "MENSAL"
}
```

**Body (Não Recorrente):**

```json
{
  // ... campos acima, mas:
  "recorrencia": "NAO_RECORRENTE",
  "qtd_parcelas": null,
  "num_parcela": null,
  "tipo_parcela": null,
  "periodicidade": null
}
```

---

## 📝 Refs Adicionados

```typescript
// Recurrence State
const openRecorrenciaModalTransaction = ref(false);
const openParcelasTransaction = ref(false);
const tiposRecorrencia = ref(["Não recorrente", "Fixa", "Parcelado"]);
const tipoCalculoParcelaTransaction = ref("total");
const tempParcelaInicialTransaction = ref(1);
const tempNumParcelasTransaction = ref(2);
const tempPeriodicidadeTransaction = ref("Mensal");
```

---

## 📦 Computed Properties Adicionados

```typescript
// Detalhe da recorrência para transactions (parcelado)
const detalheRecorrenciaTransaction = computed(() => {
  if (
    transactionData.value.recorrencia === "Parcelado" &&
    transactionData.value.valor &&
    tempNumParcelasTransaction.value > 0
  ) {
    const valorInput = parseFloat(
      transactionData.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (!isNaN(valorInput) && valorInput > 0) {
      let valorParcela: number;

      if (tipoCalculoParcelaTransaction.value === "total") {
        valorParcela = valorInput / tempNumParcelasTransaction.value;
      } else {
        valorParcela = valorInput;
      }

      return `${tempNumParcelasTransaction.value}x de R$ ${valorParcela
        .toFixed(2)
        .replace(".", ",")}`;
    }
  }
  return null;
});
```

---

## 🔨 Funções Adicionadas

```typescript
// Seleciona tipo de recorrência e abre modal de parcelas se necessário
function selecionarRecorrenciaTransaction(item: string);

// Fecha modal de parcelas
function concluirParcelasTransaction();

// Reseta state de recorrência ao fechar dialog de lançamento
function closeTransactionDialog() {
  // ... agora inclui reset dos refs de recorrência
}
```

---

## ✨ Diferenciais

✅ **Sincronização automática**: Ao mudar a quantidade de parcelas, o detalhe se atualiza em tempo real

✅ **Validação de limites**: Botões +/- desabilitam quando atingem os limites

✅ **Conversão automática**: Valores são convertidos corretamente entre "total" e "parcela"

✅ **Reset automático**: Ao fechar o dialog, todos os refs de recorrência são resetados

✅ **UI/UX consistente**: Segue exatamente o padrão visual de DespesasView

✅ **Backend ready**: Payload completo e correto para API

---

## 🚀 Próximos Passos (Backend)

1. ✅ Endpoint `/lancamentos` já existe (precisa validar compatibilidade)
2. Garantir que aceita campos: `qtd_parcelas`, `num_parcela`, `tipo_parcela`, `periodicidade`
3. Testar criação de lançamentos parcelados com `cartao_credito_id`
4. Validar se recorrência é mapeada corretamente: `NAO_RECORRENTE`, `FIXA`, `PARCELADO`

---

## 📊 Status

| Funcionalidade      | Status      | Notas                                                         |
| ------------------- | ----------- | ------------------------------------------------------------- |
| Seletor Recorrência | ✅ Completo | Modal com 3 opções                                            |
| Modal de Parcelas   | ✅ Completo | 4 campos + validação                                          |
| Toggle Valor        | ✅ Completo | Total/Parcela                                                 |
| Detalhe Dinâmico    | ✅ Completo | Atualiza em tempo real                                        |
| Computeds           | ✅ Completo | detalheRecorrenciaTransaction                                 |
| Funções             | ✅ Completo | selecionarRecorrenciaTransaction, concluirParcelasTransaction |
| Payload             | ✅ Completo | Com mapeamento de recorrência                                 |
| Estilos CSS         | ✅ Completo | custom**input, parcela**toggle                                |
| Integração          | ✅ Completo | Pronto para testes                                            |

---

## 🎯 Conclusão

**CartaoCreditoView agora possui todas as funcionalidades de recorrência do DespesasView**, incluindo:

- ✅ Seleção de tipo de recorrência
- ✅ Configuração de parcelas com modal completo
- ✅ Toggle de cálculo (total vs parcela)
- ✅ Detalhe dinâmico de parcelas
- ✅ Payload correto para backend
- ✅ Estilos e UX melhorados

**Formulário 100% funcional e pronto para backend testing! 🚀**
