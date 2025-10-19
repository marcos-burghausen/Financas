# 🚀 Implementação Final: Lançamentos Fixos e Parcelados

## ✅ Status

### Backend

- ✅ **LancamentoService.php** - Já tem toda lógica pronta
  - `createLancamentoFixoStandard()` - Cria 12 lançamentos (próximos 12 meses)
  - `createLancamentoParceladoStandard()` - Cria N lançamentos (parcelado)
  - `createLancamentoUnico()` - Cria 1 lançamento (não recorrente)
  - `atualizarSaldos()` - Atualiza saldo das contas

### Frontend

- ✅ **ReceitasView.vue** - Integrado com novo fluxo

  - Envia `recorrencia: "FIXA"` ou `"PARCELADO"` em MAIÚSCULAS
  - Para FIXA/PARCELADO: sempre CRIA novo (não atualiza)
  - Para NAO_RECORRENTE: pode atualizar

- ✅ **DespesasView.vue** - Integrado com novo fluxo
  - Mesmo padrão que ReceitasView

---

## 🔄 Fluxo Implementado

### Criar Receita FIXA

```
Usuário:
1. Clica "Nova Receita"
2. Preenche:
   - Descrição: "Salário"
   - Valor: "5000,00"
   - Recorrência: "Fixa"
   - Data: "2025-10-01"
3. Clica "Adicionar"
   ↓
Frontend (saveReceita):
1. Valida formulário ✓
2. Mapeia recorrência: "Fixa" → "FIXA"
3. Monta payload com recorrencia: "FIXA"
4. NÃO inclui ID (porque é FIXA)
5. Chama receitasService.create(payload)
   ↓
Backend (saveLancamento):
1. Valida payload
2. Chama lancamentoService.createLancamento()
   ↓
LancamentoService:
1. Vê recorrencia === 'FIXA'
2. Chama createLancamentoFixoStandard()
3. Cria 12 lançamentos:
   - 01/10: Salário (PENDENTE)
   - 01/11: Salário (PENDENTE)
   - 01/12: Salário (PENDENTE)
   - ... até 01/09/2026
4. Retorna sucesso
   ↓
Frontend:
- Toast: "✅ Receita criada com sucesso!"
- Recarrega receitas
- Tabela mostra 12 novas linhas
```

### Criar Receita PARCELADA

```
Usuário:
1. Clica "Nova Receita"
2. Preenche:
   - Descrição: "Compra"
   - Valor: "900,00"
   - Recorrência: "Parcelado"
   - Parcelas: 3
   - Toggle: "VALOR TOTAL"
   - Data: "2025-10-15"
3. Clica "Adicionar"
   ↓
Frontend (saveReceita):
1. Valida formulário ✓
2. Mapeia recorrência: "Parcelado" → "PARCELADO"
3. Adiciona campos de parcelado:
   - qtd_parcelas: 3
   - num_parcela: 1
   - tipo_parcela: "TOTAL"
   - periodicidade: "MENSAL"
4. NÃO inclui ID (porque é PARCELADO)
5. Chama receitasService.create(payload)
   ↓
Backend (saveLancamento):
1. Valida payload
2. Chama lancamentoService.createLancamento()
   ↓
LancamentoService:
1. Vê recorrencia === 'PARCELADO'
2. Chama createLancamentoParceladoStandard()
3. tipo_parcela === 'TOTAL' → divide 900/3 = 300
4. Cria 3 lançamentos:
   - 1/3 vence 2025-10-15, valor 300,00
   - 2/3 vence 2025-11-15, valor 300,00
   - 3/3 vence 2025-12-15, valor 300,00
5. Retorna sucesso
   ↓
Frontend:
- Toast: "✅ Receita criada com sucesso!"
- Recarrega receitas
- Tabela mostra 3 novas linhas
```

### Editar Receita NAO_RECORRENTE

```
Usuário:
1. Clica ✏️ em receita simples
2. Modal abre com dados preenchidos
3. Altera valor: "100,00"
4. Clica "Atualizar"
   ↓
Frontend (saveReceita):
1. Detecta editingId existe E recorrencia === "Não recorrente"
2. Inclui ID no payload
3. Chama receitasService.update(id, payload)
   ↓
Backend (editLancamento):
1. Encontra lançamento por ID
2. Atualiza com os dados do payload
3. Retorna sucesso
   ↓
Frontend:
- Toast: "✅ Receita atualizada com sucesso!"
- Recarrega receitas
- Tabela atualiza com novo valor
```

### Editar Receita FIXA ou PARCELADO

```
Usuário:
1. Clica ✏️ em receita FIXA (que criou 12 parcelas)
2. Modal abre com dados preenchidos
3. Altera valor: "5500,00"
4. Clica "Atualizar"
   ↓
Frontend (saveReceita):
1. Detecta editingId existe MAS recorrencia === "Fixa"
2. NÃO inclui ID no payload
3. Chama receitasService.create(payload)
   - Cria 12 NOVOS lançamentos com 5500,00
4. Chama receitasService.delete(editingId)
   - Deleta os 12 lançamentos antigos
5. Toast: "✅ Receita atualizada com sucesso!"
   ↓
Backend:
- Cria 12 novos lançamentos com novo valor
- Deleta os 12 antigos
- Saldo é recalculado
```

---

## 📊 Payload Esperado - Exemplos Reais

### FIXA

```json
{
  "descricao": "Aluguel",
  "valor": "2000,00",
  "tipo_lancamento": "Receita",
  "recorrencia": "FIXA",
  "categoria": "Aluguel",
  "subcategoria": "Residencial",
  "conta_id": 1,
  "data_vencimento": "2025-10-01",
  "data_lancamento": "2025-10-18",
  "status_lancamento": "PENDENTE",
  "mesAno": "2025-10"
}
```

**Backend cria:**

- 12 lançamentos de 2025-10-01 até 2026-09-01

---

### PARCELADO

```json
{
  "descricao": "Compra Importada",
  "valor": "1000,00",
  "tipo_lancamento": "Despesa",
  "recorrencia": "PARCELADO",
  "qtd_parcelas": 5,
  "num_parcela": 1,
  "tipo_parcela": "TOTAL",
  "periodicidade": "MENSAL",
  "categoria": "Importação",
  "subcategoria": "Produtos",
  "conta_id": 1,
  "data_vencimento": "2025-10-15",
  "data_lancamento": "2025-10-18",
  "status_lancamento": "PENDENTE",
  "mesAno": "2025-10"
}
```

**Backend cria:**

- 5 lançamentos de 200,00 cada
- Vencimentos: 15/10, 15/11, 15/12, 15/01, 15/02

---

### NAO_RECORRENTE

```json
{
  "descricao": "Venda de Produto",
  "valor": "150,00",
  "tipo_lancamento": "Receita",
  "recorrencia": "NAO_RECORRENTE",
  "categoria": "Vendas",
  "subcategoria": "Varejo",
  "conta_id": 1,
  "data_vencimento": "2025-10-18",
  "data_lancamento": "2025-10-18",
  "status_lancamento": "PENDENTE",
  "mesAno": "2025-10"
}
```

**Backend cria:**

- 1 lançamento apenas

---

## 🧪 Teste Agora

### Teste 1: Criar FIXA

1. Abra ReceitasView
2. Clique "Nova Receita"
3. Preencha:
   - Descrição: "Renda Extra"
   - Valor: "500,00"
   - Recorrência: **"Fixa"**
   - Data: hoje
4. Clique "Adicionar"
5. ✅ Verifique:
   - Toast: "Receita criada com sucesso!"
   - Na tabela: varia de 12 receitas (mude filtro de mês para ver as futuras)
   - No console: Payload enviado deve ter `recorrencia: "FIXA"`

### Teste 2: Criar PARCELADO

1. Abra ReceitasView
2. Clique "Nova Receita"
3. Preencha:
   - Descrição: "Financiamento"
   - Valor: "600,00"
   - Recorrência: **"Parcelado"**
   - Parcelas: **2**
   - Toggle: **"VALOR TOTAL"**
   - Data: hoje
4. Clique "Adicionar"
5. ✅ Verifique:
   - Toast: "Receita criada com sucesso!"
   - Na tabela: 2 receitas (1/2 e 2/2) de 300,00 cada
   - Vencimentos em meses diferentes
   - No console: Payload deve ter `qtd_parcelas: 2, tipo_parcela: "TOTAL"`

### Teste 3: Editar NAO_RECORRENTE

1. Clique ✏️ em receita simples
2. Altere valor para "200,00"
3. Clique "Atualizar"
4. ✅ Verifique:
   - Toast: "Receita atualizada com sucesso!"
   - Valor na tabela: 200,00 (atualizou só uma linha)

### Teste 4: Editar FIXA

1. Clique ✏️ em receita FIXA
2. Altere valor para "600,00"
3. Clique "Atualizar"
4. ✅ Verifique:
   - Toast: "Receita atualizada com sucesso!"
   - As 12 linhas antigas desaparecem
   - 12 novas linhas aparecem com 600,00
   - (Pode levar um momento para recarregar)

---

## 🔍 Debug

Se algo não funcionar, verifique no Console (F12):

1. **Network tab:**

   - POST /api/lancamentos
   - Procure "recorrencia" no payload
   - Deve estar em MAIÚSCULAS: "FIXA" ou "PARCELADO"

2. **Console logs:**

   - Procure "Payload enviado:"
   - Verifique todos os campos necessários

3. **Response:**
   - Status 201 = Sucesso
   - Status 422 = Erro de validação (falta algum campo)
   - Status 500 = Erro no servidor

---

## 📝 Resumo das Mudanças

✅ **Frontend - ReceitasView.vue**

- saveReceita() agora diferencia FIXA/PARCELADO de NAO_RECORRENTE
- Para FIXA/PARCELADO: sempre CRIA novo (delete+create ao editar)
- Para NAO_RECORRENTE: pode ATUALIZAR normalmente

✅ **Frontend - DespesasView.vue**

- Mesma lógica que ReceitasView

✅ **Backend - Já estava pronto!**

- LancamentoService.php com toda lógica
- Controller usando o serviço

---

**Status:** ✅ PRONTO PARA TESTAR
**Data:** 2025-10-18
