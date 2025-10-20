# Fix - Valor em Formulário de Edição

## Problema

Quando você editava uma receita/despesa, o valor era exibido multiplicado por 100. Por exemplo:

- Original: R$ 10,00
- Após editar: R$ 1.000,00 (valor \* 100)

## Causa Raiz

O backend retorna valores em **centavos** (1000 = R$ 10,00) para economizar espaço e evitar problemas de ponto flutuante.

Quando você clicava em "Editar", a função `editReceita()` / `editDespesa()` fazia:

```typescript
formData.value = { ...receita };
```

Isso copiava o valor em centavos (1000) diretamente para o formulário, sem conversão.

Depois, ao salvar, o `saveReceita()` / `saveDespesa()` enviava novamente em centavos:

- Recebia: 1000 (centavos)
- Enviava: "1000,00" (como string)
- Backend recebia: 100000 centavos = R$ 1.000,00 ❌

## Solução Implementada

### 1. Corrigir `editReceita()` (ReceitasView.vue)

```typescript
const editReceita = (receita: any) => {
  editingId.value = receita.id;
  // ✅ Converter valor de centavos para string formatada "10,00"
  const valorFormatado =
    typeof receita.valor === "number"
      ? (receita.valor / 100).toFixed(2).replace(".", ",")
      : receita.valor;

  formData.value = {
    ...receita,
    valor: valorFormatado, // ✅ Exibir valor formatado no formulário
  };
  dialog.value = true;
};
```

**O que faz:**

- Verifica se `receita.valor` é um número (centavos)
- Divide por 100 para converter para reais
- Usa `.toFixed(2)` para 2 casas decimais
- Substitui `.` por `,` (formato brasileiro)
- Resultado: 1000 → "10,00" ✅

### 2. Corrigir `editDespesa()` (DespesasView.vue)

Mesma lógica da receita, garantindo consistência.

### 3. Adicionar Segurança em `efetivarReceita()` (ReceitasView.vue)

```typescript
const efetivarReceita = async (receita: any) => {
  try {
    loading.value = true;
    // ✅ Converter valor de centavos para string formatada "10,00"
    const valorFormatado = typeof receita.valor === 'number'
      ? (receita.valor / 100).toFixed(2).replace('.', ',')
      : receita.valor;

    const payload = {
      ...receita,
      valor: valorFormatado, // ✅ Enviar valor formatado como STRING
      status_lancamento: 'EFETIVADA',
      // ... resto dos campos
    };

    await receitasService.update(receita.id, payload);
    // ...
  }
  // ...
};
```

### 4. Adicionar Segurança em `efetivarDespesa()` (DespesasView.vue)

Mesma proteção para despesas.

## Fluxo Correto Agora

### Antes (❌ ERRADO)

```
Backend: valor = 1000 (centavos)
         ↓
editReceita(): formData.valor = 1000
              ↓
UI exibe: R$ 1.000,00 (exibe centavos como reais) ❌
              ↓
saveReceita(): payload.valor = "1000,00"
              ↓
Backend recebe: 100000 centavos = R$ 1.000,00 ❌
```

### Depois (✅ CORRETO)

```
Backend: valor = 1000 (centavos)
         ↓
editReceita(): formData.valor = "10,00" (convertido)
              ↓
UI exibe: R$ 10,00 ✅
         (usuário vê valor correto)
              ↓
saveReceita(): payload.valor = "10,00" (string formatada)
              ↓
Backend recebe: 1000 centavos = R$ 10,00 ✅
```

## Conversão de Centavos → Reais

A conversão usa esta fórmula:

```typescript
// 1000 centavos → "10,00"
(1000 / 100).toFixed(2).replace('.', ',')
= (10).toFixed(2).replace('.', ',')
= "10.00".replace('.', ',')
= "10,00" ✅
```

## Casos Cobertos

| Cenário                 | Antes                   | Depois                |
| ----------------------- | ----------------------- | --------------------- |
| Editar receita R$ 10,00 | Exibe 1000,00 ❌        | Exibe 10,00 ✅        |
| Salvar editado          | Salva 1.000,00 ❌       | Salva 10,00 ✅        |
| Efetivar receita        | Efetivar com 1000,00 ❌ | Efetivar com 10,00 ✅ |
| Efetivar despesa        | Efetivar com 1000,00 ❌ | Efetivar com 10,00 ✅ |
| Valor já string         | Não converte ✅         | Mantém como está ✅   |

## Proteção Adicionada

Todas as funções agora verificam:

```typescript
typeof receita.valor === 'number' ? (conversão) : (mantém como está)
```

Isso garante que:

- Se vier como número → converte de centavos
- Se vier como string → não mexe (já está formatado)
- Funciona em ambos os casos ✅

## Testes Recomendados

### Test 1: Editar Receita com Valor em Centavos

1. Criar receita R$ 10,00
2. Clicar Editar
3. ✅ Verificar que exibe "10,00" (não "1000,00")
4. Modificar descrição
5. Clicar Atualizar
6. ✅ Verificar que salvou com valor R$ 10,00 (não R$ 1.000,00)

### Test 2: Efetivar Receita

1. Criar receita R$ 25,50 (pendente)
2. Clicar botão "Efetivar" (checkmark)
3. ✅ Verificar que atualizou para "Recebida"
4. ✅ Verificar que valor permanece R$ 25,50 (não R$ 2.550,00)

### Test 3: Efetivar Despesa

1. Criar despesa R$ 85,00 (pendente)
2. Clicar botão "Efetivar" (checkmark)
3. ✅ Verificar que atualizou para "Paga"
4. ✅ Verificar que valor permanece R$ 85,00 (não R$ 8.500,00)

### Test 4: Múltiplas Edições

1. Criar receita R$ 5,00
2. Editar → verificar valor "5,00" ✅
3. Salvar
4. Editar novamente → verificar valor "5,00" ✅
5. Salvar
6. ✅ Verificar que valor final é R$ 5,00

## Arquivos Modificados

- `/frontend/src/views/receitas/ReceitasView.vue`

  - Corrigido: `editReceita()` - adiciona conversão de centavos
  - Melhorado: `efetivarReceita()` - adiciona proteção de valor

- `/frontend/src/views/despesas/DespesasView.vue`
  - Corrigido: `editDespesa()` - adiciona conversão de centavos
  - Melhorado: `efetivarDespesa()` - adiciona proteção de valor

## Relacionado

- CORRECAO_VALOR_CATEGORIA.md (histórico de correções de valor)
- PAYLOAD_LANCAMENTOS_COMPLETO.md (descrição do payload)
- EFETIVAR_LANCAMENTOS.md (botão efetivar)

---

**Status**: ✅ Fix Completo
**Data**: October 19, 2025
**Impacto**: Alto - Corrige bug crítico de valor incorreto ao editar
