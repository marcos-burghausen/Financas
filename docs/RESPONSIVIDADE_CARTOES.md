# 💳 Responsividade da CartaoCreditoView

## 📋 Resumo das Mudanças

A view de Cartões de Crédito foi completamente redesenhada seguindo o padrão responsivo estabelecido nas views Dashboard, Contas, Receitas e Despesas. A nova implementação inclui:

- ✅ Header responsivo com título adaptativo
- ✅ Botão de menu lateral funcionando em mobile/tablet
- ✅ Cards de resumo com estatísticas dos cartões
- ✅ Grid responsivo de cartões de crédito
- ✅ Dialog com lançamentos (tabela desktop / lista mobile)
- ✅ Dados fictícios para demonstração
- ✅ CSS consistente com as outras views

## 🎨 Estrutura Visual

### 📱 Mobile (< 600px)

```
┌─────────────────────────┐
│ [☰] Cartões        [+]  │ ← Header compacto
├─────────────────────────┤
│   Card Resumo 1         │
│   (Fatura Total)        │
├─────────────────────────┤
│   Card Resumo 2         │
│   (Limite Total)        │
├─────────────────────────┤
│   Card Resumo 3         │
│   (Disponível)          │
├─────────────────────────┤
│   Cartão 1              │
│   (Full Width)          │
├─────────────────────────┤
│   Cartão 2              │
│   (Full Width)          │
└─────────────────────────┘
```

### 📱 Tablet (600px - 960px)

```
┌──────────────────────────────────┐
│ [☰] Cartões de Crédito  [+ Novo] │
├──────────────────────────────────┤
│  Resumo 1      Resumo 2          │
│  (Half)        (Half)            │
├──────────────────────────────────┤
│  Resumo 3                        │
│  (Half)                          │
├──────────────────────────────────┤
│  Cartão 1      Cartão 2          │
│  (Half)        (Half)            │
└──────────────────────────────────┘
```

### 💻 Desktop (>= 1280px)

```
┌─────────────────────────────────────────┐
│ Meus Cartões de Crédito  [+ Novo Cartão]│
├─────────────────────────────────────────┤
│  Resumo 1    Resumo 2    Resumo 3       │
│  (33%)       (33%)       (33%)          │
├─────────────────────────────────────────┤
│  Cartão 1    Cartão 2    Cartão 3       │
│  (33%)       (33%)       (33%)          │
├─────────────────────────────────────────┤
│  Cartão 4                               │
│  (33%)                                  │
└─────────────────────────────────────────┘
```

## 🔧 Implementação Técnica

### 1. Header Responsivo

```vue
<div class="d-flex align-center justify-space-between flex-wrap gap-3 mb-2">
  <div class="d-flex align-center flex-grow-1">
    <v-btn
      icon
      variant="text"
      class="mr-2 d-lg-none menu-button"
      @click="drawer = !drawer"
    >
      <v-icon icon="mdi-menu" size="28" />
    </v-btn>
    <div class="header-content">
      <h1 class="cartoes-title mb-1 d-flex align-center">
        <v-icon
          icon="mdi-credit-card-multiple"
          :size="$vuetify.display.xs ? '24' : '36'"
          class="mr-2 mr-md-3"
          color="warning"
        />
        <span class="d-none d-sm-inline">Meus Cartões de Crédito</span>
        <span class="d-sm-none">Cartões</span>
      </h1>
      <p class="text-caption text-sm-subtitle-1 text-grey mb-0 d-none d-sm-block">
        Gerencie seus cartões e faturas
      </p>
    </div>
  </div>
  <v-btn
    color="warning"
    :prepend-icon="$vuetify.display.xs ? '' : 'mdi-plus'"
    :icon="$vuetify.display.xs ? 'mdi-plus' : false"
    :size="$vuetify.display.xs ? 'default' : 'large'"
    class="flex-shrink-0"
    @click="openAddCardDialog"
  >
    <span v-if="!$vuetify.display.xs">Novo Cartão</span>
  </v-btn>
</div>
```

**Adaptações:**

- **Mobile**: Título "Cartões", botão icon-only, menu visível
- **Desktop**: Título "Meus Cartões de Crédito", botão com texto, menu escondido

### 2. Cards de Resumo

```vue
<v-row class="mb-6">
  <v-col cols="12" sm="6" lg="4">
    <!-- Card Fatura Total -->
  </v-col>
  <v-col cols="12" sm="6" lg="4">
    <!-- Card Limite Total -->
  </v-col>
  <v-col cols="12" sm="6" lg="4">
    <!-- Card Disponível -->
  </v-col>
</v-row>
```

**Breakpoints:**

- `cols="12"`: 1 coluna em mobile (< 600px)
- `sm="6"`: 2 colunas em tablet (600-1280px)
- `lg="4"`: 3 colunas em desktop (>= 1280px)

### 3. Cards dos Cartões

```vue
<v-row>
  <v-col
    v-for="(card, index) in creditCards"
    :key="index"
    cols="12"
    md="6"
    lg="4"
  >
    <v-card
      elevation="4"
      class="credit-card h-100"
      @click="selectCard(card)"
    >
      <!-- Conteúdo do cartão -->
    </v-card>
  </v-col>
</v-row>
```

**Estrutura do Card:**

- **Header**: Gradiente com cor da bandeira (Mastercard/Visa/Elo)
- **Body**:
  - Grid 3 colunas: Limite | Em Aberto | Disponível
  - Barra de progresso com porcentagem
  - Grid 3 colunas: Conta | Fechamento | Vencimento
  - Seção de fatura com status e botão pagar

### 4. Dialog de Lançamentos

```vue
<v-dialog v-model="transactionsDialog" max-width="900" scrollable>
  <v-card>
    <v-card-title>
      <!-- Header com nome do cartão -->
    </v-card-title>
    
    <v-card-text>
      <!-- Desktop: v-table -->
      <v-table class="d-none d-md-table">
        <!-- Colunas: Data, Descrição, Categoria, Parcela, Valor -->
      </v-table>
      
      <!-- Mobile: v-list -->
      <v-list class="d-md-none">
        <!-- Cards compactos com informações -->
      </v-list>
    </v-card-text>
    
    <v-card-actions>
      <!-- Total e botão fechar -->
    </v-card-actions>
  </v-card>
</v-dialog>
```

## 📊 Dados Fictícios

### Cartões Mock

```typescript
const creditCards = ref<CreditCard[]>([
  {
    id: 1,
    name: "Sicredi Mastercard Gold",
    bandeira: "Mastercard",
    limite: 5000,
    saldo: 2847.5,
    conta_pai_name: "Sicredi C/C",
    data_fechamento: "2024-10-25",
    data_vencimento: "2024-11-05",
    total_fatura_vigente: 2847.5,
    status_fatura: "ABERTA",
  },
  // ... mais 3 cartões
]);
```

### Lançamentos Mock

```typescript
const allTransactions: Record<number, Transaction[]> = {
  1: [
    {
      date: "15/10/2024",
      description: "Supermercado Extra",
      category: "Alimentação",
      categoryColor: "success",
      installment: "À vista",
      amount: 345.8,
    },
    // ... mais lançamentos
  ],
};
```

## 🎯 Funcionalidades Implementadas

### 1. Clique no Cartão

- Ao clicar em qualquer cartão, abre o dialog com os lançamentos
- Mostra tabela (desktop) ou lista (mobile)
- Calcula total dos lançamentos

### 2. Menu de Ações

- **Nova Despesa**: Adicionar lançamento no cartão
- **Ver Lançamentos**: Abrir dialog (mesmo que clicar no card)
- **Editar**: Editar dados do cartão

### 3. Botão Pagar

- Registrar pagamento da fatura
- Disponível em cada card

### 4. Estatísticas Calculadas

- **Fatura Total**: Soma de todas as faturas vigentes
- **Limite Total**: Soma de todos os limites
- **Disponível**: Limite total - saldo total
- **Percentual Disponível**: (Disponível / Limite Total) × 100

## 🎨 CSS Responsivo

### Container Principal

```css
@media (max-width: 599px) {
  .cartoes-view {
    padding: 12px !important;
  }
}

@media (min-width: 600px) and (max-width: 959px) {
  .cartoes-view {
    padding: 16px !important;
  }
}

@media (min-width: 960px) {
  .cartoes-view {
    padding: 24px !important;
  }
}
```

### Títulos Responsivos

```css
.cartoes-title {
  font-size: 1.5rem; /* Mobile */
}

@media (min-width: 600px) {
  .cartoes-title {
    font-size: 2rem; /* Tablet */
  }
}

@media (min-width: 960px) {
  .cartoes-title {
    font-size: 2.125rem; /* Desktop */
  }
}
```

### Cards com Hover

```css
.credit-card {
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.credit-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}
```

### Gradientes por Bandeira

```css
.card-header-mastercard {
  background: linear-gradient(135deg, #eb001b 0%, #f79e1b 100%);
}

.card-header-visa {
  background: linear-gradient(135deg, #1a1f71 0%, #0066b2 100%);
}

.card-header-elo {
  background: linear-gradient(135deg, #ffcb05 0%, #000000 100%);
}
```

## 📱 Detalhes Responsivos

### Info Grid

```css
.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

@media (max-width: 599px) {
  .info-grid {
    gap: 8px;
  }

  .info-label {
    font-size: 0.7rem;
  }

  .info-value {
    font-size: 0.8rem;
  }
}
```

### Barra de Progresso

- **Verde**: Utilização < 60%
- **Amarelo**: Utilização 60-80%
- **Vermelho**: Utilização > 80%

```typescript
const getProgressColor = (saldo: number, limite: number): string => {
  const percentual = (saldo / limite) * 100;
  if (percentual >= 80) return "error";
  if (percentual >= 60) return "warning";
  return "success";
};
```

## ✅ Checklist de Validação

### Header

- [ ] Botão menu aparece em telas < 1280px
- [ ] Botão menu desaparece em telas >= 1280px
- [ ] Título muda de "Cartões" para "Meus Cartões de Crédito"
- [ ] Botão "Novo Cartão" vira icon-only em mobile
- [ ] Descrição aparece apenas em desktop

### Cards de Resumo

- [ ] 1 coluna em mobile (< 600px)
- [ ] 2 colunas em tablet (600-1280px)
- [ ] 3 colunas em desktop (>= 1280px)
- [ ] Valores formatados corretamente em BRL
- [ ] Animação de hover funciona

### Cards de Cartões

- [ ] 1 coluna em mobile (< 600px)
- [ ] 2 colunas em tablet (600-960px)
- [ ] 3 colunas em desktop (>= 960px)
- [ ] Gradiente correto por bandeira
- [ ] Barra de progresso com cor dinâmica
- [ ] Todas as informações visíveis
- [ ] Menu de ações funcionando

### Dialog de Lançamentos

- [ ] Tabela aparece apenas em desktop (>= 960px)
- [ ] Lista aparece apenas em mobile (< 960px)
- [ ] Total calculado corretamente
- [ ] Scroll funciona com muitos lançamentos
- [ ] Dialog responsivo (margem em mobile)

## 🚀 Como Testar

### 1. Acesse a view

```
http://localhost:5173/cartoes
```

### 2. Teste os Breakpoints

**375px (Mobile)**

- ✅ Botão menu visível
- ✅ Título "Cartões"
- ✅ Botão icon-only
- ✅ 1 coluna de cards

**768px (Tablet)**

- ✅ Botão menu visível
- ✅ Título completo
- ✅ Botão com texto
- ✅ 2 colunas de cards

**1024px (Tablet Landscape)**

- ✅ Botão menu visível
- ✅ 2 colunas de resumo
- ✅ 2 colunas de cartões

**1280px+ (Desktop)**

- ✅ Botão menu escondido
- ✅ 3 colunas de resumo
- ✅ 3 colunas de cartões

### 3. Teste as Funcionalidades

1. **Clique em um cartão**

   - Dialog abre com lançamentos
   - Tabela em desktop / Lista em mobile
   - Total calculado corretamente

2. **Menu de ações (⋮)**

   - Nova Despesa
   - Ver Lançamentos
   - Editar

3. **Botão Pagar**

   - Console log com dados do cartão

4. **Barra de Progresso**
   - Verde: < 60%
   - Amarelo: 60-80%
   - Vermelho: > 80%

## 📊 Estatísticas dos Cards Mock

| Cartão             | Limite       | Saldo       | Disponível   | % Utilizado | Cor        |
| ------------------ | ------------ | ----------- | ------------ | ----------- | ---------- |
| Sicredi Mastercard | R$ 5.000,00  | R$ 2.847,50 | R$ 2.152,50  | 57%         | 🟢 Verde   |
| Nubank Visa        | R$ 8.000,00  | R$ 4.523,80 | R$ 3.476,20  | 57%         | 🟢 Verde   |
| Inter Mastercard   | R$ 12.000,00 | R$ 8.945,20 | R$ 3.054,80  | 75%         | 🟡 Amarelo |
| C6 Bank Visa       | R$ 15.000,00 | R$ 3.250,00 | R$ 11.750,00 | 22%         | 🟢 Verde   |

**Totais:**

- **Limite Total**: R$ 40.000,00
- **Fatura Total**: R$ 19.566,50
- **Disponível**: R$ 20.433,50
- **% Disponível**: 51%

## 🎯 Melhorias Futuras

### Curto Prazo

- [ ] Integrar com API real
- [ ] Adicionar filtros (bandeira, status)
- [ ] Gráfico de utilização mensal
- [ ] Exportar fatura PDF

### Médio Prazo

- [ ] Comparativo de faturas (mês atual vs anterior)
- [ ] Alertas de vencimento
- [ ] Cashback acumulado
- [ ] Parcelamento detalhado

### Longo Prazo

- [ ] Análise de gastos por cartão
- [ ] Sugestões de melhor cartão para cada compra
- [ ] Simulador de parcelamento
- [ ] Integração com programa de pontos

## 📚 Referências

- **DashboardView**: Padrão de header e cards resumo
- **ContasView**: Estrutura de cards e grid responsivo
- **ReceitasView**: Dialog de lançamentos
- **DespesasView**: Tabela desktop / lista mobile

---

**Documentação criada em:** Outubro 2025  
**Autor:** GitHub Copilot  
**Status:** ✅ Implementado com dados fictícios
