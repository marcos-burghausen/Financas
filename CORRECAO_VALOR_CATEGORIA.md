# 🔧 Correção: Valor 10x Maior e Categoria "Outros"

## ❌ Problemas Identificados

1. **Valor Multiplicado por 10**: Criou receita de R$ 10,00 mas retornou como R$ 1.000,00
2. **Categoria Inicial**: Ao abrir formulário para editar, categoria vinha como "Outros" em vez de vazia

## 🔍 Causa Raiz

### Problema 1: Valor

**Fluxo Errado:**

```
Frontend: input "10,00"
  ↓
convertParaFormData: "10,00" (mantém string)
  ↓
saveReceita(): parseFloat("10,00") * 100 = 1000 (centavos)
  ↓
Envia payload: valor: 1000 (número)
  ↓
Backend recebe: 1000
  ↓
Backend faz: transformValor() → str_replace(",", ".") → "10.00" → * 100 = 1000 ❌
  ↓
Resultado: 1000 centavos = 10,00 reais... ESPERA!
```

**O Real Problema:**

```
Frontend envia: valor: 1000 (número)
Backend espera: valor: "10,00" (STRING formatada com vírgula)
Backend faz: parseFloat("1000") * 100 = 100000
Retorna: 1000 reais = R$ 1.000,00 ❌
```

**Solução:** Enviar valor como STRING formatado, deixar o backend fazer a conversão!

### Problema 2: Categoria

Na função `loadReceitas()` e `loadDespesas()`, estava fazendo:

```typescript
categoria: r.categoria || 'Outros',  // ❌ Se vazio, preenche com "Outros"
```

Quando editava a receita, carregava com "Outros" preenchido.

---

## ✅ Solução Implementada

### 1. ReceitasView.vue - saveReceita()

**Antes:**

```typescript
const valorEmCentavos = Math.round(valorEmReais * 100);
const payload = {
  valor: valorEmCentavos, // ❌ Envia número em centavos
  categoria: formData.value.categoria,
  subcategoria: formData.value.subcategoria || "Outros", // ❌ Fallback
};
```

**Depois:**

```typescript
const payload = {
  valor: formData.value.valor, // ✅ Envia STRING "10,00"
  categoria: formData.value.categoria, // ✅ Sem fallback
  subcategoria: formData.value.subcategoria, // ✅ Sem fallback
};
```

### 2. ReceitasView.vue - loadReceitas()

**Antes:**

```typescript
receitas.value = data.map((r: any) => ({
  categoria: r.categoria || "Outros", // ❌ Fallback preenche vazio
  subcategoria: r.subcategoria || "Outros", // ❌ Fallback preenche vazio
}));
```

**Depois:**

```typescript
receitas.value = data.map((r: any) => ({
  categoria: r.categoria, // ✅ Sem fallback
  subcategoria: r.subcategoria, // ✅ Sem fallback
}));
```

### 3. DespesasView.vue - Mesmas mudanças

- ✅ saveDespesa() - Envia valor como STRING
- ✅ saveDespesa() - Remove fallback de categoria/subcategoria
- ✅ loadDespesas() - Remove fallback de categoria/subcategoria

---

## 🧪 Teste Agora

### Teste 1: Valor Correto

1. Crie nova receita
2. Digite valor: **10,00**
3. Clique Adicionar
4. ✅ Verifique na tabela: **R$ 10,00** (não R$ 1.000,00)

### Teste 2: Editar - Categoria Vazia

1. Clique ✏️ em uma receita
2. Verifique se categoria/subcategoria estão **vazias** (não com "Outros")
3. ✅ Pode deixar vazio ou preencher com valor desejado

### Teste 3: Criar com Categoria

1. Crie nova receita
2. Preencha categoria: "Salário"
3. Preencha subcategoria: "Principal"
4. ✅ Salve e verifique se mostra na tabela

---

## 📊 Comparação

| Cenário                | Antes                          | Depois                 |
| ---------------------- | ------------------------------ | ---------------------- |
| Criar receita R$ 10,00 | Retorna R$ 1.000,00 ❌         | Retorna R$ 10,00 ✅    |
| Editar receita         | Categoria vem como "Outros" ❌ | Categoria vem vazia ✅ |
| Salvar sem categoria   | Preenche com "Outros" ❌       | Mantém vazio ✅        |

---

## 🔧 Mudanças Técnicas

### Frontend → Backend (Valor)

**Novo Fluxo:**

```
Input: "10,00"
  ↓
formData.value.valor: "10,00" (STRING)
  ↓
Envia: valor: "10,00"
  ↓
Backend transformValor():
  - str_replace(".", "") = "1000"
  - str_replace(",", ".") = "10.00"
  - parseFloat * 100 = 1000 (centavos)
  ↓
Resultado: 1000 centavos = 10,00 reais ✅
```

---

## 📁 Arquivos Atualizados

✅ `/frontend/src/views/receitas/ReceitasView.vue`

- saveReceita() - Envia valor como STRING
- loadReceitas() - Remove fallback de categoria/subcategoria

✅ `/frontend/src/views/despesas/DespesasView.vue`

- saveDespesa() - Envia valor como STRING
- loadDespesas() - Remove fallback de categoria/subcategoria

---

**Status:** ✅ CORRIGIDO
**Data:** 2025-10-18
