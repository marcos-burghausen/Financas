# 🚀 Guia Rápido: Como Usar as Melhorias UI/UX

**Data**: Outubro 18, 2025  
**Status**: ✅ PRONTO

---

## 📊 TOAST MESSAGES

### Básico

```typescript
import { useToast } from "@/composables/useToast";

export default {
  setup() {
    const toast = useToast();

    return {
      toast,
    };
  },
};
```

### Exemplos de Uso

#### Sucesso

```typescript
// Operação bem-sucedida
const handleSave = async () => {
  try {
    await api.save(data);
    toast.success("Dados salvos com sucesso!");
  } catch (error) {
    toast.error("Erro ao salvar dados");
  }
};
```

#### Erro

```typescript
// Erro com mensagem customizada
const handleDelete = async (id: number) => {
  try {
    await api.delete(id);
    toast.success("Item deletado!");
  } catch (error) {
    toast.error(error.response?.data?.message || "Erro ao deletar");
  }
};
```

#### Aviso

```typescript
// Ação não reversível
const handlePermanentAction = () => {
  toast.warning("Esta ação não pode ser desfeita", 5000); // 5 segundos
};
```

#### Info

```typescript
// Informação
const handleImport = () => {
  toast.info("Importando dados...", 3000);
};
```

### Com Timeout Customizado

```typescript
// Padrão: 4 segundos
toast.success("Mensagem padrão");

// 2 segundos
toast.success("Mensagem rápida", 2000);

// Sem timeout (manter até fechar)
toast.success("Mensagem importante", 0);

// Sem remover
toast.info("Mensagem permanente", 0);
```

---

## 📈 CHARTS - Adicionar ao seu View

### 1. Importar Componente

```vue
<template>
  <v-card>
    <v-card-text>
      <!-- Aqui vai o gráfico -->
      <ChartLine
        :series="lineChartData.series"
        :categories="lineChartData.categories"
        title="Evolução do Saldo"
        color="#2196F3"
      />
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from "vue";

const lineChartData = ref({
  series: [
    {
      name: "Saldo",
      data: [10, 41, 35, 51, 49, 62, 69, 91, 100],
    },
  ],
  categories: ["1/1", "1/2", "1/3", "1/4", "1/5", "1/6", "1/7", "1/8", "1/9"],
});
</script>
```

### 2. Gráfico de Linha (Tendências)

```vue
<ChartLine
  :series="[
    { name: 'Receitas', data: [10, 20, 15, 30, 25, 40] },
    { name: 'Despesas', data: [8, 12, 18, 20, 22, 25] },
  ]"
  :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
  color="#2196F3"
/>
```

### 3. Gráfico de Pizza (Proporções)

```vue
<ChartPie
  :series="[30, 25, 20, 15, 10]"
  :labels="['Salário', 'Freelance', 'Aluguel', 'Alimentação', 'Outros']"
  :colors="['#4caf50', '#66bb6a', '#81c784', '#f44336', '#ff7043']"
/>
```

### 4. Gráfico de Colunas (Comparação)

```vue
<ChartColumn
  :series="[
    { name: 'Receitas', data: [30, 40, 35, 50, 49, 60] },
    { name: 'Despesas', data: [20, 25, 30, 40, 35, 45] },
  ]"
  :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
  :colors="['#4caf50', '#f44336']"
/>
```

### 5. Exemplo Completo em DashboardView

```vue
<template>
  <v-container fluid class="dashboard-view pa-6">
    <v-row>
      <!-- Gráfico de Linha -->
      <v-col cols="12" lg="6">
        <v-card elevation="2">
          <v-card-title>Saldo Mensal</v-card-title>
          <v-card-text>
            <ChartLine
              :series="saldoData.series"
              :categories="saldoData.categories"
              color="#2196F3"
            />
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Gráfico de Pizza -->
      <v-col cols="12" lg="6">
        <v-card elevation="2">
          <v-card-title>Distribuição de Receitas</v-card-title>
          <v-card-text>
            <ChartPie
              :series="receitasData.series"
              :labels="receitasData.labels"
            />
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Gráfico de Colunas -->
      <v-col cols="12">
        <v-card elevation="2">
          <v-card-title>Receitas vs Despesas</v-card-title>
          <v-card-text>
            <ChartColumn
              :series="compareData.series"
              :categories="compareData.categories"
            />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from "vue";

const saldoData = ref({
  series: [
    {
      name: "Saldo",
      data: [10, 20, 15, 30, 25, 40, 35, 50],
    },
  ],
  categories: ["1/1", "1/2", "1/3", "1/4", "1/5", "1/6", "1/7", "1/8"],
});

const receitasData = ref({
  series: [30, 25, 20, 15, 10],
  labels: ["Salário", "Freelance", "Investimentos", "Aluguel", "Outros"],
});

const compareData = ref({
  series: [
    { name: "Receitas", data: [30, 40, 35, 50] },
    { name: "Despesas", data: [20, 25, 30, 40] },
  ],
  categories: ["Semana 1", "Semana 2", "Semana 3", "Semana 4"],
});
</script>
```

---

## ✨ ANIMATIONS

### Usar Classes Globais

#### No Template

```vue
<template>
  <!-- Hover Effects -->
  <v-card class="card-hover">Flutuação ao hover</v-card>
  <div class="hover-lift">Levanta</div>
  <div class="hover-scale">Escala</div>

  <!-- Transitions -->
  <div class="fade-in">Fade In</div>
  <div class="slide-in-left">Slide from left</div>
  <div class="slide-in-up">Slide from bottom</div>

  <!-- Spinners -->
  <v-icon class="spinner-rotate">mdi-loading</v-icon>

  <!-- Lists -->
  <TransitionGroup name="list" tag="div">
    <div v-for="item in items" :key="item.id" class="list-item">
      {{ item.name }}
    </div>
  </TransitionGroup>
</template>
```

#### No Script

```typescript
// Adicionar classe dinamicamente
const showAnimation = ref(false)

const triggerAnimation = () => {
  showAnimation.value = true
  setTimeout(() => {
    showAnimation.value = false
  }, 3000)
}

// Usar com :class
:class="{ 'fade-in': showAnimation }"
```

### Custom Transitions

```vue
<template>
  <RouterView v-slot="{ Component }">
    <Transition name="fade-slide" mode="out-in" appear>
      <component :is="Component" />
    </Transition>
  </RouterView>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
```

---

## 📋 CHECKLIST: Integrar em Seus Views

- [ ] Importar `useToast` no seu view
- [ ] Usar `toast.success()`, `toast.error()` em operações
- [ ] Adicionar pelo menos 1 gráfico ao view
- [ ] Testar em mobile e desktop
- [ ] Verificar animações funcionando

---

## 🎯 Próximos Passos

1. ✅ Integrar toasts em LoginView e CadastroView (JÁ FEITO)
2. 🔄 Adicionar gráficos ao DashboardView
3. 🔄 Adicionar toasts a todas operações CRUD
4. 🔄 Implementar page transitions em App.vue
5. 🔄 Adicionar loading skeletons

---

## 📞 Suporte

### Problema: Toast não aparece

```typescript
// Verificar se componente está em App.vue
// App.vue deve ter: <ToastNotification />
```

### Problema: Gráfico não renderiza

```typescript
// Verificar dados:
console.log(chartData.value.series);
console.log(chartData.value.categories);

// Deve ter mesma quantidade de items
// series[0].data.length === categories.length
```

### Problema: Animações lentas

```scss
// Aumentar performance deletando classes não usadas
// Manter apenas as que você usa
```

---

**Status**: ✅ Guia Completo  
**Próximo**: Integrar em todos os views
