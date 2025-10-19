# 🔧 Correção: Toast Store Methods

## ❌ Problema

Erro ao chamar toast:

```
TypeError: toastStore.showError is not a function
```

## ✅ Solução

### Métodos Corretos do Toast Store

O `useToastStore` não expõe métodos como `showSuccess()`, `showError()`, etc.

Os métodos reais são:

- ✅ `toastStore.success(message)`
- ✅ `toastStore.error(message)`
- ✅ `toastStore.warning(message)`
- ✅ `toastStore.info(message)`

### Mudanças Aplicadas

#### ReceitasView.vue

```javascript
// Antes ❌
toastStore.showSuccess("Receita criada com sucesso!");
toastStore.showError(error.message);
toastStore.showWarning("Erro ao carregar");

// Depois ✅
toastStore.success("Receita criada com sucesso!");
toastStore.error(error.message);
toastStore.warning("Erro ao carregar");
```

#### DespesasView.vue

```javascript
// Aplicadas mesmas mudanças
toastStore.success(); // ✅
toastStore.error(); // ✅
toastStore.warning(); // ✅
```

---

## 📝 Arquivos Atualizados

✅ `/frontend/src/views/receitas/ReceitasView.vue`

- deleteReceita() - Mudado para `toastStore.success()` e `toastStore.error()`
- saveReceita() - Mudado para `toastStore.success()` e `toastStore.error()`
- loadReceitas() - Mudado para `toastStore.warning()`

✅ `/frontend/src/views/despesas/DespesasView.vue`

- deleteDespesa() - Mudado para `toastStore.success()` e `toastStore.error()`
- saveDespesa() - Mudado para `toastStore.success()` e `toastStore.error()`
- loadDespesas() - Mudado para `toastStore.warning()`

---

## 🧪 Teste Agora

1. Abra ReceitasView ou DespesasView
2. Crie uma nova receita/despesa
3. Verifique notificação de sucesso (toast verde)
4. Edite uma receita/despesa
5. Verifique notificação de atualização (toast verde)
6. Delete uma receita/despesa
7. Verifique notificação de sucesso (toast verde)

✅ Sem mais erros de "is not a function"!

---

**Status:** ✅ CORRIGIDO
**Data:** 2025-10-18
