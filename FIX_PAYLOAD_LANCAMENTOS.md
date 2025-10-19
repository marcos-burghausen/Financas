# 🔧 Fix: Correção de Payload para Criação de Lançamentos

## 📋 Problema Identificado

A API estava retornando erros de validação:

```json
{
  "tipo_lancamento": ["The tipo lancamento field is required."],
  "mesAno": ["The mes ano field is required."]
}
```

## 🎯 Causas Raiz

1. **tipo_lancamento**: Sendo enviado como "Receita" (como digitado no formulário), mas a API espera "RECEITA" (MAIÚSCULA)
2. **recorrencia**: Sendo enviado como "Não recorrente" / "Parcelado", mas API espera "NAO_RECORRENTE" / "PARCELADO"
3. **mesAno**: Não estava sendo enviado no payload
4. **tipo_parcela**: Sendo enviado em minúsculas ("total"), mas API espera "TOTAL"
5. **periodicidade**: Sendo enviado em minúsculas, mas API espera em MAIÚSCULAS

## ✅ Soluções Implementadas

### 1. Atualizar `saveReceita()` em ReceitasView.vue

**Arquivo:** `/frontend/src/views/receitas/ReceitasView.vue` (linhas 977-1051)

**Mudanças:**

```typescript
// MAPA DE TRANSFORMAÇÃO PARA RECORRÊNCIA
const recorrenciaMap: { [key: string]: string } = {
  "Não recorrente": "NAO_RECORRENTE",
  Fixa: "FIXA",
  Parcelado: "PARCELADO",
};

// OBTER mesAno NO FORMATO YYYY-MM
const mesAno = userStore.getMesAno?.() || new Date().toISOString().slice(0, 7);

// PAYLOAD COM CAMPOS EM MAIÚSCULAS
const payload = {
  descricao: formData.value.descricao,
  valor: valorEmCentavos,
  categoria: formData.value.categoria,
  subcategoria: formData.value.subcategoria || "Outros",
  conta_id: formData.value.conta_id,
  data_vencimento: formData.value.data_vencimento,
  data_lancamento: formData.value.data_lancamento,
  data_efetivacao: formData.value.data_efetivacao,
  status_lancamento: formData.value.status_lancamento,
  observacoes: formData.value.observacoes,
  recorrencia: recorrenciaMap[formData.value.recorrencia] || "NAO_RECORRENTE",
  tipo_lancamento: "Receita", // ✅ Deixado assim porque será transformado pelo backend
  mesAno: mesAno, // ✅ ADICIONADO - obrigatório
};

// SE PARCELADO - MAIÚSCULAS
if (formData.value.recorrencia === "Parcelado") {
  Object.assign(payload, {
    qtd_parcelas: tempNumParcelas.value,
    num_parcela: tempParcelaInicial.value,
    tipo_parcela: tipoCalculoParcela.value?.toUpperCase() || "TOTAL", // ✅ toUpperCase()
    periodicidade: tempPeriodicidade.value?.toUpperCase() || "MENSAL", // ✅ toUpperCase()
  });
}
```

### 2. Melhorar Interface `Receita` em receitas.service.ts

**Arquivo:** `/frontend/src/services/receitas.service.ts` (linhas 5-27)

**Campos Adicionados:**

```typescript
export interface Receita {
  // ... campos existentes ...
  tipo_lancamento?: string; // Tipo (RECEITA, DESPESA, etc)
  mesAno?: string; // Mês/ano YYYY-MM
  qtd_parcelas?: number; // Quantidade de parcelas
  num_parcela?: number; // Número da parcela atual
  tipo_parcela?: string; // TOTAL ou PARCELA
  periodicidade?: string; // MENSAL, SEMANAL, etc
}
```

### 3. Melhorar `handleError()` em receitas.service.ts

**Arquivo:** `/frontend/src/services/receitas.service.ts` (linhas 95-120)

**Agora captura:**

- ✅ Erros de validação do Laravel (campo → [mensagem])
- ✅ Mensagem geral de erro
- ✅ Status HTTP
- ✅ Log completo do erro no console

```typescript
private handleError(error: any): Error {
  console.error('ReceitasService Error:', error);

  if (error.response?.data?.errors) {
    const firstError = Object.values(error.response.data.errors)[0];
    if (Array.isArray(firstError)) {
      return new Error(firstError[0]); // "The tipo lancamento field is required."
    }
  }
  // ... outras verificações ...
}
```

## 📊 Mapeamento de Valores

### Recorrência

| Frontend       | API            |
| -------------- | -------------- |
| Não recorrente | NAO_RECORRENTE |
| Fixa           | FIXA           |
| Parcelado      | PARCELADO      |

### Tipo Lançamento

| Frontend          | API            |
| ----------------- | -------------- |
| Receita           | RECEITA        |
| Despesa           | DESPESA        |
| Cartão de Crédito | CARTAO_CREDITO |

### Tipo Parcela

| Frontend | API     |
| -------- | ------- |
| total    | TOTAL   |
| parcela  | PARCELA |

### Periodicidade

| Frontend  | API       |
| --------- | --------- |
| Mensal    | MENSAL    |
| Semanal   | SEMANAL   |
| Quinzenal | QUINZENAL |
| Bimestral | BIMESTRAL |

### Status Lançamento

| Frontend  | API       |
| --------- | --------- |
| PENDENTE  | PENDENTE  |
| EFETIVADA | EFETIVADA |

## 🧪 Teste o Novo Payload

**Esperado (Receita Simples):**

```json
{
  "descricao": "Salário",
  "valor": 500000,
  "categoria": "Salário",
  "subcategoria": "Salário",
  "conta_id": 1,
  "data_vencimento": "2025-10-18",
  "data_lancamento": "2025-10-18",
  "data_efetivacao": null,
  "status_lancamento": "PENDENTE",
  "observacoes": "",
  "recorrencia": "NAO_RECORRENTE",
  "tipo_lancamento": "Receita",
  "mesAno": "2025-10"
}
```

**Esperado (Receita Parcelada):**

```json
{
  "descricao": "Bonus",
  "valor": 100000,
  "categoria": "Bônus",
  "subcategoria": "Bônus",
  "conta_id": 1,
  "data_vencimento": "2025-10-18",
  "data_lancamento": "2025-10-18",
  "data_efetivacao": null,
  "status_lancamento": "PENDENTE",
  "observacoes": "",
  "recorrencia": "PARCELADO",
  "tipo_lancamento": "Receita",
  "mesAno": "2025-10",
  "qtd_parcelas": 2,
  "num_parcela": 1,
  "tipo_parcela": "TOTAL",
  "periodicidade": "MENSAL"
}
```

## 🔍 Verificação

Para verificar o payload sendo enviado, procure no console do navegador:

```
Payload enviado: { descricao: "...", valor: ..., etc }
```

E nos logs do backend (Laravel):

```
[2025-10-18] production.ERROR: Erro ao salvar lançamento: ...
```

## 🚀 Próximos Passos

1. ✅ Aplicar mesmo padrão em **DespesasView** - COMPLETO
2. Testes de integração completa
3. Validar transações no backend
4. Aplicar em FormCartãoCredito se necessário

---

**Status:** ✅ IMPLEMENTADO EM ReceitasView E DespesasView
**Data:** 2025-10-18
**Versão:** 2.0 - Com suporte a Despesas também
