# 🚀 Criação de Lançamentos - Implementação

## 📋 Resumo

Implementada integração completa com API para criar, editar e deletar receitas em ReceitasView.

---

## ✅ Mudanças Implementadas

### 1. Função `saveReceita()` - CRIAR/EDITAR com API

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 977-1026)

```typescript
const saveReceita = async () => {
  loading.value = true;
  try {
    // Validar formulário
    if (!formRef.value?.validate()) {
      throw new Error("Preencha todos os campos obrigatórios");
    }

    // Converter valor de string formatada para centavos
    const valorEmReais = parseFloat(
      formData.value.valor.replace(/\./g, "").replace(",", ".")
    );
    const valorEmCentavos = Math.round(valorEmReais * 100);

    const payload = {
      descricao: formData.value.descricao,
      valor: valorEmCentavos, // ✅ Valor em centavos para API
      categoria: formData.value.categoria,
      subcategoria: formData.value.subcategoria || "Outros",
      conta_id: formData.value.conta_id,
      data_vencimento: formData.value.data_vencimento,
      data_lancamento: formData.value.data_lancamento,
      data_efetivacao: formData.value.data_efetivacao,
      status_lancamento: formData.value.status_lancamento,
      observacoes: formData.value.observacoes,
      recorrencia: formData.value.recorrencia,
      tipo_lancamento: "Receita",
    };

    // Se for parcelado, adicionar dados de parcelas
    if (formData.value.recorrencia === "Parcelado") {
      Object.assign(payload, {
        qtd_parcelas: tempNumParcelas.value,
        num_parcela: tempParcelaInicial.value,
        tipo_parcela: tipoCalculoParcela.value,
        periodicidade: tempPeriodicidade.value,
      });
    }

    if (editingId.value) {
      // ATUALIZAR
      await receitasService.update(editingId.value, payload);
      toastStore.showSuccess("Receita atualizada com sucesso!");
    } else {
      // CRIAR
      await receitasService.create(payload);
      toastStore.showSuccess("Receita criada com sucesso!");
    }

    // Fechar modal e recarregar dados
    dialog.value = false;
    await loadReceitas();
  } catch (error: any) {
    console.error("Erro ao salvar receita:", error);
    toastStore.showError(error.message || "Erro ao salvar receita");
  } finally {
    loading.value = false;
  }
};
```

**Fluxo:**

1. ✅ Valida formulário
2. ✅ Converte valor de string formatada (com . e ,) para centavos (número)
3. ✅ Monta payload com todos os dados
4. ✅ Se parcelado, adiciona dados de parcelas
5. ✅ Chama API para criar ou atualizar
6. ✅ Mostra mensagem de sucesso
7. ✅ Recarrega lista de receitas
8. ✅ Fecha modal

---

### 2. Função `deleteReceita()` - DELETAR com API

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 972-985)

```typescript
const deleteReceita = async (id: number) => {
  if (confirm("Tem certeza que deseja deletar esta receita?")) {
    try {
      loading.value = true;
      await receitasService.delete(id);
      toastStore.showSuccess("Receita deletada com sucesso!");
      await loadReceitas();
    } catch (error: any) {
      console.error("Erro ao deletar receita:", error);
      toastStore.showError(error.message || "Erro ao deletar receita");
    } finally {
      loading.value = false;
    }
  }
};
```

**Fluxo:**

1. ✅ Pede confirmação
2. ✅ Chama API para deletar
3. ✅ Mostra mensagem de sucesso
4. ✅ Recarrega lista de receitas

---

### 3. Função `loadReceitas()` - CARREGAR com Tratamento de Dados

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 1055-1089)

```typescript
const loadReceitas = async () => {
  try {
    loading.value = true;
    const mesAno = userStore.getMesAno?.();
    const data = await receitasService.list(mesAno);

    if (data && data.length > 0) {
      receitas.value = data.map((r: any) => ({
        id: r.id,
        descricao: r.descricao,
        valor: r.valor || 0, // ✅ Valor em centavos da API
        categoria: r.categoria || "Outros",
        subcategoria: r.subcategoria || "Outros",
        conta: r.conta?.name || "Conta",
        conta_id: r.conta_id,
        data_vencimento: r.data_vencimento,
        status: r.status_lancamento === "EFETIVADA" ? "recebida" : "pendente",
        observacao: r.observacao || "",
        recorrencia: r.recorrencia || "Não recorrente",
        status_lancamento: r.status_lancamento || "PENDENTE",
        data_lancamento: r.data_lancamento,
        data_efetivacao: r.data_efetivacao,
        observacoes: r.observacoes || "",
      }));
    } else {
      receitas.value = [];
    }
  } catch (error: any) {
    console.warn("Erro ao carregar receitas:", error?.message);
    toastStore.showWarning("Erro ao carregar receitas");
  } finally {
    loading.value = false;
  }
};
```

**Fluxo:**

1. ✅ Obtém período (mesAno) do store de usuário
2. ✅ Chama API para listar receitas
3. ✅ Mapeia dados da API com fallback para valores padrão
4. ✅ Armazena em receitas.value para exibição

---

## 🔄 Fluxo de Dados

### Criar Nova Receita

```
Usuário clica "Nova Receita"
        ↓
openAddDialog() preenchido
        ↓
Usuário preenche formulário
        ↓
Clica "Adicionar"
        ↓
saveReceita() chamado
        ↓
Validação ✅
        ↓
Converte valor: "1.000,50" → 100050 (centavos)
        ↓
Monta payload com todos campos
        ↓
receitasService.create(payload) chamado
        ↓
POST /api/lancamentos enviado
        ↓
✅ Sucesso: "Receita criada com sucesso!"
        ↓
loadReceitas() recarrega lista
        ↓
Modal fecha, tabela atualizada
```

### Editar Receita

```
Usuário clica ✏️ em receita
        ↓
editReceita(receita) chamado
        ↓
formData preenchido com dados da receita
        ↓
Modal abre
        ↓
Usuário altera campos
        ↓
Clica "Atualizar"
        ↓
saveReceita() chamado (editingId.value != null)
        ↓
receitasService.update(id, payload) chamado
        ↓
PUT /api/lancamentos/{id} enviado
        ↓
✅ Sucesso: "Receita atualizada com sucesso!"
        ↓
loadReceitas() recarrega
```

### Deletar Receita

```
Usuário clica 🗑️ em receita
        ↓
deleteReceita(id) chamado
        ↓
Confirmação: "Tem certeza?"
        ↓
receitasService.delete(id) chamado
        ↓
DELETE /api/lancamentos/{id} enviado
        ↓
✅ Sucesso: "Receita deletada com sucesso!"
        ↓
loadReceitas() recarrega
```

---

## 📊 Mapeamento de Campos

| Campo Formulário  | Campo API         | Tipo         | Observações                           |
| ----------------- | ----------------- | ------------ | ------------------------------------- |
| descricao         | descricao         | string       | Obrigatório                           |
| valor             | valor             | number       | Centavos (÷100 na exibição)           |
| categoria         | categoria         | string       | Obrigatório                           |
| subcategoria      | subcategoria      | string       | Padrão: "Outros"                      |
| conta_id          | conta_id          | number       | Obrigatório                           |
| data_vencimento   | data_vencimento   | date         | Formato: YYYY-MM-DD                   |
| data_lancamento   | data_lancamento   | date         | Formato: YYYY-MM-DD                   |
| data_efetivacao   | data_efetivacao   | date \| null | Formato: YYYY-MM-DD                   |
| status_lancamento | status_lancamento | string       | "EFETIVADA" \| "PENDENTE"             |
| observacoes       | observacoes       | string       | Opcional                              |
| recorrencia       | recorrencia       | string       | "Não recorrente", "Fixa", "Parcelado" |

### Campos de Parcelado (Quando recorrencia === "Parcelado")

| Campo              | Campo API     | Tipo                 |
| ------------------ | ------------- | -------------------- |
| tempNumParcelas    | qtd_parcelas  | number               |
| tempParcelaInicial | num_parcela   | number               |
| tipoCalculoParcela | tipo_parcela  | "total" \| "parcela" |
| tempPeriodicidade  | periodicidade | string               |

---

## 🧮 Conversão de Valores

### Entrada (Formulário)

```
Input formatado: "1.000,50"
        ↓
Remove pontos: "1000,50"
        ↓
Substitui vírgula: "1000.50"
        ↓
parseFloat(): 1000.50
        ↓
Multiplica por 100: 100050
        ↓
Math.round(): 100050 (centavos)
        ↓
Envia API: 100050
```

### Saída (Tabela)

```
API retorna: 100050 (centavos)
        ↓
formatCurrency(100050)
        ↓
Divide por 100: 1000.50
        ↓
Formata moeda: "R$ 1.000,50"
        ↓
Exibe na tabela
```

---

## ✅ Serviços Utilizados

- **receitasService.create(data)** - Criar receita
- **receitasService.update(id, data)** - Atualizar receita
- **receitasService.delete(id)** - Deletar receita
- **receitasService.list(mesAno)** - Listar receitas por mês
- **toastStore.showSuccess()** - Notificação sucesso
- **toastStore.showError()** - Notificação erro
- **toastStore.showWarning()** - Notificação aviso
- **userStore.getMesAno()** - Obter período atual

---

## 🧪 Testes Recomendados

### Teste 1: Criar Receita Simples

- [ ] Descrição: "Teste Receita"
- [ ] Valor: "1.000,00"
- [ ] Categoria: "Salário"
- [ ] Conta: "Conta Principal"
- [ ] Data: Hoje
- [ ] Clicar "Adicionar"
- [ ] ✅ Verificar mensagem de sucesso
- [ ] ✅ Verificar receita na tabela

### Teste 2: Criar Receita Parcelada

- [ ] Descrição: "Teste Parcelado"
- [ ] Valor: "3.000,00"
- [ ] Recorrência: "Parcelado"
- [ ] Parcelas: 3
- [ ] Toggle: "VALOR TOTAL"
- [ ] Clicar "Adicionar"
- [ ] ✅ Verificar se criou com parcelas

### Teste 3: Editar Receita

- [ ] Clicar ✏️ em receita existente
- [ ] Alterar descrição: "Editado"
- [ ] Clicar "Atualizar"
- [ ] ✅ Verificar mensagem de sucesso
- [ ] ✅ Verificar mudança na tabela

### Teste 4: Deletar Receita

- [ ] Clicar 🗑️ em receita
- [ ] Confirmar deleção
- [ ] ✅ Verificar mensagem de sucesso
- [ ] ✅ Verificar que saiu da tabela

### Teste 5: Validação de Campos

- [ ] Tenta salvar sem descrição
- [ ] ✅ Mostra erro: "Campo obrigatório"
- [ ] Tenta salvar com valor 0
- [ ] ✅ Mostra erro: "Valor deve ser maior que zero"
- [ ] Tenta salvar sem categoria
- [ ] ✅ Mostra erro: "Campo obrigatório"

### Teste 6: Carregamento de Dados

- [ ] Recarrega página
- [ ] ✅ Receitas são carregadas da API
- [ ] ✅ Valores formatados corretamente
- [ ] ✅ Totais dos cartões atualizados

---

## 🚀 Próximos Passos

1. ✅ Implementar em **DespesasView** (código idêntico)
2. ✅ Implementar em **FormCartãoCredito** (se necessário)
3. Testes de integração completa
4. Validar transações no backend
