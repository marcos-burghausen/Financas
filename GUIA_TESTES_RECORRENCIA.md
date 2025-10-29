# 🧪 GUIA DE TESTES - Funcionalidades de Recorrência CartaoCreditoView

## 📋 TESTES DE INTERFACE

### **Teste 1: Abrir Modal de Recorrência**

```
1. Abrir CartaoCreditoView
2. Clicar no botão "+" em qualquer cartão
3. Dialog abre com formulário
4. Clicar no campo "Recorrência"
✅ ESPERADO: Modal abre com 3 opções
   - Não recorrente ☐
   - Fixa ☐
   - Parcelado ☐
```

### **Teste 2: Selecionar "Não Recorrente"**

```
1. Na modal de recorrência, clicar em "Não recorrente"
2. Modal fecha
✅ ESPERADO:
   - Campo mostra: "Não recorrente"
   - Nenhum detalhe adicional
   - Nenhum campo extra aparece
   - Toggle desaparece (se estava visível)
```

### **Teste 3: Selecionar "Fixa"**

```
1. Clicar em "Recorrência"
2. Selecionar "Fixa"
3. Modal fecha
✅ ESPERADO:
   - Campo mostra: "Fixa"
   - Nenhum detalhe adicional
   - Nenhum campo extra aparece
   - Toggle desaparece
```

### **Teste 4: Selecionar "Parcelado" - Abertura Automática**

```
1. Clicar em "Recorrência"
2. Selecionar "Parcelado"
✅ ESPERADO:
   - Modal de recorrência fecha
   - Modal de parcelas abre AUTOMATICAMENTE
   - Campo de recorrência mostra "Parcelado"
   - Detalhe mostra: "2x de R$ 0,00" (valores padrão)
```

### **Teste 5: Modal de Parcelas - Layout Correto**

```
1. Na modal de parcelas, verificar campos presentes
✅ ESPERADO:
   - [Título] "Configurar Parcelas"
   - [Campo 1] "Parcela Inicial:"
     └─ [-] [Input: 1] [+]
   - [Divider]
   - [Campo 2] "Quantidade:"
     └─ [-] [Input: 2] [+]
   - [Divider]
   - [Campo 3] "Periodicidade:"
     └─ [Dropdown: Mensal ▼]
   - [Botões] [Cancelar] [Concluído]
```

### **Teste 6: Botões +/- de Parcela Inicial**

```
Cenário A: Limite inferior
1. Parcela Inicial está em 1
2. Clicar [-]
✅ ESPERADO: Botão fica DESABILITADO

Cenário B: Limite superior
1. Parcela Inicial está em 2, Quantidade está em 2
2. Clicar [+] em Parcela Inicial
✅ ESPERADO: Botão fica DESABILITADO

Cenário C: Valor normal
1. Parcela Inicial = 1, Quantidade = 5
2. Clicar [+]
✅ ESPERADO: Parcela Inicial = 2
```

### **Teste 7: Botões +/- de Quantidade**

```
Cenário A: Limite inferior
1. Quantidade está em 2
2. Clicar [-]
✅ ESPERADO: Botão fica DESABILITADO

Cenário B: Sem limite superior
1. Quantidade está em 10
2. Clicar [+]
✅ ESPERADO: Quantidade = 11

Cenário C: Aumentar quantidade
1. Quantidade = 3, Parcela Inicial = 3
2. Clicar [+] para aumentar Quantidade
✅ ESPERADO:
   - Quantidade = 4
   - Parcela Inicial = 3 (não reseta)
```

### **Teste 8: Dropdown de Periodicidade**

```
1. Clicar no dropdown de Periodicidade
✅ ESPERADO: Mostra 4 opções:
   - Mensal (selecionado por padrão)
   - Semanal
   - Quinzenal
   - Bimestral

2. Selecionar "Quinzenal"
✅ ESPERADO: Dropdown mostra "Quinzenal"
```

### **Teste 9: Botão Cancelar**

```
1. Abrir modal de parcelas
2. Mudar valores:
   - Parcela Inicial: 2
   - Quantidade: 5
   - Periodicidade: Semanal
3. Clicar "Cancelar"
✅ ESPERADO:
   - Modal fecha
   - Valores mantêm ANTERIOR (não aplica mudanças)
```

### **Teste 10: Botão Concluído**

```
1. Na modal, configurar:
   - Parcela Inicial: 2
   - Quantidade: 4
   - Periodicidade: Bimestral
2. Clicar "Concluído"
✅ ESPERADO:
   - Modal fecha
   - Campo recorrência mostra "Parcelado"
   - Detalhe mostra: "4x de R$ 0,00" (se valor for 0)
   - Toggle de cálculo aparece
```

---

## 📊 TESTES DE LÓGICA

### **Teste 11: Toggle de Cálculo "Valor Total"**

```
Pré-condições:
- Recorrência: "Parcelado"
- Valor: "300,00"
- Quantidade: "3"

1. Certificar que toggle mostra "[Valor total] [Valor parcela]"
2. Clicar em "Valor total"
3. Verificar detalhe
✅ ESPERADO:
   - Detalhe mostra: "3x de R$ 100,00"
   - (300 ÷ 3 = 100)
```

### **Teste 12: Toggle de Cálculo "Valor Parcela"**

```
Pré-condições:
- Recorrência: "Parcelado"
- Valor: "100,00"
- Quantidade: "3"
- Toggle em "Valor parcela"

1. Verificar detalhe
✅ ESPERADO:
   - Detalhe mostra: "3x de R$ 100,00"
   - (Valor já é de 1 parcela)
```

### **Teste 13: Detalhe Atualiza em Tempo Real**

```
Pré-condições:
- Recorrência: "Parcelado"
- Valor: "300,00"
- Quantidade: "2"
- Toggle: "Valor total"
- Detalhe deve mostrar: "2x de R$ 150,00"

1. Clicar ícone ✏️ para editar parcelas
2. Mudar Quantidade de 2 para 5
3. Clicar "Concluído"
✅ ESPERADO:
   - Detalhe atualiza para: "5x de R$ 60,00"
   - (300 ÷ 5 = 60)
```

### **Teste 14: Detalhe com Valor Centavos**

```
Pré-condições:
- Valor: "500,00"
- Quantidade: "3"
- Toggle: "Valor total"

1. Verificar detalhe
✅ ESPERADO:
   - Detalhe mostra: "3x de R$ 166,67"
   - (500 ÷ 3 = 166,666... → arredonda para 166,67)
```

### **Teste 15: Reset ao Fechar Dialog**

```
Pré-condições:
- Configuração anterior:
  * Parcela Inicial: 2
  * Quantidade: 5
  * Periodicidade: Semanal
  * Tipo Cálculo: "Valor parcela"

1. Fechar dialog de lançamento
2. Abrir novo lançamento
✅ ESPERADO:
   - Parcela Inicial: 1 (resetado)
   - Quantidade: 2 (resetado)
   - Periodicidade: Mensal (resetado)
   - Tipo Cálculo: "total" (resetado)
```

---

## 🔌 TESTES DE BACKEND

### **Teste 16: Payload Não Recorrente**

```
1. Configurar:
   - Descrição: "Teste"
   - Valor: "50,00"
   - Recorrência: "Não recorrente"
   - Categoria: "Compras"
   - Conta: Selecionada

2. Clicar "Salvar"
3. Capturar payload enviado (console)

✅ ESPERADO no payload:
{
  "descricao": "Teste",
  "valor": "50,00",
  "recorrencia": "NAO_RECORRENTE",
  "qtd_parcelas": null,
  "num_parcela": null,
  "tipo_parcela": null,
  "periodicidade": null,
  ...
}
```

### **Teste 17: Payload Fixa**

```
1. Configurar:
   - Recorrência: "Fixa"
   - Outros campos normalmente

2. Clicar "Salvar"
3. Capturar payload

✅ ESPERADO:
{
  "recorrencia": "FIXA",
  "qtd_parcelas": null,
  "num_parcela": null,
  "tipo_parcela": null,
  "periodicidade": null,
  ...
}
```

### **Teste 18: Payload Parcelado Completo**

```
1. Configurar:
   - Valor: "300,00"
   - Recorrência: "Parcelado"
   - Parcela Inicial: 1
   - Quantidade: 3
   - Periodicidade: "Mensal"
   - Tipo Cálculo: "Valor total"

2. Clicar "Salvar"
3. Capturar payload

✅ ESPERADO:
{
  "valor": "300,00",
  "recorrencia": "PARCELADO",
  "qtd_parcelas": 3,
  "num_parcela": 1,
  "tipo_parcela": "total",
  "periodicidade": "MENSAL",
  ...
}
```

### **Teste 19: Resposta de Sucesso**

```
1. Preencher formulário completo
2. Clicar "Salvar"
3. Aguardar resposta

✅ ESPERADO:
- Toast verde: "Lançamento adicionado com sucesso!"
- Dialog fecha
- Lista é recarregada
```

### **Teste 20: Resposta de Erro**

```
1. Sem conectar backend (ou backend retorna erro)
2. Tentar salvar lançamento

✅ ESPERADO:
- Toast vermelho: "Erro ao salvar lançamento"
- Dialog permanece aberto
- Dados não são perdidos
```

---

## 🎨 TESTES DE RESPONSIVIDADE

### **Teste 21: Mobile (< 600px)**

```
1. Abrir em dispositivo mobile
2. Clicar para abrir dialog
3. Verificar modal de parcelas

✅ ESPERADO:
- Modal ocupa ~90% da largura
- Campos são legíveis
- Botões +/- funcionam
- Sem overflow horizontal
```

### **Teste 22: Tablet (600px - 960px)**

```
1. Abrir em tablet
2. Testar todos os campos

✅ ESPERADO:
- Layout bem distribuído
- Sem quebras estranhas
- Toggle de cálculo aparece corretamente
```

### **Teste 23: Desktop (> 960px)**

```
1. Abrir em desktop
2. Verificar dialog

✅ ESPERADO:
- Dialog tem ~600px de largura
- Espaçamento adequado
- Todos os elementos visíveis
```

---

## 🔄 TESTES DE INTEGRAÇÃO

### **Teste 24: Fluxo Completo - Não Recorrente**

```
1. Abrir CartaoCreditoView
2. Clicar + em um cartão
3. Preencher:
   - Descrição: "Compra X"
   - Valor: "50,00"
   - Recorrência: "Não recorrente"
   - Categoria: "Compras"
   - Outros campos...
4. Salvar

✅ ESPERADO:
- Lançamento criado no backend
- Toast de sucesso
- Dialog fecha
- Lista recarrega
```

### **Teste 25: Fluxo Completo - Parcelado**

```
1. Abrir CartaoCreditoView
2. Clicar + em um cartão
3. Preencher:
   - Descrição: "Compra parcelada"
   - Valor: "300,00"
   - Recorrência: "Parcelado"
     ↓ (modal abre automático)
     - Parcela: 1
     - Quantidade: 3
     - Periodicidade: Mensal
   - Toggle: "Valor total"
   - Categoria: "Compras"
   - Outros campos...
4. Verificar detalhe: "3x de R$ 100,00"
5. Salvar

✅ ESPERADO:
- Payload correto enviado
- 3 lançamentos criados (ou 1 com parcelas conforme backend)
- Toast de sucesso
- Dialog fecha
```

---

## 📝 CHECKLIST FINAL

### Antes de ir para PRODUÇÃO:

- [ ] Teste 1-15: Todos os testes de interface passam
- [ ] Teste 16-20: Todos os testes de backend passam
- [ ] Teste 21-23: Responsividade OK
- [ ] Teste 24-25: Fluxo completo OK
- [ ] Console sem erros
- [ ] Payload correto com mapeamento MAIÚSCULO
- [ ] Backend aceita campos: qtd_parcelas, num_parcela, tipo_parcela, periodicidade
- [ ] Lançamentos são criados corretamente
- [ ] Recorrência é salva no banco
- [ ] Parcelas são processadas conforme esperado

---

## 🐛 PROBLEMAS POTENCIAIS

| Problema                  | Como Testar                   | Solução                                              |
| ------------------------- | ----------------------------- | ---------------------------------------------------- |
| Modal não abre automático | Selecionar "Parcelado"        | Verificar `selecionarRecorrenciaTransaction()`       |
| Detalhe não atualiza      | Mudar quantidade              | Verificar computed `detalheRecorrenciaTransaction`   |
| Botões +/- não funcionam  | Clicar em + na quantidade     | Verificar refs `tempNumParcelasTransaction`          |
| Toggle não aparece        | Selecionar "Parcelado"        | Verificar `v-if` no template                         |
| Payload vazio             | Abrir console e verificar log | Verificar `console.log("Payload enviado:", payload)` |
| Erro 422 no backend       | Tentar salvar                 | Verificar mapeamento de campos no payload            |

---

## 🚀 CONCLUSÃO

Todos os 25 testes cobrem:

- ✅ Interface e UX
- ✅ Validação e limites
- ✅ Lógica de cálculo
- ✅ Backend integration
- ✅ Responsividade
- ✅ Casos extremos

**Se todos passarem, o sistema está pronto para PRODUÇÃO! ✅**
