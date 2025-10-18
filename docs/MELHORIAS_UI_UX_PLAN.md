# 📊 Melhorias UI/UX - Charts, Animations & Toast Messages

**Versão**: 2.0  
**Data**: Outubro 18, 2025  
**Status**: 🔄 PLANEJAMENTO  
**Escopo**: Animações, Gráficos, Notificações

---

## 🎯 Objetivo

Melhorar a experiência do usuário adicionando:

1. **📊 Gráficos Interativos** (ApexCharts)
2. **✨ Animações Suaves** (Transitions, Page Changes)
3. **🔔 Toast Messages** (Snackbar melhorado com store)

---

## 📦 Dependências Disponíveis

### Já Instaladas ✅

```json
{
  "apexcharts": "^4.5.0",
  "vue-chartjs": "^5.2.0",
  "vue3-apexcharts": "^1.8.0",
  "chart.js": "^4.4.0"
}
```

### Recomendadas

- **Animações**: Vue Transition Groups (built-in)
- **Toast**: Vuetify Snackbar (já temos)

---

## 🚀 Implementações Planejadas

### 1️⃣ Toast Message System (PRIORITY: 🔴 HIGH)

#### Store: `src/store/toast.ts`

```typescript
interface Toast {
  id: string;
  message: string;
  color: "success" | "error" | "warning" | "info";
  timeout?: number;
  icon?: string;
  position?: "top" | "bottom";
}

export const useToastStore = defineStore("toast", () => {
  const toasts = ref<Toast[]>([]);

  const addToast = (toast: Toast) => {
    // Auto-remove after timeout
  };

  const removeToast = (id: string) => {};
  const clearAll = () => {};

  return { toasts, addToast, removeToast, clearAll };
});
```

#### Composable: `src/composables/useToast.ts`

```typescript
export const useToast = () => {
  const toastStore = useToastStore();

  return {
    success: (message: string, timeout?: number) => {},
    error: (message: string, timeout?: number) => {},
    warning: (message: string, timeout?: number) => {},
    info: (message: string, timeout?: number) => {},
  };
};
```

#### Componente Global: `src/components/ToastNotification.vue`

```vue
<template>
  <v-snackbar
    v-for="toast in toastStore.toasts"
    :key="toast.id"
    v-model="toast.show"
    :color="toast.color"
  >
    <div class="d-flex align-center">
      <v-icon v-if="toast.icon" :icon="toast.icon" class="mr-2" />
      {{ toast.message }}
    </div>
  </v-snackbar>
</template>
```

**Aplicações**:

- ✅ Salvar formulários
- ✅ Deletar itens
- ✅ Erros de validação
- ✅ Sucesso de operações

---

### 2️⃣ Dashboard Charts (PRIORITY: 🟡 MEDIUM)

#### DashboardView - Gráfico de Receitas vs Despesas

```typescript
// ApexChart tipo Column
{
  series: [{
    name: 'Receitas',
    data: [30, 40, 35, 50, 49, 60]
  }, {
    name: 'Despesas',
    data: [20, 25, 30, 40, 35, 45]
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
  }
}
```

#### DashboardView - Gráfico de Categorias (Pie)

```typescript
// ApexChart tipo Pie
{
  series: [30, 25, 20, 15, 10],
  labels: ['Salário', 'Freelance', 'Aluguel', 'Alimentação', 'Outros']
}
```

#### ReceitasView/DespesasView - Gráfico de Tendências

```typescript
// ApexChart tipo Line (suave)
{
  series: [{
    name: 'Saldo',
    data: [10, 41, 35, 51, 49, 62, 69, 91, 100]
  }],
  xaxis: {
    categories: ['1/1', '1/2', '1/3', ...]
  }
}
```

---

### 3️⃣ Page Transitions & Animations (PRIORITY: 🟡 MEDIUM)

#### Router Transitions

```vue
<RouterView v-slot="{ Component }">
  <Transition
    name="fade-slide"
    mode="out-in"
    appear
  >
    <component :is="Component" />
  </Transition>
</RouterView>
```

#### SCSS Animations

```scss
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
```

#### Card Hover Animations

```vue
<v-card
  class="card-hover"
  @mouseenter="cardHovered = true"
  @mouseleave="cardHovered = false"
>
  <!-- Content -->
</v-card>
```

```scss
.card-hover {
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  }
}
```

---

### 4️⃣ Loading Animations (PRIORITY: 🟢 LOW)

#### Skeleton Loading

```vue
<v-skeleton-loader v-if="loading" type="card, card, card" :loading="loading" />
```

#### Progress Indicators

```vue
<v-progress-linear v-if="formLoading" indeterminate color="primary" />
```

---

### 5️⃣ Enhanced Form Animations (PRIORITY: 🟢 LOW)

#### Input Focus Effects

```scss
.v-text-field {
  &.focused {
    .v-input__details {
      animation: slideUp 0.2s ease;
    }
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
```

---

## 📊 Comparação: ANTES vs DEPOIS

| Feature              | ANTES        | DEPOIS                 |
| -------------------- | ------------ | ---------------------- |
| **Feedback de Ação** | Dialog       | Toast ✅               |
| **Dashboard**        | KPI cards    | KPI + Charts ✅        |
| **Page Change**      | Instant      | Transition ✅          |
| **Hover Effects**    | Static       | Animated ✅            |
| **Error Messages**   | Simple       | Colored Toast ✅       |
| **Loading**          | Button state | Progress + Skeleton ✅ |

---

## 📈 Priorização

### 🔴 PHASE 1 (Semana 1)

1. Toast Message System (Store + Composable + Component)
2. Dashboard Charts (Linha + Pie + Column)
3. Page Transitions (fade-slide)

**Estimativa**: 4-6 horas  
**Views afetadas**: 12

### 🟡 PHASE 2 (Semana 2)

4. Card Hover Animations
5. Form Input Effects
6. Loading Skeletons

**Estimativa**: 3-4 horas  
**Views afetadas**: 12

### 🟢 PHASE 3 (Semana 3+)

7. Advanced Analytics
8. Custom Transitions
9. Sound Notifications

---

## 📋 Checklist de Implementação

### Toast System

- [ ] Criar `src/store/toast.ts`
- [ ] Criar `src/composables/useToast.ts`
- [ ] Criar `src/components/ToastNotification.vue`
- [ ] Registrar componente global em `main.ts`
- [ ] Substituir snackbars existentes
- [ ] Testar em todos os views (create, update, delete)

### Charts

- [ ] Criar `src/components/ChartLine.vue`
- [ ] Criar `src/components/ChartPie.vue`
- [ ] Criar `src/components/ChartColumn.vue`
- [ ] Adicionar a DashboardView
- [ ] Adicionar a ReceitasView
- [ ] Adicionar a DespesasView
- [ ] Adicionar a CartaoCreditoView

### Animations

- [ ] Criar `src/styles/animations.scss`
- [ ] Aplicar ao App.vue (RouterView)
- [ ] Aplicar a todos KPI cards
- [ ] Aplicar a data tables (row animations)
- [ ] Testar performance

---

## 🎨 Exemplos de Código

### Toast Usage

```typescript
import { useToast } from "@/composables/useToast";

export default {
  setup() {
    const toast = useToast();

    const handleSave = async () => {
      try {
        await saveData();
        toast.success("Dados salvos com sucesso!");
      } catch (error) {
        toast.error("Erro ao salvar dados");
      }
    };

    return { handleSave };
  },
};
```

### Chart Usage

```vue
<template>
  <ChartLine
    :series="chartData.series"
    :categories="chartData.categories"
    title="Evolução do Saldo"
  />
</template>

<script setup>
const chartData = {
  series: [{ name: "Saldo", data: [10, 20, 30] }],
  categories: ["1/1", "1/2", "1/3"],
};
</script>
```

### Animation Usage

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

## 🧪 Testing

### Manual Testing Checklist

- [ ] Toast aparece e desaparece automaticamente
- [ ] Cores corretas por tipo (success/error/warning/info)
- [ ] Múltiplos toasts não se sobrepõem
- [ ] Charts renderizam corretamente em todos tamanhos
- [ ] Transitions suaves entre páginas
- [ ] Performance: FPS stable > 50fps
- [ ] Mobile: Responsive animations
- [ ] Dark mode: Animações visíveis em ambos temas

---

## 📊 Performance Metrics

- **Toast**: <50ms creation, <100ms animation
- **Charts**: <500ms render, <1s data update
- **Transitions**: <300ms page change
- **Memory**: <5MB additional

---

## 🔗 Referências

- ApexCharts: https://apexcharts.com/docs/vue/
- Chart.js: https://www.chartjs.org/
- Vue Transitions: https://vuejs.org/guide/built-ins/transition.html
- Vuetify Snackbar: https://vuetifyjs.com/en/components/snackbars/

---

**Status**: 📋 Planejamento Concluído  
**Próximo Passo**: Implementar PHASE 1
