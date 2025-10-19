# 🧪 Teste - Toggle VALOR TOTAL vs VALOR PARCELA

## Cenário de Teste

### Teste 1: VALOR TOTAL (Padrão)

```
1. Abrir "Nova Receita"
2. Descrição: "Teste Parcela"
3. Valor: 1000
4. Recorrência: "Parcelado" ✅
5. Modal Parcelas:
   - Parcela Inicial: 1
   - Quantidade: 2
   - Periodicidade: Mensal
6. Concluído ✅

✅ RESULTADO ESPERADO:
   Deve exibir: "Em 2x de R$ 500,00"
   Cálculo: 1000 / 2 = 500
```

---

### Teste 2: VALOR PARCELA (Toggle)

```
1. Com o formulário aberto (continuação do Teste 1)
2. No card de recorrência, em "Valor total" vs "Valor parcela"
3. Clique no toggle "Valor parcela" 🎚️
4. Valor continua: 1000
5. Parcelas continua: 2

✅ RESULTADO ESPERADO (CORRIGIDO):
   Deve exibir: "Em 2x de R$ 1.000,00"

⚠️ ANTES (BUG):
   Exibia: "Em 2x de R$ 500,00" ❌ (ERRADO)

🔧 AGORA (CORRIGIDO):
   Exibe: "Em 2x de R$ 1.000,00" ✅ (CORRETO)

📝 LÓGICA:
   - "VALOR TOTAL": valor_parcela = 1000 / 2 = 500 ✅
   - "VALOR PARCELA": valor_parcela = 1000 (já é valor de parcela) ✅
```

---

### Teste 3: Múltiplas Alternâncias

```
1. Com formulário aberto
2. Valor: 600
3. Parcelas: 3

Sequência de clicks no toggle:
① "VALOR TOTAL" → Exibe "Em 3x de R$ 200,00" ✅
② "VALOR PARCELA" → Exibe "Em 3x de R$ 600,00" ✅
③ "VALOR TOTAL" → Exibe "Em 3x de R$ 200,00" ✅
④ "VALOR PARCELA" → Exibe "Em 3x de R$ 600,00" ✅

Cada alternância deve atualizar IMEDIATAMENTE
```

---

### Teste 4: Mudança de Valor com Toggle

```
Cenário A: VALOR TOTAL selecionado
- Valor: 1200
- Parcelas: 4
- Exibe: "Em 4x de R$ 300,00" ✅

Cenário B: Muda para "VALOR PARCELA"
- Mesmo valor: 1200
- Mesmas parcelas: 4
- Exibe: "Em 4x de R$ 1.200,00" ✅

Cenário C: Muda valor para 500 (mantém VALOR PARCELA)
- Novo valor: 500
- Mesmas parcelas: 4
- Exibe: "Em 4x de R$ 500,00" ✅ (valor de cada parcela é 500)
```

---

## 🐛 Bug Report (Histórico)

**Data:** 18/10/2025
**Problema:** Toggle VALOR PARCELA não atualizava o cálculo
**Causa:** Código sempre dividia valor por número de parcelas, independente do toggle
**Status:** ✅ CORRIGIDO em ReceitasView.vue linha 763-780

---

## 📝 Código Corrigido

```typescript
// ANTES (BUG):
const detalheRecorrencia = computed(() => {
  if (
    formData.value.recorrencia === "Parcelado" &&
    formData.value.valor &&
    tempNumParcelas.value > 0
  ) {
    const valorInput = parseFloat(
      formData.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (!isNaN(valorInput) && valorInput > 0) {
      const valorParcela = valorInput / tempNumParcelas.value; // ❌ SEMPRE divide
      const valorFormatado = valorParcela.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return `Em ${tempNumParcelas.value}x de R$ ${valorFormatado}`;
    }
  }
  return "";
});

// DEPOIS (CORRIGIDO):
const detalheRecorrencia = computed(() => {
  if (
    formData.value.recorrencia === "Parcelado" &&
    formData.value.valor &&
    tempNumParcelas.value > 0
  ) {
    const valorInput = parseFloat(
      formData.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (!isNaN(valorInput) && valorInput > 0) {
      let valorParcela: number;

      // ✅ AGORA verifica qual toggle está ativo
      if (tipoCalculoParcela.value === "total") {
        valorParcela = valorInput / tempNumParcelas.value; // VALOR TOTAL: divide
      } else {
        valorParcela = valorInput; // VALOR PARCELA: já é valor da parcela
      }

      const valorFormatado = valorParcela.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return `Em ${tempNumParcelas.value}x de R$ ${valorFormatado}`;
    }
  }
  return "";
});
```

---

## ✅ Checklist de Testes

- [ ] Teste 1: VALOR TOTAL exibe cálculo correto
- [ ] Teste 2: Toggle para VALOR PARCELA atualiza imediatamente
- [ ] Teste 3: Múltiplas alternâncias funcionam corretamente
- [ ] Teste 4: Mudança de valor com toggle ativo funciona
- [ ] Teste 5: Mobile/Tablet responsivo
- [ ] Teste 6: Valores grandes (99999) funcionam
- [ ] Teste 7: Decimal (123,45) funciona
- [ ] Teste 8: Formulário salva com dados corretos
