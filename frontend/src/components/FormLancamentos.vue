<template>
  <div class="container__modal">
    <v-form v-model="isFormValid" class="px-3 w-100" @submit.prevent="submitForm">
      <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
        <div class="d-flex align-items-center">
          <v-btn :disabled="loading" class="close fs-5 ms-2" icon="mdi-close" @click="closeForm" />
          <span class="fs-5 ms-2"> {{ formTitle }} </span>
        </div>
        <v-btn :disabled="loading || !isFormValid" :loading="loading" class="btn m-0 me-3 p-0 px-2" type="submit" rounded="xl">
          Salvar
        </v-btn>
      </div>

      <div class="form-body">
        <v-select v-model="form.tipo_lancamento" label="Tipo de Lançamento" :items="tiposLancamento" variant="underlined" prepend-inner-icon="mdi-swap-horizontal" />

        <v-text-field v-model="form.descricao" label="Descrição" variant="underlined" :rules="[rules.required]" prepend-inner-icon="mdi-text-long" />

        <v-text-field v-model="form.valor" label="Valor" variant="underlined" type="number" step="0.01" :rules="[rules.required]" prepend-inner-icon="mdi-cash" />

        <v-select v-model="form.conta_id" label="Conta / Cartão" :items="availableAccounts" item-title="name" item-value="id" variant="underlined" :rules="[rules.required]" prepend-inner-icon="mdi-wallet" />

        <v-text-field v-model="form.data_lancamento" label="Data" type="date" variant="underlined" :rules="[rules.required]" />
        
        <v-row>
            <v-col cols="6">
                <v-select v-model="form.categoria" label="Categoria" :items="availableCategories" item-title="name" item-value="name" variant="underlined" :rules="[rules.required]" @update:modelValue="form.subcategoria = ''" />
            </v-col>
            <v-col cols="6">
                 <v-select v-model="form.subcategoria" label="Subcategoria" :items="availableSubcategories" item-title="name" item-value="name" variant="underlined" :rules="[rules.required]" />
            </v-col>
        </v-row>

        <v-select v-if="form.tipo_lancamento !== 'Cartão de Crédito'" v-model="form.status_lancamento" label="Status" :items="['Pendente', 'Efetivada']" variant="underlined" />
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { useDataStore } from '@/store/data';
import { useLancamentoStore } from '@/store/lancamentos';
import type { Conta } from '@/types/accounts.types';
import type { Categoria, Lancamento } from '@/types/transactions.types';
import { computed, ref } from 'vue';

const props = defineProps<{
  transactionType?: 'Receita' | 'Despesa' | 'Cartão de Crédito';
  lancamento?: Lancamento;
}>();

const emit = defineEmits(['close']);

const dataStore = useDataStore();
const lancamentoStore = useLancamentoStore();

const form = ref<Partial<Lancamento>>({
  tipo_lancamento: props.transactionType || 'Despesa',
  descricao: '',
  valor: undefined,
  conta_id: null,
  data_lancamento: new Date().toISOString().split('T')[0],
  categoria: '',
  subcategoria: '',
  status_lancamento: 'Pendente',
});

const loading = ref(false);
const isFormValid = ref(false);

const formTitle = computed(() => `${props.lancamento ? 'Editar' : 'Novo(a)'} ${form.value.tipo_lancamento}`);
const tiposLancamento = ['Despesa', 'Receita', 'Cartão de Crédito'];

const availableAccounts = computed<Conta[]>(() => {
    return form.value.tipo_lancamento === 'Cartão de Crédito' 
      ? dataStore.wallets.cartoes 
      : dataStore.wallets.contas;
});

const availableCategories = computed<Categoria[]>(() => {
    const typeMap = { 'Receita': 'receita', 'Despesa': 'despesa', 'Cartão de Crédito': 'despesa' };
    const currentType = typeMap[form.value.tipo_lancamento!];
    return dataStore.categories.filter(c => c.type === currentType || c.type === 'ambas');
});

const availableSubcategories = computed(() => {
    const selectedCategory = availableCategories.value.find(c => c.name === form.value.categoria);
    return selectedCategory ? selectedCategory.subcategories : [];
});

const rules = {
  required: (value: any) => !!value || 'Campo obrigatório.',
};

const closeForm = () => emit('close');

const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    await lancamentoStore.saveLancamento(form.value);
    closeForm();
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false;
  }
};

if (props.lancamento) {
    form.value = { ...props.lancamento, valor: props.lancamento.valor / 100 };
}
</script>

<style scoped>
.container__modal { background-color: #1e1e1e; color: white; height: 100vh; width: 100vw; }
.form-body { padding: 80px 16px 16px 16px; }
.header__items { background-color: #1e1e1e; width: 100%; z-index: 10; padding: 10px 0; }
.close { background: transparent; color: white; box-shadow: none; }
.btn { background-color: #0c99ed; color: #1e1e1e; text-transform: none; font-weight: bold; }
</style>