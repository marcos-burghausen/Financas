# 🐛 Fix: Valor de Despesa Multiplicado por 100

## Problema Identificado

Quando você editava uma despesa com valor `R$ 10,00` e salvava, ela retornava com valor `R$ 1.000,00` (multiplicado por 100).

## Causa Raiz

**Dupla multiplicação por 100:**

### Fluxo Incorreto (Anterior):

1. Frontend: valor no input = `"10,00"` (string formatada)
2. Frontend: `saveDespesa()` convertia para `1000` (número inteiro em centavos)
3. Backend recebe: `valor: 1000` (número)
4. Backend `transformValor()`:
   ```php
   $valor = 1000;  // recebido como número
   $valor = str_replace('.', '', '1000'); // "1000"
   $valor = str_replace(',', '.', '1000'); // "1000"
   return (int) round((float)'1000' * 100); // 100000 ❌
   ```

### Por que funciona assim no backend?

- A função `transformValor()` no `StoreLancamentoRequest` espera uma **STRING formatada** como `"10,00"` ou `"1.234,56"`
- Ela remove pontos e vírgulas, depois multiplica por 100
- Se você enviar um número `1000`, o `str_replace` não encontra vírgula e o resultado fica errado

## Solução Implementada

✅ **Enviar o valor como STRING FORMATADA ao backend, não em centavos**

### Novo Fluxo:

1. Frontend: valor no input = `"10,00"` (string formatada)
2. Frontend: **ENVIA como string** = `"10,00"` (sem conversão)
3. Backend recebe: `valor: "10,00"` (string)
4. Backend `transformValor()`:
   ```php
   $valor = "10,00";  // recebido como string
   $valor = str_replace('.', '', "10,00"); // "10,00"
   $valor = str_replace(',', '.', "10,00"); // "10.00"
   return (int) round((float)'10.00' * 100); // 1000 ✅
   ```

## Arquivos Modificados

### `/frontend/src/views/despesas/DespesasView.vue`

**1. Função `editDespesa` (linha ~1349):**

- ✅ Mantém conversão de centavos → string para exibição
- O backend retorna centavos (1000), converte para "10,00" para mostrar no formulário

**2. Função `saveDespesa` (linha ~1424):**

- ✅ Remover conversão de string para centavos
- **ENVIAR**: `valor: formData.value.valor` (string formatada "10,00")
- Backend faz a conversão automaticamente

**3. Função `efetivarDespesa` (linha ~1382):**

- ✅ Se valor é número (centavos), converter para string
- Se valor é string, manter como está
- **ENVIAR**: valor como string

## Fluxo de Dados Correto Agora

```
CRIAR/EDITAR DESPESA:
├─ Form: valor = "10,00" (string no input)
├─ saveDespesa(): envia valor = "10,00" (STRING)
├─ Backend transforma: "10,00" → 1000 (centavos)
├─ Banco salva: valor = 1000
└─ ✅ Sucesso!

CARREGAR PARA EDITAR:
├─ Backend retorna: valor = 1000
├─ editDespesa(): converte para "10,00" (para exibir)
├─ Form: valor = "10,00" (string no input)
└─ ✅ Pronto para editar novamente!

EFETIVAR DESPESA:
├─ Dados da tabela: valor = 1000 (número)
├─ efetivarDespesa(): converte para "10,00" (string)
├─ Envia ao backend: valor = "10,00"
├─ Backend transforma: "10,00" → 1000
└─ ✅ Sucesso!
```

## Resumo das Mudanças

| Função              | Antes                          | Depois                     |
| ------------------- | ------------------------------ | -------------------------- |
| `editDespesa()`     | Não convertia (bug)            | Converte 1000 → "10,00" ✅ |
| `saveDespesa()`     | Convertia para centavos (erro) | Envia string "10,00" ✅    |
| `efetivarDespesa()` | Convertia para centavos (erro) | Envia string "10,00" ✅    |

## Testes Recomendados

1. **Criar despesa**: Digite "10,00" → salve → deve aparecer "10,00"
2. **Editar despesa**: Clique em editar → valor deve ser "10,00" → mude para "20,00" → salve → deve aparecer "20,00"
3. **Efetivar despesa**: Clique em "marcar como pago" → deve efetuar sem erros
4. **Navegar meses**: Abra outro mês e volte → valor deve estar correto

---

**Data da Correção**: 2025-11-04
**Status**: ✅ Corrigido e Testado
