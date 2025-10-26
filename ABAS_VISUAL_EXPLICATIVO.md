# 📊 Abas de Distribuição - Visual Explicativo

## 🎨 Como fica o Card de Distribuição

```
┌─────────────────────────────────────────────┐
│  📊 Distribuição                            │
├─────────────────────────────────────────────┤
│ ┌─ ABA 1 ─┐    ┌─ ABA 2 ─┐                 │
│ │ 📉      │    │ 📈      │                 │
│ │Despesas │    │Receitas │                 │
│ └─────────┘    └─────────┘                 │
├─────────────────────────────────────────────┤
│                                             │
│  CONTEÚDO DA ABA SELECIONADA:               │
│                                             │
│      Gráfico de Pizza (Donut)               │
│                                             │
│   Mostrando distribuição de categorias      │
│   com percentuais e cores específicas       │
│                                             │
│   Legenda na base do gráfico                │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🔄 Comportamento das Abas

### ABA "DESPESAS" (Padrão)

```
Aba SELECIONADA:
├── Ícone: 📉 (cash-remove) - Vermelho
├── Gráfico: Despesas por categoria
├── Cores: Vermelhos, laranjas, tons neutros
├── Dados: Apenas despesas EFETIVADAS
├── Tooltip: Mostra valor em R$ ao passar mouse
└── Mensagem vazia: "Nenhuma despesa efetivada"
```

### ABA "RECEITAS" (Ao clicar)

```
Aba SELECIONADA:
├── Ícone: 📈 (cash-plus) - Verde
├── Gráfico: Receitas por categoria
├── Cores: Verdes, azuis, tons vibrantes
├── Dados: Apenas receitas EFETIVADAS
├── Tooltip: Mostra valor em R$ ao passar mouse
└── Mensagem vazia: "Nenhuma receita efetivada"
```

---

## 📱 Responsividade

```
Desktop (lg):
┌─────────────────────────────────────────┐
│  Receitas      Despesas    Distribuição │
│  (1/3)         (1/3)         (1/3)       │
│   Card          Card          Card       │
│  (com abas)                              │
└─────────────────────────────────────────┘

Tablet (md/sm):
┌───────────────────────┐
│  Receitas  Despesas   │
│  (1/2)      (1/2)     │
├───────────────────────┤
│ Distribuição (1/1)    │
│   (com abas)          │
└───────────────────────┘

Mobile (xs):
┌─────────────────┐
│ Receitas (1/1)  │
├─────────────────┤
│ Despesas (1/1)  │
├─────────────────┤
│ Distribuição    │
│   (com abas)    │
└─────────────────┘
```

---

## 🎯 Exemplo de Dados

### Aba DESPESAS com dados:

```json
{
  "Alimentação": 35.5%,
  "Transporte": 20.3%,
  "Moradia": 25.8%,
  "Lazer": 12.4%,
  "Outros": 6.0%
}

Cores Utilizadas:
├── Alimentação (Rosa): #FF6384
├── Transporte (Azul): #36A2EB
├── Moradia (Amarelo): #FFCE56
├── Lazer (Turquesa): #4BC0C0
└── Outros (Roxo): #9966FF
```

### Aba RECEITAS com dados:

```json
{
  "Salário": 70.2%,
  "Freelance": 15.8%,
  "Investimentos": 10.5%,
  "Outros": 3.5%
}

Cores Utilizadas:
├── Salário (Verde): #66BB6A
├── Freelance (Azul claro): #42A5F5
├── Investimentos (Roxo): #AB47BC
└── Outros (Amarelo): #FFCA28
```

---

## 🎬 Interações Possíveis

1. **Clicar em uma aba**
   → Muda o gráfico exibido
   → Animação suave de transição
   → Mantém scroll position

2. **Passar mouse sobre fatia**
   → Mostra tooltip com:

   - Nome da categoria
   - Percentual
   - Valor em R$ formatado

3. **Legendas clicáveis** (opcional do ApexCharts)
   → Clicar em categoria na legenda pode destacar

---

## ✅ Verificação

- [x] Template com v-tabs implementado
- [x] Lógica de cálculo de despesas
- [x] Lógica de cálculo de receitas
- [x] Cores distintas por tipo
- [x] Tratamento de erros
- [x] Mensagens quando vazio
- [x] Tooltips formatados em R$
- [x] Responsivo para mobile

---

## 📝 Código-chave para debug

```typescript
// Verificar qual aba está selecionada
console.log("Aba selecionada:", distribuicaoTab.value);

// Verificar dados de despesas
console.log("Despesas:", chartSeriesDespesas.value);
console.log("Opções Despesas:", chartOptionsDespesas.value);

// Verificar dados de receitas
console.log("Receitas:", chartSeriesReceitas.value);
console.log("Opções Receitas:", chartOptionsReceitas.value);

// Verificar lancamentos originais
console.log("Lançamentos Despesas:", lancamentosDespesas);
console.log("Lançamentos Receitas:", lancamentosReceitas);
```
