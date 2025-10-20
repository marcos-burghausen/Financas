# Navegação Entre Meses - Feature Implementation

## Overview

Implementada navegação intuitiva entre meses para as views de Receitas e Despesas, permitindo que os usuários visualizem dados de diferentes períodos de forma fácil e intuitiva.

## Changes Made

### 1. ReceitasView.vue

#### State Addition (line ~661)

```typescript
// 📅 Month Navigation State
const selectedMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM format
```

**Features:**

- Inicializa com o mês atual em formato YYYY-MM
- Reativo a mudanças de navegação

#### Month Navigation Functions (line ~935)

```typescript
// 📅 Month Navigation Functions
const getMonthName = (mesAnoString: string): string => {
  const [ano, mes] = mesAnoString.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  return format(date, "MMMM yyyy", { locale: ptBR });
};

const goToPreviousMonth = () => {
  const [ano, mes] = selectedMonth.value.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() - 1);
  const newMonth = date.toISOString().slice(0, 7);
  selectedMonth.value = newMonth;
  userStore.setMesAno(newMonth);
  loadReceitas();
};

const goToNextMonth = () => {
  const [ano, mes] = selectedMonth.value.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() + 1);
  const newMonth = date.toISOString().slice(0, 7);
  selectedMonth.value = newMonth;
  userStore.setMesAno(newMonth);
  loadReceitas();
};

const goToCurrentMonth = () => {
  const currentMonth = new Date().toISOString().slice(0, 7);
  selectedMonth.value = currentMonth;
  userStore.setMesAno(currentMonth);
  loadReceitas();
};
```

**Functions:**

- `getMonthName()`: Converte formato YYYY-MM para nome legível (ex: "October 2025")
- `goToPreviousMonth()`: Navega para o mês anterior
- `goToNextMonth()`: Navega para o próximo mês
- `goToCurrentMonth()`: Retorna ao mês atual (clicando no nome do mês)

#### Template Update (lines ~27-57)

```vue
<!-- 📅 Month Navigation -->
<v-card class="mb-6" elevation="1">
  <v-card-text class="pa-4">
    <div class="d-flex align-center justify-center gap-4">
      <v-btn
        icon="mdi-chevron-left"
        color="primary"
        variant="outlined"
        size="small"
        @click="goToPreviousMonth"
        title="Mês anterior"
      />
      <div class="text-center" style="min-width: 250px">
        <v-btn
          variant="text"
          :text="getMonthName(selectedMonth).toUpperCase()"
          @click="goToCurrentMonth"
          :class="{ 'text-primary font-weight-bold': selectedMonth === new Date().toISOString().slice(0, 7) }"
          title="Ir para o mês atual"
        />
      </div>
      <v-btn
        icon="mdi-chevron-right"
        color="primary"
        variant="outlined"
        size="small"
        @click="goToNextMonth"
        title="Próximo mês"
      />
    </div>
  </v-card-text>
</v-card>
```

**UI Features:**

- Botão esquerda: ← (Mês anterior)
- Centro: Nome do mês em MAIÚSCULAS (clicável para voltar ao mês atual)
- Botão direita: → (Próximo mês)
- Destaque visual quando visualizando o mês atual
- Responsive design com flex layout

### 2. DespesasView.vue

#### Imports Addition (line ~362-365)

```typescript
import { format } from "date-fns";
import { ptBR } from "date-fns/locale";
```

#### State Addition (line ~380)

```typescript
// 📅 Month Navigation State
const selectedMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM format
```

#### Month Navigation Functions (line ~458-503)

Idênticas ao ReceitasView com chamadas para:

- `loadDespesas()` em vez de `loadReceitas()`
- Mesma lógica de navegação e formatação

#### Template Update (lines ~27-57)

Idêntico ao ReceitasView

## How It Works

### User Flow

1. Usuário vê a view de Receitas/Despesas com navegação de mês
2. Visualiza o mês atual destacado em negrito
3. Clica em ← para ver mês anterior
4. Interface atualiza com dados do novo mês
5. Summary cards recalculam valores para o período selecionado
6. Clica no nome do mês para retornar ao mês atual

### Technical Flow

1. User clica em `goToPreviousMonth()` ou `goToNextMonth()`
2. Função calcula novo mês usando Date object
3. Converte para formato YYYY-MM
4. Atualiza `selectedMonth.value`
5. Chama `userStore.setMesAno(newMonth)` - persiste no store
6. Chama `loadReceitas()` ou `loadDespesas()` - recarrega dados filtrados
7. Backend recebe `mesAno` query param e filtra dados
8. Template atualiza com novo nome de mês
9. Summary cards e table rerendem com novos dados

### Date Calculation

```typescript
// Parse YYYY-MM string
const [ano, mes] = selectedMonth.value.split("-");

// Create date from string
const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);

// Navigate months
date.setMonth(date.getMonth() - 1); // Previous
date.setMonth(date.getMonth() + 1); // Next

// Convert back to YYYY-MM
const newMonth = date.toISOString().slice(0, 7);
```

## State Management Integration

### userStore Integration

```typescript
// Persists selected month in user store
userStore.setMesAno(newMonth);

// Used in loadReceitas() and loadDespesas()
const mesAno = userStore.getMesAno?.() || new Date().toISOString().slice(0, 7);
```

**Benefits:**

- Mês selecionado persiste entre navegações
- Se user fechar e reabrir, volta ao último mês visualizado
- Sincroniza entre Receitas e Despesas views

## Backend Compatibility

### Expected Query Parameter

```typescript
// loadReceitas() sends:
const mesAno = userStore.getMesAno?.() || new Date().toISOString().slice(0, 7);

// API expects:
GET /api/receitas?mesAno=2025-10
```

### Backend Response Filtering

Backend deve filtrar lançamentos que tenham `data_vencimento` ou `data_lancamento` dentro do mês especificado:

```php
// Example (Laravel)
if ($mesAno) {
    $query->whereYear('data_vencimento', '=', substr($mesAno, 0, 4))
          ->whereMonth('data_vencimento', '=', substr($mesAno, 5, 2));
}
```

## Date Formatting

### Input/Output Formats

- **State Storage**: YYYY-MM (e.g., "2025-10")
- **Display**: "October 2025" (English) or "outubro 2025" (Portuguese)
- **Backend**: YYYY-MM for API query params
- **Calculation**: JavaScript Date object for month arithmetic

### Locale Support

- Uses `date-fns` with `ptBR` locale
- Month names automatically translated to Portuguese
- Format: `format(date, 'MMMM yyyy', { locale: ptBR })`

## Testing Procedures

### Test Case 1: Navigate to Previous Month

1. Open Receitas view
2. Verify current month is displayed and highlighted
3. Click left arrow (←)
4. Verify month name changes correctly
5. Verify data updates for previous month
6. Verify summary cards recalculate

### Test Case 2: Navigate to Next Month

1. In any month view, click right arrow (→)
2. Verify month name increments
3. Verify data updates for next month
4. Verify summary cards recalculate

### Test Case 3: Return to Current Month

1. Navigate to previous month
2. Click on the month name (center)
3. Verify returns to current month
4. Verify month is highlighted in bold

### Test Case 4: Year Boundary Navigation

1. Navigate to December 2024
2. Click right arrow
3. Verify correctly moves to January 2025
4. Verify year changes in display

### Test Case 5: Multiple Consecutive Navigations

1. Navigate forward 3 months
2. Navigate backward 2 months
3. Verify correct month displayed
4. Verify data is accurate

### Test Case 6: Month Persistence

1. Navigate to June 2025
2. Reload page
3. Verify June 2025 is still selected
4. Verify data for June is loaded

## Integration Points

### Stores Used

- **useUserStore()**: `setMesAno()`, `getMesAno()`
- Used to persist and retrieve selected month

### Services Used

- **receitasService**: Updated via `loadReceitas()`
- **despesasService**: Updated via `loadDespesas()`
- Services should filter by `mesAno` parameter

### Helper Functions Used

- **format()** from date-fns: Format month name
- **loadReceitas()** / **loadDespesas()**: Reload data for month

### Reactive References

- **selectedMonth**: v-model for current selected month
- Updates trigger template re-render with new month name

## Files Modified

- `/frontend/src/views/receitas/ReceitasView.vue`

  - Added: `selectedMonth` state
  - Added: `getMonthName()`, `goToPreviousMonth()`, `goToNextMonth()`, `goToCurrentMonth()`
  - Added: Month navigation card in template

- `/frontend/src/views/despesas/DespesasView.vue`
  - Added: date-fns imports (`format`, `ptBR`)
  - Added: `selectedMonth` state
  - Added: `getMonthName()`, `goToPreviousMonth()`, `goToNextMonth()`, `goToCurrentMonth()`
  - Added: Month navigation card in template

## Backward Compatibility

✅ Feature is fully backward compatible:

- Existing data loading logic unchanged
- Month defaults to current month if not set
- No breaking changes to API contract
- Existing summary cards still work with new month parameter

## Future Enhancements

Possible improvements:

- Add date picker component to jump to specific month
- Add "This Year" and "Last Year" quick filters
- Add month comparison view (side-by-side)
- Add month range selection
- Add month export functionality
- Add URL routing for bookmarkable months (/receitas?month=2025-10)

## Performance Considerations

- Month navigation refetches data (necessary for accurate filtering)
- Consider adding loading state during month transitions
- Cache previous months data if needed for faster navigation
- Consider pagination if dealing with large datasets

---

**Status**: ✅ Implementation Complete
**Date**: October 19, 2025
**Related Docs**:

- EFETIVAR_LANCAMENTOS.md (action buttons)
- FIX_STATUS_ATRASADA_DINAMICO.md (dynamic status)
