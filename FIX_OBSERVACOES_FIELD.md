# Fix: Observações Field in Maintenance Form

## Problem
When submitting a new maintenance record, the API returned error:
```
Erro ao registrar manutenção: Undefined array key "observacoes"
```

## Root Cause
The frontend form was missing the `observacoes` (observations) field:
1. **Template**: No textarea field for observacoes in the form
2. **Data Model**: formManutencaoData didn't initialize observacoes
3. **Submission**: saveManutencao() function didn't include observacoes in POST data

## Solution

### 1. Updated resetFormManutencao() Function
**File**: `/frontend/src/views/veiculo/VeiculoView.vue` (lines 1384-1399)

Added initialization of observacoes field:
```javascript
function resetFormManutencao(veiculoId: number | null | undefined = null) {
  return {
    veiculoId: veiculoId || null,
    tipo: '',
    data: new Date().toISOString().split('T')[0],
    quilometragem: 0,
    oficina: { /* ... */ },
    observacoes: '',  // ← NEW FIELD
    itens: [/* ... */]
  }
}
```

### 2. Added UI Field to Template
**File**: `/frontend/src/views/veiculo/VeiculoView.vue` (after line 570)

Added textarea field for observations:
```vue
<!-- Observações -->
<v-row class="mt-2">
  <v-col cols="12">
    <v-textarea
      v-model="formManutencaoData.observacoes"
      label="Observações"
      placeholder="Notas adicionais sobre a manutenção (opcional)"
      outlined
      dense
      rows="3"
    />
  </v-col>
</v-row>
```

### 3. Updated saveManutencao() Function
**File**: `/frontend/src/views/veiculo/VeiculoView.vue` (lines 1419-1470)

Updated both createManutencao and updateManutencao calls to include observacoes:

```javascript
// For creating new maintenance
manutencaoService.createManutencao({
  veiculo_id: formManutencaoData.value.veiculoId,
  tipo: formManutencaoData.value.tipo,
  data: formManutencaoData.value.data,
  quilometragem: formManutencaoData.value.quilometragem,
  oficina_nome: formManutencaoData.value.oficina.nome,
  oficina_telefone: formManutencaoData.value.oficina.telefone,
  oficina_email: formManutencaoData.value.oficina.email,
  oficina_endereco: formManutencaoData.value.oficina.endereco,
  observacoes: formManutencaoData.value.observacoes || '',  // ← ADDED
  itens: itens,
})

// For updating maintenance
manutencaoService.updateManutencao(editingManutencao.value, {
  // ... other fields ...
  observacoes: formManutencaoData.value.observacoes || '',  // ← ADDED
  itens: itens,
})
```

## Backend Compatibility

The backend ManutencaoController already had correct validation:
```php
'observacoes' => 'nullable|string'
```

And properly handles the field in model creation:
```php
'observacoes' => $validated['observacoes']
```

## Testing

1. Open "Adicionar Manutenção" dialog
2. Fill form including observações field
3. Submit form
4. Verify:
   - API returns 201 (created) instead of 500
   - Success message appears: "Manutenção registrada com sucesso!"
   - New maintenance appears in list with observações displayed

## Files Modified
- `/frontend/src/views/veiculo/VeiculoView.vue`

## Commit
- **Hash**: 77c4331c
- **Message**: "fix: Add observacoes field to maintenance form"

## Related Issues
- Error: "Undefined array key 'observacoes'"
- Status: ✅ RESOLVED
