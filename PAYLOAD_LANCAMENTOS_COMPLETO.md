# 🔧 Payload Lançamentos - Versão Completa

## ✅ Mudanças Realizadas

### Problema Identificado

O payload enviado pelo frontend estava **INCOMPLETO** - faltavam muitos campos que o backend espera na interface `Lancamento`.

### Solução Implementada

#### 1. **Interface Receita/Despesa Atualizada**

- Agora corresponde exatamente com a interface `Lancamento` do backend
- Adicionados todos os campos necessários com tipos corretos

**Campos Adicionados:**

```typescript
id?: number | null
user_id?: number | null
invoice_id?: number | null
is_estorno?: boolean
original_lancamento_id?: number | null
fatura?: string | null // "YYYY-MM"
cartao_id?: number | null
conta_model?: { id: number; nome: string }
data_efetivacao?: string | Date | null
```

#### 2. **Payload ReceitasView.vue - ANTES vs DEPOIS**

**ANTES (Incompleto):**

```typescript
{
  descricao: "...",
  valor: "10,00",
  categoria: "...",
  tipo_lancamento: "Receita",              // ❌ Errado (deve ser MAIÚSCULA)
  recorrencia: "NAO_RECORRENTE",
  mesAno: "2025-10"
}
```

**DEPOIS (Completo):**

```typescript
{
  // Campos obrigatórios
  descricao: "...",
  valor: "10,00",                           // ✅ STRING "10,00"
  tipo_lancamento: "RECEITA",               // ✅ MAIÚSCULAS
  recorrencia: "NAO_RECORRENTE",            // ✅ MAIÚSCULAS
  status_lancamento: "PENDENTE",
  categoria: "...",
  subcategoria: "...",
  conta_id: 1,
  data_vencimento: "2025-10-20",
  data_lancamento: "2025-10-18",
  mesAno: "2025-10",

  // Campos da interface Lancamento (com valores padrão)
  id: null,                                 // ✅ null se criar, id se atualizar NAO_RECORRENTE
  invoice_id: null,
  is_estorno: false,
  original_lancamento_id: null,
  data_efetivacao: null,
  observacoes: null,
  fatura: null,
  cartao_id: null,
  user_id: null,

  // Se for Parcelado (adicionados automaticamente)
  qtd_parcelas: 3,
  num_parcela: 1,
  tipo_parcela: "total",                    // ✅ lowercase (total ou parcela)
  periodicidade: "MENSAL"                   // ✅ MAIÚSCULAS
}
```

#### 3. **DespesasView.vue - Mesmas Mudanças**

- Atualizado tipo_lancamento: 'DESPESA' (MAIÚSCULAS)
- Mesmo payload structure que ReceitasView
- Mesmos campos adicionados

---

## 📊 Comparação de Tipos

### ReceitasView.vue

| Campo               | Tipo   | Exemplo                                      |
| ------------------- | ------ | -------------------------------------------- |
| `tipo_lancamento`   | String | `RECEITA` (MAIÚSCULAS)                       |
| `valor`             | String | `"10,00"` (não número)                       |
| `recorrencia`       | String | `NAO_RECORRENTE` / `FIXA` / `PARCELADO`      |
| `status_lancamento` | String | `PENDENTE` / `EFETIVADA`                     |
| `tipo_parcela`      | String | `total` / `parcela` (lowercase para parcela) |
| `periodicidade`     | String | `MENSAL` / `ANUAL` (MAIÚSCULAS)              |

### DespesasView.vue

| Campo             | Tipo            | Exemplo                |
| ----------------- | --------------- | ---------------------- |
| `tipo_lancamento` | String          | `DESPESA` (MAIÚSCULAS) |
| Resto             | Igual a Receita | Mesmo padrão           |

---

## 🔍 Validação de Payload

### Exemplo 1: Receita Não Recorrente (Simples)

```typescript
{
  descricao: "Venda de serviço",
  valor: "1500,00",
  tipo_lancamento: "RECEITA",
  recorrencia: "NAO_RECORRENTE",
  status_lancamento: "PENDENTE",
  categoria: "Serviços",
  subcategoria: "Consultoria",
  conta_id: 1,
  data_vencimento: "2025-10-25",
  data_lancamento: "2025-10-18",
  mesAno: "2025-10",
  // Valores padrão
  id: null,
  invoice_id: null,
  is_estorno: false,
  original_lancamento_id: null,
  data_efetivacao: null,
  observacoes: null,
  fatura: null,
  cartao_id: null,
  user_id: null,
  // Sem parcelamento
  qtd_parcelas: null,
  num_parcela: null,
  tipo_parcela: null,
  periodicidade: null
}
```

### Exemplo 2: Despesa Fixa

```typescript
{
  descricao: "Aluguel",
  valor: "2000,00",
  tipo_lancamento: "DESPESA",
  recorrencia: "FIXA",
  status_lancamento: "PENDENTE",
  categoria: "Aluguel",
  subcategoria: null,
  conta_id: 1,
  data_vencimento: "2025-10-05",
  data_lancamento: "2025-10-18",
  mesAno: "2025-10",
  // Valores padrão
  id: null,
  invoice_id: null,
  is_estorno: false,
  original_lancamento_id: null,
  data_efetivacao: null,
  observacoes: null,
  fatura: null,
  cartao_id: null,
  user_id: null,
  // Sem parcelamento
  qtd_parcelas: null,
  num_parcela: null,
  tipo_parcela: null,
  periodicidade: null
}
```

### Exemplo 3: Receita Parcelada (3x)

```typescript
{
  descricao: "Venda parcelada",
  valor: "3000,00",
  tipo_lancamento: "RECEITA",
  recorrencia: "PARCELADO",
  status_lancamento: "PENDENTE",
  categoria: "Vendas",
  subcategoria: "Produtos",
  conta_id: 1,
  data_vencimento: "2025-11-25",
  data_lancamento: "2025-10-18",
  mesAno: "2025-10",
  // Valores padrão
  id: null,
  invoice_id: null,
  is_estorno: false,
  original_lancamento_id: null,
  data_efetivacao: null,
  observacoes: null,
  fatura: null,
  cartao_id: null,
  user_id: null,
  // COM PARCELAMENTO ✅
  qtd_parcelas: 3,
  num_parcela: 1,
  tipo_parcela: "total",          // ✅ Vai dividir 3000 por 3
  periodicidade: "MENSAL"
}
```

---

## 🎯 Comportamento por Recorrência

### NAO_RECORRENTE

- **Criar:** `id: null` → POST /api/lancamentos
- **Editar:** `id: editingId.value` → PUT /api/lancamentos/{id}
- **Cria:** 1 lançamento apenas

### FIXA

- **Criar:** `id: null` → POST /api/lancamentos
- **Editar:** DELETE antigo + POST novo (sempre cria 12)
- **Cria:** 12 lançamentos (LancamentoService.php)

### PARCELADO

- **Criar:** `id: null` → POST /api/lancamentos
- **Editar:** DELETE antigo + POST novo (sempre cria N)
- **Cria:** N lançamentos baseado em `qtd_parcelas`

---

## 🚀 Testes

### Verificar no Browser Console

```javascript
// Quando salvar, ver o payload
console.log('Payload enviado:', payload);

// Verificar estrutura
{
  descricao: "...",
  valor: "10,00",              // ✅ String
  tipo_lancamento: "RECEITA",  // ✅ Maiúsculas
  recorrencia: "NAO_RECORRENTE", // ✅ Maiúsculas
  status_lancamento: "PENDENTE",
  // ... todos os outros campos ...
}
```

### Verificar na Network (DevTools)

1. Abrir DevTools → Network
2. Criar nova receita
3. Clicar em POST /api/lancamentos
4. Ir para Request payload
5. Verificar se contém todos os campos acima

---

## 🔐 Campos Obrigatórios vs Opcionais

### Obrigatórios (sempre enviar)

- `descricao`
- `valor` (STRING "10,00")
- `tipo_lancamento` (MAIÚSCULAS)
- `recorrencia` (MAIÚSCULAS)
- `status_lancamento`
- `categoria`
- `conta_id`
- `data_vencimento`
- `data_lancamento`
- `mesAno` (YYYY-MM)

### Opcionais (enviar null/empty)

- `id` (null se criar, id se atualizar)
- `invoice_id` (null)
- `is_estorno` (false)
- `original_lancamento_id` (null)
- `data_efetivacao` (null)
- `observacoes` (null)
- `fatura` (null)
- `cartao_id` (null)
- `user_id` (null)
- `subcategoria` (empty string)
- `tipo_parcela` (null se NAO_RECORRENTE ou FIXA)
- `qtd_parcelas` (null se NAO_RECORRENTE ou FIXA)
- `periodicidade` (null se NAO_RECORRENTE ou FIXA)

---

## 📝 Checklist de Validação

✅ **ReceitasView.vue:**

- [ ] tipo_lancamento = "RECEITA" (MAIÚSCULAS)
- [ ] valor = STRING "10,00"
- [ ] recorrencia = MAIÚSCULAS
- [ ] id = null ou editingId (apenas NAO_RECORRENTE)
- [ ] Todos os campos padrão inclusos

✅ **DespesasView.vue:**

- [ ] tipo_lancamento = "DESPESA" (MAIÚSCULAS)
- [ ] valor = STRING "10,00"
- [ ] recorrencia = MAIÚSCULAS
- [ ] id = null ou editingId (apenas NAO_RECORRENTE)
- [ ] Todos os campos padrão inclusos

✅ **Services:**

- [ ] Receita interface atualizada
- [ ] Despesa interface atualizada
- [ ] create() aceita payload completo
- [ ] update() aceita payload completo

---

## 🔗 Relação com Backend

O backend espera a seguinte estrutura em `StoreLancamentoRequest.php`:

```php
// Campos que vêm do frontend
$validated = $request->validate([
    'tipo_lancamento' => 'required|in:RECEITA,DESPESA,...',
    'valor' => 'required|string',
    'recorrencia' => 'required|in:NAO_RECORRENTE,FIXA,PARCELADO',
    'status_lancamento' => 'required|in:PENDENTE,EFETIVADA',
    'data_vencimento' => 'required|date',
    'data_lancamento' => 'required|date',
    // ...
]);

// Se FIXA → LancamentoService::createLancamentoFixoStandard()
// Se PARCELADO → LancamentoService::createLancamentoParceladoStandard()
// Se NAO_RECORRENTE → LancamentoService::createLancamentoNaoRecorrente()
```

---

## 🎓 Resumo das Mudanças

| Aspecto           | Antes        | Depois            |
| ----------------- | ------------ | ----------------- |
| tipo_lancamento   | "Receita"    | "RECEITA" ✅      |
| valor             | "10,00"      | "10,00" ✅        |
| Campos extra      | Não inclusos | Todos inclusos ✅ |
| tipo_parcela      | "TOTAL"      | "total" ✅        |
| id                | Não enviado  | null ou id ✅     |
| status_lancamento | Não enviado  | "PENDENTE" ✅     |

**Resultado:** Payload agora contém TODOS os campos que o backend espera! 🎉
