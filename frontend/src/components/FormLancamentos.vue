<template>
  <div class="container__modal">
    <v-form 
      v-model="isFormValid" 
      class="px-3 w-100" 
      @submit.prevent="submitForm"
    >
      <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
        <div class="d-flex align-items-center">
          <v-btn 
            :disabled="loading" 
            class="close fs-5 ms-2" 
            icon="mdi-close" 
            @click="closeForm" 
          />
          <div class="d-flex flex-column ms-2">
            <span class="fs-5">
              {{ isEditMode ? "Editar" : "Nova" }}
              {{ transactionType }}
            </span>
            <span v-if="isCard" style="font-size: 12px;">
              Lançamento no Cartão de Crédito
            </span>
          </div>
        </div>
        <v-btn 
          :disabled="loading || !isFormValid" 
          :loading="loading" 
          class="btn m-0 me-3 p-0 px-2" 
          type="submit"
          rounded="xl"
        >
          Salvar
        </v-btn>
      </div>

      <div class="form__body">
        <v-text-field
          v-model="form.descricao" 
          label="Descrição" 
          variant="underlined" 
          :rules="[rules.required]"
          prepend-inner-icon="mdi-text-long" 
          class="mb-4" 
        />
  
        <v-text-field
          v-model="form.valor"
          variant="underlined"
          hide-details="auto"
          label="Valor" 
          type="tel"
          class="mb-3"
          :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
          prepend-inner-icon="mdi-currency-usd"
          @input="formatValueSave"
        />

        <template v-if="isCard">
          <v-select
            v-model="form.cartao_id"
            label="Cartão de Crédito"
            :items="creditCardAccounts"
            item-title="name"
            item-value="id"
            variant="underlined"
            :rules="[rules.required]"
          >
            <template #selection="{ item }">
              <div class="d-flex align-center">
                <v-icon :icon="getBankIcon(item.raw.name)" size="25" class="mr-2" />
                <span>{{ item.title }}</span>
              </div>
            </template>
            <template #append-inner>
              <v-icon v-if="selectedCreditCard" :icon="getBankIcon(selectedCreditCard.bandeira)" size="25" class="mr-2" />
            </template>
            <template #item="{ props, item }">
              <v-list-item
                v-bind="props" 
                :prepend-icon="getBankIcon(item.raw.name)" 
                :append-icon="getBankIcon(item.raw.bandeira)" 
                :title="item.raw.name"
              />
            </template>
          </v-select>

          <div ref="faturaSelectRef" class="fatura-custom-select" @click="toggleDropdown">
            <div class="fatura-prefix">
              <v-icon icon="mdi-calendar" size="20" class="me-2" />
              <span>Fatura</span>
            </div>
            <div class="fatura-selection">
              <span>{{ form.fatura || 'Selecione' }}</span>
              <v-icon icon="mdi-chevron-down" size="20" class="ms-2" />
            </div>
            <div v-if="isDropdownOpen" ref="dropdownContainerRef" class="fatura-dropdown">
              <div
                v-for="item in invoiceList"
                :key="item"
                class="dropdown-item"
                :class="{ 'is-selected': item === form.fatura }"
                :data-invoice="item"
                @click.stop="selectInvoice(item)"
              >
                {{ item }}
              </div>
            </div>
          </div>
        </template>

        <v-text-field
          v-if="isCard"
          :model-value="linkedAccountName"
          label="Conta"
          variant="underlined"
          readonly
          prepend-inner-icon="mdi-bank"
          class="mt-4"
        />
        <v-select
          v-else
          v-model="form.conta_id"
          label="Conta"
          :items="availableBankAccounts"
          item-title="name"
          item-value="id"
          variant="underlined"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-bank"
          class="mt-4"
        />

        <v-row>
          <v-col cols="6">
            <v-select
              v-model="form.categoria"
              label="Categoria"
              :items="availableCategories"
              item-title="name"
              item-value="name"
              variant="underlined"
              :rules="[rules.required]"
              @update:model-value="form.subcategoria = ''"
            />
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="form.subcategoria"
              label="Subcategoria"
              :items="availableSubcategories"
              item-title="name"
              item-value="name"
              variant="underlined"
            />
          </v-col>
        </v-row>
        
        <v-select
          v-if="!isCard"
          v-model="form.status_lancamento"
          label="Status"
          :items="['Pendente', 'Efetivada']"
          variant="underlined"
        />
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
// --- 1. IMPORTS ---
import { useWalletsStore } from "@/store";
import { useLancamentoStore } from "@/store/lancamentos";
import type { CategoryData, Lancamento, Wallet } from "@/types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon } from "@/utils/iconMapper";
import { onClickOutside } from '@vueuse/core';
import { computed, nextTick, onMounted, ref } from "vue";

// --- 2. PROPS & EMITS ---
const props = defineProps<{
  transactionType: "Receita" | "Despesa";
  isCard?: boolean;
  lancamento?: Lancamento;
}>();
const emit = defineEmits(["close"]);

// --- 3. STORES ---
const walletsStore = useWalletsStore();
const lancamentoStore = useLancamentoStore();

// --- 4. STATE ---
const form = ref<Partial<Lancamento & { cartao_id: number | null, fatura: string | null }>>({
  tipo_lancamento: props.transactionType,
  descricao: "",
  valor: "0,00",
  conta_id: null,
  cartao_id: null,
  fatura: "",
  data_lancamento: new Date().toISOString().split("T")[0],
  recorrencia: "Não recorrente",
  categoria: "",
  subcategoria: "",
  status_lancamento: props.isCard ? "Efetivada" : "Pendente",
});
const loading = ref(false);
const isFormValid = ref(false);
const isEditMode = computed(() => !!props.lancamento?.id);
const isDropdownOpen = ref(false);
const faturaSelectRef = ref(null);
const dropdownContainerRef = ref<HTMLElement | null>(null);

// --- 5. LÓGICA DE INTERAÇÃO ---
onClickOutside(faturaSelectRef, () => {
  isDropdownOpen.value = false;
});

// --- 6. COMPUTED PROPERTIES ---
const creditCardAccounts = computed<Wallet[]>(() => walletsStore.walletsData.cartoes || []);
const availableBankAccounts = computed<Wallet[]>(() => walletsStore.walletsData.contas || []);
const selectedCreditCard = computed(() => {
  if (!form.value.cartao_id) return null;
  return creditCardAccounts.value.find(c => c.id === form.value.cartao_id);
});
const linkedAccountName = computed(() => {
  if (!selectedCreditCard.value || !selectedCreditCard.value.conta_pai_id) return "Nenhuma conta vinculada";
  const conta = availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
  return conta?.name || "Conta não encontrada";
});
const availableCategories = computed<CategoryData[]>(() => {
  if (!walletsStore.walletsData.categories) return [];
  const typeMap: { [key: string]: string } = { "Receita": "receita", "Despesa": "despesa" };
  const currentType = typeMap[props.transactionType];
  return walletsStore.walletsData.categories.filter(cat => cat && (cat.type === currentType || cat.type === 'ambas'));
});
const availableSubcategories = computed(() => {
  const selectedCategory = availableCategories.value.find(c => c.name === form.value.categoria);
  return selectedCategory?.subcategories ? selectedCategory.subcategories.map(s => s.name) : [];
});

const invoiceList = computed(() => {
  const list = [];
  const now = new Date();
  const formatDate = (date: Date) => {
    const month = date.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace('.', '');
    const year = date.getFullYear();
    return `${month}/${year}`;
  };
  for (let i = 12; i > 0; i--) {
    list.push(formatDate(new Date(now.getFullYear(), now.getMonth() - i, 1)));
  }
  for (let i = 0; i <= 12; i++) {
    list.push(formatDate(new Date(now.getFullYear(), now.getMonth() + i, 1)));
  }
  return list;
});

// --- 7. MÉTODOS ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) => {
    if (!value) return "O campo valor é obrigatório";
    const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
    return (!isNaN(numericValue) && numericValue > 0) || "O valor deve ser maior que zero";
  },
};

const formatValueSave = () => {
  let digits = String(form.value.valor).replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  form.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

const toggleDropdown = async () => {
  isDropdownOpen.value = !isDropdownOpen.value;

  if (isDropdownOpen.value) {
    await nextTick();
    const container = dropdownContainerRef.value;
    if (container && form.value.fatura) {
      const selectedItem = container.querySelector(`[data-invoice="${form.value.fatura}"]`) as HTMLElement;
      if (selectedItem) {
        /**
         * AQUI ESTÁ A MUDANÇA!
         * Trocamos 'center' por 'start'.
         * 'start' alinha o topo do item selecionado com o topo da área visível da lista.
         */
        selectedItem.scrollIntoView({
          block: 'start',
          behavior: 'auto' // 'auto' é mais rápido que 'smooth' para esse caso
        });
      }
    }
  }
};

const selectInvoice = (invoice: string) => {
  form.value.fatura = invoice;
  isDropdownOpen.value = false;
};

const closeForm = () => emit("close");
const submitForm = async () => { /* ... seu código ... */ };

// --- 8. LIFECYCLE HOOKS ---
onMounted(() => {
  if (props.isCard && creditCardAccounts.value.length > 0) {
    form.value.cartao_id = creditCardAccounts.value[0].id;
  }
  const now = new Date();
  const month = now.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace('.', '');
  const year = now.getFullYear();
  form.value.fatura = `${month}/${year}`;
  
  if (props.lancamento) {
    form.value = { ...props.lancamento, valor: formatValue(Number(props.lancamento.valor)) };
  }
});
</script>

<style scoped>
/* ... (Estilos - sem alterações) ... */
.container__modal { background-color: #1e1e1e; color: white; height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; z-index: 1005; }
.form__body { padding: 80px 16px 16px 16px; overflow-y: auto; height: 100vh; }
.header__items { background-color: #1e1e1e; width: 100%; z-index: 10; padding: 10px 0; }
.close { background: transparent; color: white; box-shadow: none; }
.btn { background-color: #0c99ed; color: #1e1e1e; text-transform: none; font-weight: bold; }
.brand-icon { height: 24px; width: auto; }
.fatura-custom-select {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 4px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  cursor: pointer;
  width: 100%;
  background-color: transparent;
  margin-top: 16px;
}
.fatura-prefix { display: flex; align-items: center; color: rgba(255, 255, 255, 0.7); }
.fatura-selection { display: flex; align-items: center; color: white; font-weight: 500; }
.fatura-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #2c2c2c;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  max-height: 250px;
  overflow-y: auto;
  z-index: 10;
  width: 250px;
  margin: 4px auto 0;
}
.dropdown-item {
  padding: 10px 16px;
  color: white;
  font-size: 0.9rem;
  transition: background-color 0.2s ease;
  border-radius: 4px;
  margin: 2px 4px;
}
.dropdown-item:hover {
  background-color: #3f3f3f;
}
.dropdown-item.is-selected {
  background-color: #0c99ed;
  font-weight: bold;
}
</style>