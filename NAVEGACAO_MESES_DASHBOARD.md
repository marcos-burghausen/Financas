# Navegação de Meses no Dashboard

## 📋 Resumo da Implementação

Adicionada funcionalidade de navegação entre meses no Dashboard, permitindo visualizar dados de meses anteriores e futuros sem sair da página principal.

## 🎯 Objetivos Atingidos

✅ **Navegação de Meses**

- Botões ← e → para navegar entre meses
- Exibição clara do mês/ano selecionado
- Botão "Mês Atual" para retornar ao mês corrente

✅ **Persistência de Seleção**

- Mês selecionado persiste via `userStore.mesAno`
- Sincronizado entre todas as views (Dashboard, Receitas, Despesas)

✅ **Atualização Dinâmica**

- Dashboard recarrega automaticamente ao mudar de mês
- Dados dos KPI cards são atualizados
- Gráficos refletem dados do mês selecionado
- Alertas se ajustam ao novo mês

## 🔧 Implementação Técnica

### Componentes Adicionados

**UI Navigation Block** (antes dos KPI Cards):

```vue
<!-- MONTH NAVIGATION -->
<v-row class="mb-6 align-center">
  <v-col cols="12" class="d-flex align-center justify-space-between">
    <div class="d-flex align-center gap-2">
      <!-- Botão Anterior -->
      <v-btn icon="mdi-chevron-left" @click="navigationMonth('prev')" />
      
      <!-- Exibição do Mês -->
      <div class="text-center">
        <p class="text-subtitle-1 font-weight-bold">
          {{ currentMonthFormatted }}
        </p>
        <p class="text-caption text-grey">
          {{ mesAnoFormatted }}
        </p>
      </div>
      
      <!-- Botão Próximo -->
      <v-btn icon="mdi-chevron-right" @click="navigationMonth('next')" />
    </div>
    
    <!-- Botão Retornar Hoje -->
    <v-btn variant="tonal" color="primary" @click="navigationMonth('today')">
      Mês Atual
    </v-btn>
  </v-col>
</v-row>
```

### Computeds Adicionados

**currentMonthFormatted**

- Formata o mês em "linguagem natural" (ex: "outubro de 2024")
- Baseado em `userStore.mesAno`

**mesAnoFormatted**

- Formato curto (ex: "out/2024")
- Para exibição compacta

### Métodos Adicionados

**navigationMonth(direction: 'prev' | 'next' | 'today')**

```typescript
const navigationMonth = (direction: "prev" | "next" | "today") => {
  const mesAno = userStore.getMesAno(); // "2024-10"
  const [year, month] = mesAno.split("-");
  const current = new Date(`${year}-${month}-01`);

  if (direction === "prev") {
    current.setMonth(current.getMonth() - 1);
  } else if (direction === "next") {
    current.setMonth(current.getMonth() + 1);
  } else if (direction === "today") {
    const today = new Date();
    userStore.setMesAno(today.toISOString().slice(0, 7));
    loadDashboardData();
    return;
  }

  const newMesAno = current.toISOString().slice(0, 7);
  userStore.setMesAno(newMesAno);
  loadDashboardData(); // Recarrega dados automaticamente
};
```

### Monitores Adicionados

**Watch na mudança de mesAno**

```typescript
watch(
  () => userStore.mesAno,
  () => {
    loadDashboardData();
  }
);
```

- Recarrega dashboard quando mês muda
- Sincroniza com mudanças feitas em outras views

### Atualizações do monthDisplay

**Antes:**

```typescript
const monthDisplay = computed(() => {
  const today = new Date();
  return today.toLocaleString("pt-BR", { month: "long", year: "numeric" });
});
```

**Depois:**

```typescript
const monthDisplay = computed(() => {
  const mesAno = userStore.getMesAno(); // Usa mês selecionado
  const [year, month] = mesAno.split("-");
  const date = new Date(`${year}-${month}-01`);
  return date.toLocaleString("pt-BR", { month: "long", year: "numeric" });
});
```

## 📊 Fluxo de Funcionamento

```
Usuário clica em ←
    ↓
navigationMonth('prev') é chamado
    ↓
setMesAno() atualiza store e localStorage
    ↓
Watch detecta mudança no mesAno
    ↓
loadDashboardData() é chamado
    ↓
monthDisplay computed é recalculado
    ↓
Dashboard renderiza com novos dados
```

## ✨ Características

| Feature                   | Comportamento                                      |
| ------------------------- | -------------------------------------------------- |
| **Clique em ←**           | Navega para o mês anterior                         |
| **Clique em →**           | Navega para o próximo mês                          |
| **Clique em "Mês Atual"** | Retorna ao mês corrente                            |
| **Exibição do Mês**       | Mostra mês selecionado em português                |
| **Sincronização**         | Persiste entre views (Receitas/Despesas/Dashboard) |
| **Recarregamento**        | Automático ao mudar de mês                         |

## 🧪 Fluxo de Testes

### Teste 1: Navegação Anterior

1. Abrir Dashboard (deve mostrar mês atual)
2. Clicar em ← (botão anterior)
3. Verificar se mês mudou para anterior
4. KPI cards devem mostrar dados do mês anterior
5. Transações recentes devem ser do mês anterior

### Teste 2: Navegação Próxima

1. Do Dashboard, clicar em → (botão próximo)
2. Verificar se mês avançou
3. Dados devem ser atualizados

### Teste 3: Retornar ao Mês Atual

1. Navegar para alguns meses anteriores/posteriores
2. Clicar em "Mês Atual"
3. Dashboard deve retornar ao mês corrente

### Teste 4: Sincronização Entre Views

1. Dashboard mostrando "Setembro 2024"
2. Navegar para ReceitasView (menu lateral)
3. Verificar se também mostra "Setembro 2024"
4. Retornar para Dashboard
5. Verificar se mantém "Setembro 2024"

### Teste 5: Dados Recarregam

1. Navegação de mês
2. Verificar se todos os dados da dashboard foram atualizados:
   - KPI Cards (valores)
   - Gráfico de Barras (receitas/despesas)
   - Gráfico de Pizza (categorias)
   - Transações Recentes
   - Alertas Dinâmicos

## 📝 Notas Técnicas

- A navegação usa formato ISO "YYYY-MM" internamente
- Exibição para usuário em português via `toLocaleString()`
- Persistência via localStorage através do userStore
- Não há limite de meses navegáveis (pode ir ao futuro se houver dados)

## 🔄 Integrações Relacionadas

- **UserStore**: `mesAno`, `setMesAno()`, `getMesAno()`
- **ReceitasView**: Já tinha navegação similar
- **DespesasView**: Já tinha navegação similar
- **Dashboard**: Agora sincronizado com as outras views

## 🚀 Próximas Melhorias (Opcional)

- [ ] Indicador visual se há dados para o mês selecionado
- [ ] Desabilitar botões se não houver meses anteriores/posteriores
- [ ] Picker de mês/ano (via modal)
- [ ] Animações de transição ao mudar dados
- [ ] Comparação visual entre meses

---

## ✅ Status: IMPLEMENTADO

Funcionalidade de navegação de meses no Dashboard completamente implementada e testada.
