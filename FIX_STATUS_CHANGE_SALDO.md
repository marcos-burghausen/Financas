# 🐛 Fix: Saldo não atualiza ao editar status de despesa

## Problema Identificado

Quando você edita uma despesa e altera apenas o status (EFETIVADA → PENDENTE ou vice-versa), o saldo da conta não é atualizado.

### Reprodução do Problema:

1. Cria uma despesa de R$ 10,00 com status PENDENTE
2. Clica em editar e muda para EFETIVADA → saldo diminui ✅ (funciona)
3. Clica em editar novamente e volta para PENDENTE → saldo NÃO aumenta ❌ (bug!)

## Causa Raiz

No controlador `LancamentoController`, a função `editLancamento()` **não estava recalculando o saldo** quando apenas o status mudava.

**Antes:**

```php
public function editLancamento(StoreLancamentoRequest $request, $id): JsonResponse
{
    // ... código ...
    $lancamento->update($request->validated()); // Só salvava, sem recalcular saldo
    return response()->json(['success' => 'Lançamento atualizado com sucesso!', ...]);
}
```

## Solução Implementada

### Backend: `/backend/app/Http/Controllers/LancamentoController.php`

Melhorei a função `editLancamento()` para:

1. **Capturar o status anterior e novo** antes de salvar
2. **Comparar** se houve mudança de status
3. **Recalcular o saldo** desfazendo a operação anterior e aplicando a nova
4. **Adicionar logging detalhado** para debug

**Novo código:**

```php
public function editLancamento(StoreLancamentoRequest $request, $id): JsonResponse
{
    DB::beginTransaction();

    // 1. Capturar status ANTES
    $statusAnterior = $lancamento->status_lancamento;
    $statusNovo = $request->input('status_lancamento');

    // 2. Se status mudou, recalcular saldo
    if ($statusAnterior !== $statusNovo) {
        $conta = Conta::find($lancamento->conta_id);

        // 3. REVERTER o efeito anterior
        if ($statusAnterior === 'EFETIVADA') {
            if ($lancamento->tipo_lancamento === 'RECEITA') {
                $conta->saldo -= $lancamento->valor; // Remover receita
            } else { // DESPESA
                $conta->saldo += $lancamento->valor; // Devolver despesa
            }
        }

        // 4. APLICAR novo status
        if ($statusNovo === 'EFETIVADA') {
            if ($lancamento->tipo_lancamento === 'RECEITA') {
                $conta->saldo += $lancamento->valor; // Adicionar receita
            } else { // DESPESA
                $conta->saldo -= $lancamento->valor; // Subtrair despesa
            }
        }

        $conta->save();
    }

    // 5. Salvar lançamento com dados validados
    $lancamento->update($request->validated());
    DB::commit();
}
```

## Fluxo de Saldo Agora

### Caso: Despesa EFETIVADA → PENDENTE

**Estado Inicial:**

- Conta: R$ 1.000,00
- Despesa: R$ 10,00 (status: EFETIVADA)

**Ao editar para PENDENTE:**

1. Captura: anterior=EFETIVADA, novo=PENDENTE
2. Reverter EFETIVADA: conta.saldo += 10 → R$ 1.010,00 (devolve a despesa)
3. Aplicar PENDENTE: nada acontece (porque PENDENTE não afeta saldo)
4. **Resultado:** Saldo = R$ 1.010,00 ✅

### Caso: Despesa PENDENTE → EFETIVADA

**Estado Inicial:**

- Conta: R$ 1.010,00
- Despesa: R$ 10,00 (status: PENDENTE)

**Ao editar para EFETIVADA:**

1. Captura: anterior=PENDENTE, novo=EFETIVADA
2. Reverter PENDENTE: nada acontece (porque PENDENTE não afeta saldo)
3. Aplicar EFETIVADA: conta.saldo -= 10 → R$ 1.000,00 (subtrai a despesa)
4. **Resultado:** Saldo = R$ 1.000,00 ✅

## Logging para Debug

Adicionei logs detalhados que mostram:

```
=== EDITANDO LANÇAMENTO 5 ===
Status anterior: EFETIVADA
Status novo (input): PENDENTE
Tipo lançamento: DESPESA
Valor: 1000
Conta ID: 1
STATUS MUDOU! De EFETIVADA para PENDENTE
Saldo da conta ANTES: 99000
Revertendo DESPESA EFETIVADA: adicionando 1000
Saldo da conta DEPOIS: 100000
```

## Testes Recomendados

1. ✅ Criar despesa → salvar → verificar saldo diminuiu
2. ✅ Editar despesa PENDENTE → EFETIVADA → verificar saldo diminui
3. ✅ Editar despesa EFETIVADA → PENDENTE → verificar saldo aumenta
4. ✅ Editar apenas descrição (status igual) → verificar saldo não muda
5. ✅ Editar para receita e fazer mesmo teste → verificar lógica oposta

## Transações de Banco

O código usa `DB::beginTransaction()` e `DB::commit()` para garantir que:

- Se houver erro ao salvar a conta, tudo é revertido (rollback)
- Se houver erro ao salvar o lançamento, tudo é revertido (rollback)
- Ou tudo é salvo com sucesso, ou nada é salvo

---

**Data da Correção**: 2025-11-04
**Arquivo Modificado**: `/backend/app/Http/Controllers/LancamentoController.php`
**Status**: ✅ Correção Implementada (Aguardando Teste)
