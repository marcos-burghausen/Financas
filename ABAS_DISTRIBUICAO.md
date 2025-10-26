# 📊 Abas de Distribuição - Despesas e Receitas

**Data**: October 26, 2025
**Status**: ✅ Implementado

---

## 🎨 O que foi adicionado

### Card "Distribuição" agora possui **2 ABAS**:

1. **ABA DESPESAS** 📉

   - Gráfico de pizza mostrando distribuição de despesas por categoria
   - Apenas despesas EFETIVADAS
   - Ícone: `mdi-cash-remove`
   - Cores: Vermelhos e tons neutros

2. **ABA RECEITAS** 📈
   - Gráfico de pizza mostrando distribuição de receitas por categoria
   - Apenas receitas EFETIVADAS
   - Ícone: `mdi-cash-plus`
   - Cores: Verdes e tons vibrantes

---

## 🔧 Implementação Técnica

### Frontend: `/frontend/src/views/DashboardView.vue`

#### 1️⃣ Novas variáveis ref adicionadas:

```typescript
// Tabs para distribuição (despesas e receitas)
const distribuicaoTab = ref<"despesas" | "receitas">("despesas");

// Chart series separadas para cada tipo
const chartSeriesDespesas = ref<any>([]);
const chartSeriesReceitas = ref<any>([]);
const chartOptionsDespesas = ref<any>({});
const chartOptionsReceitas = ref<any>({});
```

#### 2️⃣ Template do card:

```vue
<!-- Tabs -->
<v-tabs v-model="distribuicaoTab" align-tabs="center" class="px-4">
  <v-tab value="despesas">
    <v-icon icon="mdi-cash-remove" start size="18" />
    Despesas
  </v-tab>
  <v-tab value="receitas">
    <v-icon icon="mdi-cash-plus" start size="18" />
    Receitas
  </v-tab>
</v-tabs>

<!-- Despesas Tab -->
<div v-if="distribuicaoTab === 'despesas'">
  <apexchart
    type="donut"
    :options="chartOptionsDespesas"
    :series="chartSeriesDespesas"
    height="350"
  />
</div>

<!-- Receitas Tab -->
<div v-if="distribuicaoTab === 'receitas'">
  <apexchart
    type="donut"
    :options="chartOptionsReceitas"
    :series="chartSeriesReceitas"
    height="350"
  />
</div>
```

#### 3️⃣ Script - Cálculos de categorias:

**Despesas**:

```typescript
try {
  const categoriaDespesasMap = new Map<string, number>();

  lancamentosDespesas.forEach((item: any) => {
    if (item.status_lancamento === "EFETIVADA") {
      const categoria = item.categoria || "Outros";
      const valor = item.valor || 0;
      categoriaDespesasMap.set(categoria, (categoriaDespesasMap.get(categoria) || 0) + valor);
    }
  });

  const labelsDespesas = Array.from(categoriaDespesasMap.keys());
  const valuesDespesas = Array.from(categoriaDespesasMap.values());
  const totalDespesasGraf = valuesDespesas.reduce((a, b) => a + b, 0);
  const percentuaisDespesas = valuesDespesas.map(v => (totalDespesasGraf > 0 ? (v / totalDespesasGraf) * 100 : 0));

  chartSeriesDespesas.value = percentuaisDespesas.length > 0 ? percentuaisDespesas : [100];
  chartOptionsDespesas.value = { /* opções do gráfico */ };
}
```

**Receitas**:

```typescript
try {
  const categoriaReceitasMap = new Map<string, number>();

  lancamentosReceitas.forEach((item: any) => {
    if (item.status_lancamento === "EFETIVADA") {
      const categoria = item.categoria || "Outros";
      const valor = item.valor || 0;
      categoriaReceitasMap.set(categoria, (categoriaReceitasMap.get(categoria) || 0) + valor);
    }
  });

  const labelsReceitas = Array.from(categoriaReceitasMap.keys());
  const valuesReceitas = Array.from(categoriaReceitasMap.values());
  const totalReceitasGraf = valuesReceitas.reduce((a, b) => a + b, 0);
  const percentuaisReceitas = valuesReceitas.map(v => (totalReceitasGraf > 0 ? (v / totalReceitasGraf) * 100 : 0));

  chartSeriesReceitas.value = percentuaisReceitas.length > 0 ? percentuaisReceitas : [100];
  chartOptionsReceitas.value = { /* opções do gráfico */ };
}
```

---

## 🎨 Cores Utilizadas

### Despesas (Aba 1):

- `#FF6384` - Rosa
- `#36A2EB` - Azul
- `#FFCE56` - Amarelo
- `#4BC0C0` - Turquesa
- `#9966FF` - Roxo
- `#C9CBCF` - Cinza
- `#FF7043` - Laranja

### Receitas (Aba 2):

- `#66BB6A` - Verde
- `#42A5F5` - Azul claro
- `#AB47BC` - Roxo
- `#EC407A` - Rosa
- `#29B6F6` - Azul
- `#78909C` - Cinza azulado
- `#FFCA28` - Amarelo

---

## ✨ Features

✅ **Abas interativas** com ícones e cores distintas
✅ **Cálculo automático** de percentuais por categoria
✅ **Apenas transações EFETIVADAS** aparecem nos gráficos
✅ **Dados em tempo real** do backend
✅ **Mensagens amigáveis** quando não há dados
✅ **Tooltips com valores** em R$ formatado
✅ **Rótulos com percentuais** em cada fatia

---

## 🚀 Como Usar

1. Acesse o Dashboard
2. Procure o card **"Distribuição"**
3. Clique na aba **"Despesas"** para ver distribuição de despesas por categoria
4. Clique na aba **"Receitas"** para ver distribuição de receitas por categoria
5. Hover sobre as fatias para ver o valor em R$

---

## 📝 Próximas Melhorias (opcional)

- [ ] Adicionar filtro de período (últimos 3, 6, 12 meses)
- [ ] Exportar gráfico como imagem
- [ ] Comparar distribuição mês anterior
- [ ] Mostrar top 5 categorias com valores
- [ ] Animação ao trocar de aba
- [ ] Mobile: adaptar tamanho do gráfico
