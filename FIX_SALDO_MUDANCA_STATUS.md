# 🔧 Fix: Atualizar Saldo da Conta ao Alterar Status do Lançamento

## Problema Identificado

Quando você edita um lançamento apenas para mudar o status (de EFETIVADA para PENDENTE ou vice-versa), o saldo da conta não era atualizado.

### Exemplo do Bug:

1. Despesa R$ 100,00 com status EFETIVADA → saldo conta = 900,00 ✅
2. Editar despesa e mudar status para PENDENTE → saldo continua 900,00 ❌
3. Esperado: saldo deveria voltar para 1000,00

## Causa Raiz

Na função `editLancamento()` do `LancamentoController`, não havia lógica para:

1. **Detectar mudanças de status**
2. **Reverter o saldo anterior** se estava EFETIVADA
3. **Aplicar o novo efeito** se o novo status é EFETIVADA

O código apenas salvava o lançamento sem atualizar a conta.

## Solução Implementada

### Arquivo: `/backend/app/Http/Controllers/LancamentoController.php`

**Adições:**

1. Import da classe `Conta` (linha 12)
2. Lógica de atualização de saldo na função `editLancamento()` (linhas 250-293)

**Lógica implementada:**

```php
// 1. Capturar status anterior ANTES de atualizar
$statusAnterior = $lancamento->status_lancamento;
$statusNovo = $request->input('status_lancamento');

// 2. Se o status mudou, reverter o saldo anterior e aplicar o novo
if ($statusAnterior !== $statusNovo && in_array($lancamento->tipo_lancamento, ['RECEITA', 'DESPESA']) && $lancamento->conta_id) {
    $conta = Conta::find($lancamento->conta_id);

    if ($conta) {
        // REVERTER o saldo anterior
        if ($statusAnterior === 'EFETIVADA') {
            if ($lancamento->tipo_lancamento === 'RECEITA') {
                $conta->saldo -= $lancamento->valor; // Remover receita
            } else { // DESPESA
                $conta->saldo += $lancamento->valor; // Remover despesa
            }
        }

        // APLICAR o novo status
        if ($statusNovo === 'EFETIVADA') {
            if ($lancamento->tipo_lancamento === 'RECEITA') {
                $conta->saldo += $lancamento->valor; // Adicionar receita
            } else { // DESPESA
                $conta->saldo -= $lancamento->valor; // Subtrair despesa
            }
        }

        $conta->save();
    }
}
```

## Fluxos de Teste

### Fluxo 1: EFETIVADA → PENDENTE (Desfazer)

```
ANTES:
- Lançamento: Despesa R$ 100,00 - Status: EFETIVADA
- Conta: Saldo = 900,00

AÇÃO: Editar → Mudar status para PENDENTE

DEPOIS:
✅ Lançamento: Status = PENDENTE
✅ Conta: Saldo = 1.000,00 (despesa revertida)
```

### Fluxo 2: PENDENTE → EFETIVADA (Confirmar)

```
ANTES:
- Lançamento: Receita R$ 500,00 - Status: PENDENTE
- Conta: Saldo = 1.000,00

AÇÃO: Editar → Mudar status para EFETIVADA

DEPOIS:
✅ Lançamento: Status = EFETIVADA
✅ Conta: Saldo = 1.500,00 (receita adicionada)
```

### Fluxo 3: Mudar valor E status

```
ANTES:
- Lançamento: Despesa R$ 100,00 - Status: EFETIVADA - Conta: 900,00

AÇÃO: Editar → Mudar valor para 150,00 E status para PENDENTE

DEPOIS:
✅ Lançamento: Despesa R$ 150,00 - Status: PENDENTE
✅ Conta: Saldo = 1.050,00
   (Reverteu 100, não aplica 150 porque ficou PENDENTE)
```

## Fluxo Técnico Detalhado

```
1️⃣ Frontend: Edita despesa e muda status
   └─ Envia: {status_lancamento: "PENDENTE", ...outros_dados}

2️⃣ Backend editLancamento() recebe requisição
   ├─ Captura status_anterior = "EFETIVADA"
   ├─ Captura status_novo = "PENDENTE"
   ├─ Detecta mudança: EFETIVADA → PENDENTE
   │
   ├─ Se tipo = DESPESA e estava EFETIVADA:
   │  └─ Reverter: conta.saldo += 100 (remover despesa)
   │
   ├─ Se tipo = DESPESA e novo = PENDENTE:
   │  └─ Não aplica: pois status novo é PENDENTE
   │
   ├─ Salva lançamento com novo status
   └─ Salva conta com novo saldo

3️⃣ Frontend recebe resposta
   └─ Recarrega dados → mostra novo saldo ✅
```

## Casos Cobertos

✅ **Despesa EFETIVADA → PENDENTE**

- Status anterior: EFETIVADA (saldo -= valor)
- Novo status: PENDENTE (não aplica efeito)
- Resultado: saldo +valor (reverter despesa)

✅ **Receita EFETIVADA → PENDENTE**

- Status anterior: EFETIVADA (saldo += valor)
- Novo status: PENDENTE (não aplica efeito)
- Resultado: saldo -valor (reverter receita)

✅ **Despesa PENDENTE → EFETIVADA**

- Status anterior: PENDENTE (não tinha efeito)
- Novo status: EFETIVADA (saldo -= valor)
- Resultado: saldo -valor (efetiva despesa)

✅ **Receita PENDENTE → EFETIVADA**

- Status anterior: PENDENTE (não tinha efeito)
- Novo status: EFETIVADA (saldo += valor)
- Resultado: saldo +valor (efetiva receita)

✅ **Sem mudança de status**

- `if ($statusAnterior !== $statusNovo)` = FALSE
- Lógica não executa
- Saldo não muda ✅

## Integração com outras operações

Essa solução não conflita com:

- ✅ `createLancamento()` - continua funcionando (usa `LancamentoService`)
- ✅ `efetivarLancamento()` - continua funcionando (usa `LancamentoService`)
- ✅ Lançamentos CARTAO_CREDITO - ignorados por `in_array(['RECEITA', 'DESPESA'])`
- ✅ Lançamentos sem conta - ignorados por `$lancamento->conta_id`

---

**Data da Correção**: 2025-11-04
**Arquivo**: `/backend/app/Http/Controllers/LancamentoController.php`
**Linhas**: 250-293
**Status**: ✅ Implementado
