# Efetivar Lançamentos - Feature Implementation

## Overview

Added "Efetivar" button in the actions column of Receitas and Despesas tables to allow users to quickly mark pending/overdue items as effectuated (received/paid) directly from the table view.

## Changes Made

### 1. ReceitasView.vue

#### New Function (after deleteReceita, line ~1055)

```typescript
const efetivarReceita = async (receita: any) => {
  try {
    loading.value = true;
    const payload = {
      ...receita,
      status_lancamento: "EFETIVADA",
      data_vencimento: formatDateForBackend(receita.data_vencimento),
      data_lancamento: formatDateForBackend(receita.data_lancamento),
      data_efetivacao: formatDateForBackend(receita.data_efetivacao),
      tipo_lancamento: "Receita",
    };

    await receitasService.update(receita.id, payload);
    toastStore.success("Receita efetivada com sucesso!");
    await loadReceitas();
  } catch (error: any) {
    console.error("Erro ao efetivar receita:", error);
    toastStore.error(error.message || "Erro ao efetivar receita");
  } finally {
    loading.value = false;
  }
};
```

**Features:**

- Builds complete payload with all receita fields
- Sets status_lancamento to 'EFETIVADA'
- Formats all dates using formatDateForBackend() for YYYY-MM-DD format
- Ensures tipo_lancamento is 'Receita' (required by backend)
- Calls receitas.service.update() to save changes
- Reloads data via loadReceitas()
- Shows success/error toast messages
- Sets loading state during operation

#### Template Update (lines ~208-228)

Added new button in #item.acoes template:

```vue
<template #item.acoes="{ item }">
  <div class="d-flex gap-1 justify-end">
    <v-btn
      v-if="
        getStatusReal(item) === 'pendente' || getStatusReal(item) === 'atrasada'
      "
      icon="mdi-check-circle"
      size="x-small"
      variant="text"
      color="success"
      @click="efetivarReceita(item)"
      title="Marcar como recebida"
    />
    <v-btn
      icon="mdi-pencil"
      size="x-small"
      variant="text"
      color="primary"
      @click="editReceita(item)"
    />
    <v-btn
      icon="mdi-delete"
      size="x-small"
      variant="text"
      color="error"
      @click="deleteReceita(item.id)"
    />
  </div>
</template>
```

**Button Features:**

- Icon: `mdi-check-circle` (green checkmark in circle)
- Color: `success` (green)
- Size: `x-small` (matches other action buttons)
- Visibility: `v-if` condition shows only if status is 'pendente' or 'atrasada'
- Position: First in actions, before edit and delete buttons
- Tooltip: "Marcar como recebida"
- Click handler: `efetivarReceita(item)`

### 2. DespesasView.vue

#### New Function (after deleteDespesa, line ~540)

```typescript
const efetivarDespesa = async (despesa: any) => {
  try {
    loading.value = true;
    const payload = {
      ...despesa,
      status_lancamento: "EFETIVADA",
      data_vencimento: formatDateForBackend(despesa.data_vencimento),
      data_lancamento: formatDateForBackend(despesa.data_lancamento),
      data_efetivacao: formatDateForBackend(despesa.data_efetivacao),
      tipo_lancamento: "Despesa",
    };

    await despesasService.update(despesa.id, payload);
    toastStore.success("Despesa paga com sucesso!");
    await loadDespesas();
  } catch (error: any) {
    console.error("Erro ao efetivar despesa:", error);
    toastStore.error(error.message || "Erro ao efetivar despesa");
  } finally {
    loading.value = false;
  }
};
```

**Identical to ReceitasView with changes:**

- tipo_lancamento: 'Despesa' (instead of 'Receita')
- Toast message: 'Despesa paga com sucesso!' (instead of efetivada)
- Uses despesasService instead of receitasService
- Calls loadDespesas() instead of loadReceitas()

#### Template Update (lines ~207-227)

Same button structure as ReceitasView, with tooltip "Marcar como paga" (instead of recebida)

## How It Works

### User Flow

1. User sees pending or overdue receipt/expense in table
2. Row displays with yellow (pendente) or red (atrasada) status badge
3. Green checkmark button appears in actions column
4. User clicks checkmark button
5. Item status updates to EFETIVADA
6. Status badge changes to green (recebida/paga)
7. Summary cards update their counts
8. Success toast appears

### Technical Flow

1. User clicks efetivar button → `efetivarReceita(item)` or `efetivarDespesa(item)` called
2. Function builds payload with:
   - All existing receita/despesa fields
   - status_lancamento: 'EFETIVADA'
   - Properly formatted dates (YYYY-MM-DD)
   - Correct tipo_lancamento ('Receita' or 'Despesa')
3. Calls API via service: `await receitasService.update(id, payload)`
4. Backend receives update and saves EFETIVADA status
5. Frontend reloads all data via `loadReceitas()` or `loadDespesas()`
6. Table re-renders with updated statuses
7. getStatusReal() recalculates status (EFETIVADA → 'recebida'/'paga')
8. Summary cards recomputed with new counts

## Status Display Logic

### Before Efetivar

- Receita/Despesa with PENDENTE status shows:
  - Yellow "Pendente" badge (if future due date)
  - Red "Atrasada" badge (if past due date)
  - Green checkmark button visible

### After Efetivar

- Same item now shows:
  - Green "Recebida" badge (for receitas) or "Paga" badge (for despesas)
  - Checkmark button hidden (v-if condition no longer met)
  - Item moves from "Pendentes" or "Atrasadas" card to "Recebidas" or "Pagas" card

## Data Format & Validation

### Critical Requirements

✅ **tipo_lancamento**: Must be 'Receita' or 'Despesa' (NOT MAIÚSCULAS)

- Backend's StoreLancamentoRequest.transformTipoLancamento() expects this format
- It automatically transforms to MAIÚSCULAS in database

✅ **status_lancamento**: Must be 'EFETIVADA' (MAIÚSCULAS)

- Backend stores and expects uppercase

✅ **Dates**: Must be YYYY-MM-DD format

- formatDateForBackend() extracts from ISO strings
- Backend stores and processes as-is

✅ **valor**: Must be string with proper formatting

- Payload spreads existing receita/despesa which has correct value format

## Error Handling

### Success Scenarios

- ✅ Item successfully updated → Toast: "Receita efetivada com sucesso!"
- ✅ Item successfully updated → Toast: "Despesa paga com sucesso!"
- ✅ Data reloaded → Table updates with new status badges

### Error Scenarios

- ❌ Network error → Toast: Error message from API
- ❌ Validation error → Toast: Validation error details
- ❌ Server error → Toast: Server error message or generic "Erro ao efetivar receita/despesa"
- ✅ Error caught, console logged, loading state cleared

## Testing Procedures

### Test Case 1: Mark Pending Receipt as Received

1. Create a new receipt with status PENDENTE and future due date
2. Verify yellow "Pendente" badge appears
3. Verify green checkmark button is visible
4. Click checkmark button
5. Verify API call succeeds (check network tab)
6. Verify badge changes to green "Recebida"
7. Verify checkmark button disappears
8. Verify "Recebidas" summary card count increases by 1
9. Verify "Pendentes" summary card count decreases by 1

### Test Case 2: Mark Overdue Receipt as Received

1. Create receipt with status PENDENTE and past due date
2. Verify red "Atrasada" badge appears
3. Verify green checkmark button is visible
4. Click checkmark button
5. Verify badge changes to green "Recebida"
6. Verify checkmark button disappears
7. Verify "Recebidas" summary card count increases
8. Verify "Atrasadas" summary card count decreases

### Test Case 3: Mark Pending Expense as Paid

1. Create expense with status PENDENTE and future due date
2. Verify yellow "Pendente" badge appears
3. Click checkmark button (should say "Marcar como paga")
4. Verify badge changes to green "Paga"
5. Verify "Pagas" summary card count increases
6. Verify "Pendentes" summary card count decreases

### Test Case 4: Verify Button Not Shown for Received Items

1. Look for already received (green "Recebida") receipt
2. Verify checkmark button is NOT visible in actions column
3. Verify only edit and delete buttons show

### Test Case 5: Error Handling

1. Attempt to efetivar with network disabled
2. Verify error toast appears with error message
3. Verify table doesn't update (maintains original state)
4. Verify loading state returns to normal

## Integration Points

### Services Used

- **receitas.service.ts**: `update(id, data)` - Updates receipt in backend
- **despesas.service.ts**: `update(id, data)` - Updates expense in backend

### Store Used

- **useToastStore()**: `success()`, `error()` - Show notifications

### Helper Functions Used

- **formatDateForBackend(date)**: Converts ISO/Date to YYYY-MM-DD
- **getStatusReal(item)**: Calculates dynamic status based on dates
- **loadReceitas()**: Reloads all receipts from API
- **loadDespesas()**: Reloads all expenses from API

### State Variables Used

- **loading.value**: Set during API operation
- **formRef.value**: Not used in efetivar (only in save)

## Files Modified

- `/frontend/src/views/receitas/ReceitasView.vue`

  - Added: efetivarReceita() function
  - Updated: Template #item.acoes with efetivar button

- `/frontend/src/views/despesas/DespesasView.vue`
  - Added: efetivarDespesa() function
  - Updated: Template #item.acoes with efetivar button

## Backward Compatibility

✅ Feature is fully backward compatible:

- Existing edit and delete buttons remain functional
- No changes to data model or API contract
- Uses existing update endpoint (PUT /lancamentos/{id})
- No changes to other features or computations

## Future Enhancements

Possible future improvements:

- Add undo/revert button to change back from EFETIVADA to PENDENTE
- Add bulk operations to efetivar multiple items at once
- Add date picker for data_efetivacao when marking as effectuated
- Add confirmation dialog before marking items as effectuated

---

**Status**: ✅ Implementation Complete
**Date**: 2025
**Related Docs**:

- FIX_STATUS_ATRASADA_DINAMICO.md (dynamic status calculation)
- PAYLOAD_LANCAMENTOS_COMPLETO.md (payload format requirements)
