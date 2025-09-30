<template>
  <div class="content-wrapper">
    <div class="header">
      <router-link class="link me-7 d-flex align-items-center opaco" :to="{ name: 'dashboard' }">
        <v-icon icon="mdi-arrow-left" size="25" />
      </router-link>
      <div class="header__items">
        <div class="d-flex flex-column">
          <span class="title__page fs-5">Cartão de Crédito</span>
        </div>
      </div>
    </div>

    <div class="px-3 py-2">
      <v-select v-model="selectedCardId" label="Selecione o Cartão" :items="creditCardAccounts" item-title="name" item-value="id" variant="solo-filled" @update:modelValue="loadInvoices" hide-details />
    </div>

    <div v-if="creditCardStore.isLoading" class="text-center pa-4">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <div v-else-if="creditCardStore.invoices.length > 0" class="px-3">
      <v-expansion-panels>
        <v-expansion-panel v-for="invoice in creditCardStore.invoices" :key="invoice.id" class="mb-2 bg-grey-darken-4">
          <v-expansion-panel-title class="d-flex justify-space-between">
            <div>
              <span>Fatura de {{ formatCompetencia(invoice.competencia) }}</span>
              <div class="text-caption opaco">Venc. {{ formatDate(invoice.data_vencimento) }}</div>
            </div>
            <v-spacer />
            <v-chip :color="invoice.status === 'Paga' ? 'success' : 'warning'" size="small" class="me-4">{{ invoice.status }}</v-chip>
          </v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list lines="two" bg-color="transparent">
              <v-list-item v-for="lancamento in invoice.lancamentos" :key="lancamento.id">
                <v-list-item-title>{{ lancamento.descricao }}</v-list-item-title>
                <v-list-item-subtitle>{{ lancamento.categoria }}</v-list-item-subtitle>
                <template v-slot:append>
                  <div class="d-flex align-center">
                    <span :class="lancamento.is_estorno ? 'text-success' : ''">
                      R$ {{ (lancamento.valor / 100).toFixed(2) }}
                    </span>
                    <v-btn v-if="!lancamento.is_estorno" icon="mdi-undo-variant" variant="text" size="small" @click="openRefundModal(lancamento)" />
                  </div>
                </template>
              </v-list-item>
            </v-list>
            <v-divider class="my-2" />
            <v-card-actions>
              <span class="text-h6">Total: R$ {{ (calculateInvoiceTotal(invoice) / 100).toFixed(2) }}</span>
              <v-spacer />
              <v-btn v-if="invoice.status !== 'Paga'" color="primary" variant="tonal" @click="openPayInvoiceModal(invoice)">
                Pagar Fatura
              </v-btn>
            </v-card-actions>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </div>
    
    <div v-else class="text-center pa-5 opaco">
      <div v-if="!selectedCardId">Selecione um cartão para ver as faturas.</div>
      <div v-else>Nenhuma fatura encontrada.</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCreditCardStore } from '@/store/creditCard';
import { useDataStore } from '@/store/data';
import type { CreditCardInvoice, Lancamento } from '@/types/transactions.types';
import { computed, onMounted, ref } from 'vue';

const creditCardStore = useCreditCardStore();
const dataStore = useDataStore();

const selectedCardId = ref<number | null>(null);
const creditCardAccounts = computed(() => dataStore.wallets.cartoes);

onMounted(() => {
  if (creditCardAccounts.value.length > 0) {
    selectedCardId.value = creditCardAccounts.value[0].id;
    loadInvoices();
  }
});

const loadInvoices = () => {
  creditCardStore.fetchInvoices(selectedCardId.value!);
};

const calculateInvoiceTotal = (invoice: CreditCardInvoice) => {
  return invoice.lancamentos.reduce((total, lanc) => {
    return lanc.is_estorno ? total - lanc.valor : total + lanc.valor;
  }, 0);
};

// Funções de formatação
const formatCompetencia = (comp: string) => new Date(`${comp}-02`).toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
const formatDate = (date: string) => new Date(`${date}T00:00:00`).toLocaleDateString('pt-BR');

// Lógica de Modais (simplificada para este exemplo)
const openPayInvoiceModal = async (invoice: CreditCardInvoice) => {
  const contaId = prompt("Digite o ID da conta para pagamento:");
  if (contaId) {
    await creditCardStore.payInvoice({
      invoice_id: invoice.id,
      conta_pagamento_id: parseInt(contaId),
      mesAno: dataStore.mesAno,
    });
  }
};

const openRefundModal = async (lancamento: Lancamento) => {
  const valor = prompt(`Digite o valor do estorno para "${lancamento.descricao}":`);
  if (valor) {
    await creditCardStore.createRefund({
      lancamento_original_id: lancamento.id,
      valor: parseFloat(valor),
    }, selectedCardId.value!);
  }
};
</script>

<style scoped>
.content-wrapper { padding-top: 60px; }
.opaco { opacity: 0.7; }
</style>