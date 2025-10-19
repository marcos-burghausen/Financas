# 🔧 Implementação: Lançamentos Fixos e Parcelados

## 📋 Status Backend

✅ **Backend PRONTO** - O LancamentoService.php já tem toda a lógica:

### Para Lançamentos FIXOS:

```php
private function createLancamentoFixoStandard(array $data, User $user): void
{
    // Cria 12 lançamentos (um para cada mês)
    for ($i = 1; $i <= 12; $i++) {
        $offsetMeses = $i - 1;
        // ... calcula data de vencimento para cada mês
        // ... cria lançamento para cada mês
        // ... se EFETIVADA, atualiza saldo da primeira
    }
}
```

**Comportamento:**

- Quando tipo é "FIXA" → cria 12 lançamentos
- Distribui ao longo dos próximos 12 meses
- Mantém o mesmo dia da semana/mês (ajusta se necessário para meses com menos dias)
- Se criado como "EFETIVADA", apenas a primeira fica efetivada, resto fica PENDENTE

### Para Lançamentos PARCELADOS:

```php
private function createLancamentoParceladoStandard(array $data, User $user): void
{
    $qtdParcelas = (int) $data['qtd_parcelas'];
    $tipoParcela = $data['tipo_parcela'] ?? 'TOTAL';

    if ($tipoParcela === 'TOTAL') {
        // Divide valor total por quantidade de parcelas
        $valorBaseParcela = intdiv($valorTotal, $qtdParcelas);
    } else {
        // Usa valor como está (é o valor de cada parcela)
        $valorBaseParcela = $valorTotal;
    }

    // Cria parcelas com vencimentos mensais
    for ($i = $parcelaInicial; $i <= $qtdParcelas; $i++) {
        // ... calcula valor da parcela
        // ... calcula data de vencimento
        // ... cria lançamento
    }
}
```

**Comportamento:**

- Quando tipo é "PARCELADO" → cria N lançamentos (qtd_parcelas)
- Se tipo_parcela = "TOTAL" → divide valor total por N
- Se tipo_parcela = "PARCELA" → usa valor como está
- Cada parcela vence em mês diferente
- Se criado como "EFETIVADA", apenas a primeira fica efetivada

---

## ✅ Payload Esperado do Frontend

Quando enviar FIXA:

```json
{
  "descricao": "Salário",
  "valor": "5000,00",
  "tipo_lancamento": "Receita",
  "recorrencia": "FIXA",
  "categoria": "Salário",
  "subcategoria": "Principal",
  "conta_id": 1,
  "data_vencimento": "2025-10-18",
  "data_lancamento": "2025-10-18",
  "status_lancamento": "PENDENTE",
  "mesAno": "2025-10"
}
```

→ Backend cria 12 lançamentos com datas:

- 2025-10-18
- 2025-11-18
- 2025-12-18
- 2026-01-18
- ... até 2026-09-18

---

Quando enviar PARCELADO:

```json
{
  "descricao": "Compra",
  "valor": "1000,00",
  "tipo_lancamento": "Despesa",
  "recorrencia": "PARCELADO",
  "qtd_parcelas": 3,
  "num_parcela": 1,
  "tipo_parcela": "TOTAL",
  "periodicidade": "MENSAL",
  "categoria": "Compras",
  "subcategoria": "Gerais",
  "conta_id": 1,
  "data_vencimento": "2025-10-18",
  "data_lancamento": "2025-10-18",
  "status_lancamento": "PENDENTE",
  "mesAno": "2025-10"
}
```

→ Backend cria 3 lançamentos:

- 1/3 vence 2025-10-18, valor 333,33
- 2/3 vence 2025-11-18, valor 333,33
- 3/3 vence 2025-12-18, valor 333,34 (ajusta resto)

---

## 🧪 Teste os Payloads

### Teste 1: Criar FIXA

```bash
curl -X POST http://localhost/api/lancamentos \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "descricao": "Aluguel",
    "valor": "1500,00",
    "tipo_lancamento": "Receita",
    "recorrencia": "FIXA",
    "categoria": "Aluguel",
    "subcategoria": "Principal",
    "conta_id": 1,
    "data_vencimento": "2025-10-01",
    "data_lancamento": "2025-10-18",
    "status_lancamento": "PENDENTE",
    "mesAno": "2025-10"
  }'
```

**Resultado esperado:**

- 12 lançamentos criados
- Todos com mesmo valor 1500,00
- Vencimentos em 01 de cada mês (próximos 12 meses)
- Todos PENDENTE

---

### Teste 2: Criar PARCELADO

```bash
curl -X POST http://localhost/api/lancamentos \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "descricao": "Importação",
    "valor": "600,00",
    "tipo_lancamento": "Despesa",
    "recorrencia": "PARCELADO",
    "qtd_parcelas": 2,
    "num_parcela": 1,
    "tipo_parcela": "TOTAL",
    "periodicidade": "MENSAL",
    "categoria": "Importação",
    "subcategoria": "Produtos",
    "conta_id": 1,
    "data_vencimento": "2025-10-15",
    "data_lancamento": "2025-10-18",
    "status_lancamento": "PENDENTE",
    "mesAno": "2025-10"
  }'
```

**Resultado esperado:**

- 2 lançamentos criados
- 1/2 valor 300,00 vence 2025-10-15
- 2/2 valor 300,00 vence 2025-11-15
- Ambos PENDENTE

---

## 📊 Resumo

| Tipo           | Comportamento                           |
| -------------- | --------------------------------------- |
| NAO_RECORRENTE | Cria 1 lançamento apenas                |
| FIXA           | Cria 12 lançamentos (próximos 12 meses) |
| PARCELADO      | Cria N lançamentos (qtd_parcelas meses) |

---

## ✅ Campos Obrigatórios por Tipo

### FIXA

- ✅ recorrencia = "FIXA"
- ✅ data_vencimento (base para os 12 meses)
- ✅ tipo_lancamento
- ✅ valor
- ✅ conta_id

### PARCELADO

- ✅ recorrencia = "PARCELADO"
- ✅ qtd_parcelas (>1)
- ✅ num_parcela (inicial, geralmente 1)
- ✅ tipo_parcela ("TOTAL" ou "PARCELA")
- ✅ periodicidade ("MENSAL", "SEMANAL", etc)
- ✅ data_vencimento (base para primeira parcela)
- ✅ valor
- ✅ conta_id

---

## 🎯 Verificação Frontend

O frontend está enviando esses campos corretamente? Verifique no Console (F12):

1. Abra Network
2. Crie uma receita FIXA
3. Procure POST /api/lancamentos
4. Veja o Payload enviado
5. Verifique se tem: `recorrencia: "FIXA"` e todos os campos acima

Se faltar algum campo, o backend retorna erro de validação.

---

**Status:** ✅ BACKEND PRONTO
**Próximo:** Verificar e testar payloads do frontend
**Data:** 2025-10-18
