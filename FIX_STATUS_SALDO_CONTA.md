# 🐛 Fix: Saldo da Conta Não Atualiza ao Mudar Status

## Problema

Quando você edita uma despesa e muda o status de **EFETIVADA → PENDENTE**, o saldo da conta não é revertido (não sobe de volta).

O inverso funciona: **PENDENTE → EFETIVADA** funciona perfeitamente (saldo desce).

## Causa Raiz

A função `editLancamento` no backend não estava recalculando o saldo corretamente quando o status mudava.

### Fluxo Antigo (Incorreto):

```
PENDENTE → EFETIVADA (funciona):
  ✅ Frontend clica "Marcar como pago"
  ✅ Backend chama efetivarLancamento() (método dedicado)
  ✅ Saldo atualizado corretamente

EFETIVADA → PENDENTE (não funciona):
  ❌ Frontend clica editar, muda status, salva
  ❌ Backend chama editLancamento()
  ❌ Não recalcula saldo (apenas atualiza DB record)
  ❌ Saldo fica errado
```

## Solução Implementada

### Mudanças no Backend

**Arquivo**: `/backend/app/Http/Controllers/LancamentoController.php`

A função `editLancamento()` agora:

1. **Captura o status anterior** antes de fazer a atualização
2. **Compara com o novo status** vindo do frontend
3. **Se mudou e é RECEITA/DESPESA**:
   - **REVERTER** o efeito do status anterior
   - **APLICAR** o efeito do novo status
4. **Recalcula o saldo** da conta associada
5. **Log detalhado** para debug

### Lógica de Recalc

```php
// SE ESTAVA EFETIVADA:
if ($statusAnterior === 'EFETIVADA') {
    if (RECEITA) saldo -= valor      // Remover receita adicionada
    if (DESPESA) saldo += valor      // Devolver despesa subtraída
}

// SE VAI FICAR EFETIVADA:
if ($statusNovo === 'EFETIVADA') {
    if (RECEITA) saldo += valor      // Adicionar receita
    if (DESPESA) saldo -= valor      // Subtrair despesa
}
```

### Exemplos de Transições

| De        | Para      | Ação     | Despesa 100     |
| --------- | --------- | -------- | --------------- |
| PENDENTE  | EFETIVADA | Aplicar  | Saldo -= 100 ✅ |
| EFETIVADA | PENDENTE  | Reverter | Saldo += 100 ✅ |
| PENDENTE  | PENDENTE  | Nada     | Sem mudança     |
| EFETIVADA | EFETIVADA | Nada     | Sem mudança     |

## Fluxo de Dados Após Fix

```
Frontend DespesasView.vue:
├─ User clica "editar"
├─ Muda status (ex: EFETIVADA → PENDENTE)
├─ Clica "Atualizar"
├─ Envia payload com status_lancamento: "PENDENTE"
│
Backend LancamentoController:
├─ Recebe status_lancamento: "PENDENTE"
├─ Lê status atual do BD: "EFETIVADA"
├─ Detecta mudança: "EFETIVADA" !== "PENDENTE"
├─ Se DESPESA:
│  ├─ Reverter: saldo += 100 (devolve a despesa)
│  └─ Aplicar: nada (novo status é PENDENTE)
├─ Salva novo status no BD
└─ ✅ Saldo atualizado!

BD:
├─ lancamento.status_lancamento = "PENDENTE"
├─ conta.saldo += 100
└─ ✅ Consistência mantida!
```

## Logs para Debug

Se algo não funcionar, verifique os logs:

```bash
# Ver logs do Laravel
docker compose logs -f backend

# Procurar por "Editando lançamento"
# Procurar por "Recalculando saldo"
# Procurar por "Novo saldo da conta"
```

## Testes Recomendados

### Teste 1: EFETIVADA → PENDENTE

1. Crie uma despesa com R$ 10,00
2. Marque como paga (vai para EFETIVADA)
3. Verifique saldo desceu R$ 10,00
4. Edite, mude status para PENDENTE
5. ✅ Saldo deve subir R$ 10,00 de volta

### Teste 2: PENDENTE → EFETIVADA (via editar)

1. Crie uma despesa com R$ 20,00
2. Deixe como PENDENTE
3. Edite, mude status para EFETIVADA
4. ✅ Saldo deve descer R$ 20,00

### Teste 3: Sem mudança de status

1. Edite apenas a descrição (mantendo status)
2. ✅ Saldo não deve mudar

### Teste 4: Receita

1. Crie receita com R$ 30,00
2. Marque como recebida
3. Verifique saldo subiu R$ 30,00
4. Edite, mude para PENDENTE
5. ✅ Saldo deve descer R$ 30,00

## Status Versões

| Campo               | Status                           |
| ------------------- | -------------------------------- |
| `status_lancamento` | EFETIVADA, PENDENTE              |
| `tipo_lancamento`   | RECEITA, DESPESA, CARTAO_CREDITO |

---

**Data da Correção**: 2025-11-04  
**Arquivo Modificado**: `/backend/app/Http/Controllers/LancamentoController.php`  
**Método**: `editLancamento()` (linhas ~242-298)  
**Status**: ✅ Implementado com Logging
