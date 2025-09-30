<template>
  <div class="container__modal">
    <v-form v-model="isFormValid" class="w-100" @submit.prevent="submitForm">
      <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
        <div class="d-flex align-items-center">
          <v-btn :disabled="loading" class="close fs-5 ms-2" icon="mdi-close" @click="closeForm" />
          <span class="fs-5 ms-2"> {{ isCreditCard ? 'Novo Cartão' : 'Nova Conta' }} </span>
        </div>
        <v-btn :disabled="loading || !isFormValid" :loading="loading" class="btn m-0 me-3 p-0 px-2" type="submit" rounded="xl">
          Salvar
        </v-btn>
      </div>

      <div class="form-body">
        <v-select v-model="form.tipo_conta" label="Tipo de Conta" variant="underlined" :items="['Conta Corrente', 'Poupança', 'Carteira', 'Investimento', 'Cartão de Crédito', 'Outro']" prepend-inner-icon="mdi-bank" />

        <v-text-field v-model="form.name" label="Nome da Conta / Cartão" variant="underlined" class="imput" :rules="[rules.required]" prepend-inner-icon="mdi-text" />

        <v-text-field v-if="!isCreditCard" v-model="form.saldo_inicial" label="Valor Inicial" variant="underlined" type="number" step="0.01" class="imput" :rules="[rules.required]" prepend-inner-icon="mdi-cash" />

        <template v-if="isCreditCard">
          <v-text-field v-model="form.limite" label="Limite do Cartão" variant="underlined" type="number" step="0.01" class="imput" :rules="[rules.required]" prepend-inner-icon="mdi-credit-card-chip" />
          
          <v-row>
            <v-col cols="6">
              <v-text-field v-model="form.dia_fechamento" label="Dia do Fechamento" variant="underlined" type="number" min="1" max="31" :rules="[rules.required]" />
            </v-col>
            <v-col cols="6">
              <v-text-field v-model="form.dia_vencimento" label="Dia do Vencimento" variant="underlined" type="number" min="1" max="31" :rules="[rules.required]" />
            </v-col>
          </v-row>
          
          <v-select v-model="form.conta_pai_id" label="Conta para Débito (Opcional)" variant="underlined" :items="contasParaPagamento" item-title="name" item-value="id" clearable prepend-inner-icon="mdi-link-variant" />
        </template>
        
        <v-checkbox v-model="form.incluir_em_soma_inicial" :label="isCreditCard ? 'Incluir limite na soma total' : 'Incluir saldo no total geral'" />
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import { useDataStore } from '@/store/data';
import { useWalletsStore } from '@/store/wallets';
import type { Conta } from '@/types/accounts.types';
import { computed, ref, watch } from 'vue';

const walletsStore = useWalletsStore();
const dataStore = useDataStore();
const emit = defineEmits(['close']);

const form = ref<Partial<Conta>>({
  name: '',
  tipo_conta: 'Conta Corrente',
  saldo_inicial: 0,
  incluir_em_soma_inicial: true,
  limite: 0,
  dia_fechamento: 1,
  dia_vencimento: 10,
  conta_pai_id: null,
});

const loading = ref(false);
const isFormValid = ref(false);

const isCreditCard = computed(() => form.value.tipo_conta === 'Cartão de Crédito');
const contasParaPagamento = computed(() => dataStore.wallets.contas);

const rules = {
  required: (value: any) => !!value || 'Campo obrigatório.',
};

watch(isCreditCard, (isCard) => {
  if (isCard) {
    form.value.incluir_em_soma_inicial = false;
    form.value.saldo_inicial = 0;
  } else {
    form.value.incluir_em_soma_inicial = true;
  }
}, { immediate: true });

const closeForm = () => emit('close');

const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    await walletsStore.saveWallet(form.value);
    closeForm();
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.container__modal { background-color: #1e1e1e; color: white; height: 100vh; width: 100vw; }
.header__items { background-color: #1e1e1e; width: 100%; z-index: 10; padding: 10px 0; }
.form-body { padding: 80px 16px 16px 16px; }
.close { background: transparent; color: white; box-shadow: none; }
.btn { background-color: #0c99ed; color: #1e1e1e; text-transform: none; font-weight: bold; }
</style>