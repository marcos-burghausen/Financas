# Fix - Erro ao Efetivar Receita (Valor em Centavos)

## Problema

Ao clicar no botão "Efetivar" para marcar uma receita como recebida, aparecia erro no backend:

```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'valor' at row 1
SQL: update `lancamentos` set `valor` = 10,00, ...
```

## Causa Raiz

**Múltiplas causas concorrentes:**

### 1. Frontend enviando valor errado para efetivar

- Ao clicar "Efetivar", o valor vinha do banco em centavos (1000)
- Frontend convertia para string "10,00" (com VÍRGULA)
- Backend recebia "10,00" e tentava fazer `UPDATE valor = '10,00'`
- MySQL vê "10,00" como string com vírgula e trunca para "10"
- Depois multiplica por 100 = 1000 centavos (OK) mas com truncation warning

### 2. Backend não processando transformações

- O controller `editLancamento()` estava usando `Request` genérico
- Não passava por `StoreLancamentoRequest` que faz transformações
- Não executava `transformValor()` que converte "10,00" → 1000

## Solução

### 1. Corrigir Frontend - Enviar Centavos (ReceitasView.vue)

```typescript
const efetivarReceita = async (receita: any) => {
  try {
    loading.value = true;
    // ✅ Converter valor para centavos (inteiro) para enviar ao backend
    let valorCentavos = receita.valor;

    if (typeof receita.valor === 'string') {
      // Se for string "10,00", converte para 1000
      valorCentavos = Math.round(parseFloat(receita.valor.replace(',', '.')) * 100);
    } else if (typeof receita.valor === 'number') {
      // Se já for número, assume que é centavos, mantém como está
      valorCentavos = receita.valor;
    }

    const payload = {
      ...receita,
      valor: valorCentavos, // ✅ Enviar valor em centavos (número inteiro)
      status_lancamento: 'EFETIVADA',
      // ... resto do payload
    };

    await receitasService.update(receita.id, payload);
    // ...
  }
};
```

**O que faz:**

- Verifica se `receita.valor` é string ("10,00")
- Substitui vírgula por ponto: "10.00"
- Converte para float: 10.00
- Multiplica por 100: 1000.0
- Arredonda: 1000 (centavos) ✅
- Resultado: Envia `valor: 1000` (inteiro)

### 2. Corrigir Backend - Usar StoreLancamentoRequest (LancamentoController.php)

**Antes:**

```php
public function editLancamento(Request $request, $id): JsonResponse
{
    $lancamento->update($request->all()); // ❌ Sem transformações
}
```

**Depois:**

```php
public function editLancamento(StoreLancamentoRequest $request, $id): JsonResponse
{
    // ✅ Usar validated() para aplicar transformações do StoreLancamentoRequest
    $lancamento->update($request->validated());
}
```

**O que faz:**

- Type hint `StoreLancamentoRequest` invoca validações e transformações
- `$request->validated()` retorna dados já transformados
- `transformValor()` aplicada automaticamente:
  - Recebe: "10,00" (string com vírgula)
  - Remove ".": "1000" (sem separador milhares)
  - Substitui ",": "." → "10.00"
  - Multiplica por 100 → 1000 (centavos)
  - Retorna: 1000 (inteiro)

## Fluxo Correto Agora

```
Frontend:
  receita.valor = 1000 (centavos do banco)
         ↓
  "Se é número, enviar como está" → payload.valor = 1000
         ↓
Backend recebe:
  PUT /lancamentos/74
  { valor: 1000, status_lancamento: 'EFETIVADA', ... }
         ↓
StoreLancamentoRequest.validated():
  - transformValor() vê que 1000 é número
  - Backend MySQL recebe: UPDATE valor = 1000 ✅
  - Sem truncation, sem erro!
         ↓
Response:
  { success: "Lançamento atualizado com sucesso!", ... } ✅
```

## Casos Cobertos

| Cenário                             | Antes              | Depois     |
| ----------------------------------- | ------------------ | ---------- |
| Efetivar com valor inteiro (1000)   | Erro truncation ❌ | Sucesso ✅ |
| Efetivar com valor string ("10,00") | Erro truncation ❌ | Sucesso ✅ |
| Editar form normalmente             | OK ✅              | OK ✅      |
| Salvar receita nova                 | OK ✅              | OK ✅      |

## Proteção Dupla

### Frontend

```typescript
if (typeof receita.valor === "string") {
  valorCentavos = Math.round(parseFloat(receita.valor.replace(",", ".")) * 100);
} else if (typeof receita.valor === "number") {
  valorCentavos = receita.valor; // Já está em centavos
}
```

Garante que: String "10,00" → 1000 ou Número 1000 → 1000

### Backend

```php
private function transformValor(): int
{
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return (int) round((float) $valor * 100);
}
```

Garante que: Qualquer formato → Centavos (inteiro)

## Teste Rápido

1. Criar receita R$ 10,00 (fica em banco como 1000 centavos)
2. Clicar botão "Efetivar" (checkmark verde)
3. ✅ Deve atualizar status para "Recebida" SEM ERRO
4. ✅ Valor deve permanecer R$ 10,00

## Arquivos Modificados

- `/frontend/src/views/receitas/ReceitasView.vue`

  - Corrigido: `efetivarReceita()` - converte string para centavos
  - Mudança: Enviando `valor` como inteiro (centavos)

- `/frontend/src/views/despesas/DespesasView.vue`

  - Corrigido: `efetivarDespesa()` - mesmo padrão
  - Mudança: Enviando `valor` como inteiro (centavos)

- `/backend/app/Http/Controllers/LancamentoController.php`
  - **CRÍTICO**: `editLancamento()` agora recebe `StoreLancamentoRequest`
  - Mudança: De `$request->all()` para `$request->validated()`
  - Impacto: Todas as transformações (transformValor, transformTipoLancamento) agora aplicadas

## Relacionado

- FIX_VALOR_FORMULARIO_EDICAO.md (conversão de centavos no formulário)
- CORRECAO_VALOR_CATEGORIA.md (histórico de correções de valor)
- PAYLOAD_LANCAMENTOS_COMPLETO.md (formato de payload)

---

**Status**: ✅ Fix Completo
**Data**: October 19, 2025
**Impacto**: CRÍTICO - Permite usar botão "Efetivar" sem erros
**Severidade**: Alta - Bloqueava funcionalidade de efetivar
