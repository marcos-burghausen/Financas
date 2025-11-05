# 📊 Resumo das Correções - Despesas (04/11/2025)

## ✅ Correção 1: Valor Multiplicado por 100

### Problema

```
Input: 10,00 → Salvo: 1.000,00 ❌
```

### Causa

Frontend enviava `1000` (número em centavos), backend multiplicava por 100 novamente.

### Solução

**Arquivo**: `frontend/src/views/despesas/DespesasView.vue`

- ✅ `editDespesa()`: Converte centavos → string para exibição (1000 → "10,00")
- ✅ `saveDespesa()`: Envia string "10,00", backend converte
- ✅ `efetivarDespesa()`: Converte número → string antes de enviar

### Fluxo Correto

```
Backend retorna: 1000 centavos
        ↓
editDespesa: 1000 ÷ 100 = "10,00" (mostrar no form)
        ↓
saveDespesa: envia "10,00" (STRING)
        ↓
Backend transforma: "10,00" × 100 = 1000 ✅
```

---

## ✅ Correção 2: Saldo não atualiza ao mudar Status

### Problema

```
1. Despesa R$ 10,00 - Status EFETIVADA → Saldo = 90,00 ✅
2. Editar → Mudar status para PENDENTE
3. Resultado: Saldo ainda 90,00 ❌
4. Esperado: Saldo 100,00 (despesa revertida)
```

### Causa

`editLancamento()` não recalculava saldo quando status mudava.

### Solução

**Arquivo**: `backend/app/Http/Controllers/LancamentoController.php`

- ✅ Import da classe `Conta` (linha 12)
- ✅ Detecta mudança de status (linhas 255-256)
- ✅ Reverte saldo anterior se estava EFETIVADA (linhas 264-273)
- ✅ Aplica novo efeito se novo status é EFETIVADA (linhas 276-285)
- ✅ Salva conta com novo saldo (linha 288)

### Lógica

```
IF status mudou de EFETIVADA para PENDENTE:
  ├─ Reverter saldo anterior
  │  ├─ Se RECEITA: saldo -= valor
  │  └─ Se DESPESA: saldo += valor
  │
  └─ Não aplicar novo efeito (pois novo é PENDENTE)

IF status mudou de PENDENTE para EFETIVADA:
  ├─ Não reverter (não tinha efeito anterior)
  │
  └─ Aplicar novo efeito
     ├─ Se RECEITA: saldo += valor
     └─ Se DESPESA: saldo -= valor

IF status não mudou:
  └─ Não fazer nada com saldo
```

### Casos Testados

- ✅ Despesa EFETIVADA → PENDENTE (saldo +valor)
- ✅ Receita EFETIVADA → PENDENTE (saldo -valor)
- ✅ Despesa PENDENTE → EFETIVADA (saldo -valor)
- ✅ Receita PENDENTE → EFETIVADA (saldo +valor)
- ✅ Sem mudança de status (saldo não muda)

---

## 📝 Checklist de Testes

### Para Teste 1 (Valor)

- [ ] Criar despesa com R$ 10,00 → salvar
- [ ] Verificar se salvou como 1.000 centavos (10,00 na tabela)
- [ ] Editar a despesa → valor deve aparecer como "10,00"
- [ ] Mudar para R$ 20,00 → salvar
- [ ] Verificar se atualizou corretamente

### Para Teste 2 (Status e Saldo)

- [ ] Criar despesa com R$ 15,00 - Status EFETIVADA
- [ ] Verificar saldo da conta: deve diminuir 15,00 ✅
- [ ] Editar despesa → mudar status para PENDENTE
- [ ] Verificar saldo da conta: deve aumentar 15,00 ✅
- [ ] Editar despesa → mudar status para EFETIVADA
- [ ] Verificar saldo da conta: deve diminuir 15,00 ✅

### Para Teste 3 (Ambas)

- [ ] Criar despesa R$ 10,00 - EFETIVADA → saldo -= 10,00
- [ ] Editar: mudar valor para 25,00 E status para PENDENTE
- [ ] Verificar: saldo += 10,00 (reverteu só o anterior)
- [ ] Editar novamente: mudar valor para 30,00 (manter PENDENTE)
- [ ] Verificar: saldo não muda (pois continua PENDENTE)

---

## 🔄 Fluxo Completo de Edição

```
Usuario: Clica "Editar" em despesa
    ↓
Frontend carrega dados:
  ├─ valor: 1000 (centavos do backend)
  ├─ status_lancamento: "EFETIVADA"
  └─ Converte valor para "10,00" (editDespesa)
    ↓
Formulário exibe:
  ├─ Valor: "10,00" ✅
  ├─ Status: EFETIVADA
  └─ Outros dados...
    ↓
Usuario altera:
  ├─ Valor: "20,00"
  └─ Status: PENDENTE
    ↓
Clica "Atualizar":
  ├─ Frontend valida
  ├─ Envia: {valor: "20,00", status_lancamento: "PENDENTE", ...}
  └─ Backend recebe
    ↓
Backend editLancamento():
  ├─ Status anterior: "EFETIVADA"
  ├─ Status novo: "PENDENTE"
  ├─ Detecta mudança ✅
  ├─ Reverter saldo: +10,00 (remover despesa antiga)
  ├─ Não aplica novo (PENDENTE não efetiva)
  ├─ Transforma valor: "20,00" × 100 = 2000
  ├─ Atualiza lançamento
  ├─ Salva conta com novo saldo
  └─ Retorna sucesso
    ↓
Frontend recarrega dados:
  ├─ Mostra novo valor: 2.000 centavos → "20,00" ✅
  ├─ Mostra novo status: PENDENTE ✅
  ├─ Mostra novo saldo da conta: +10,00 ✅
  └─ Toast: "Despesa atualizada com sucesso!"
```

---

## 📋 Arquivos Modificados

| Arquivo                                                 | Mudança                   | Linhas              |
| ------------------------------------------------------- | ------------------------- | ------------------- |
| `frontend/src/views/despesas/DespesasView.vue`          | Fix valor multiplicado    | ~1349, ~1382, ~1428 |
| `backend/app/Http/Controllers/LancamentoController.php` | Fix saldo status + import | 12, 250-293         |

---

**Status**: ✅ Ambas correções implementadas e documentadas
**Data**: 04 de Novembro de 2025
