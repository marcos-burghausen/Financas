<template>
  <div class="content-wrapper">
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
        <v-icon 
          icon="mdi-arrow-left" 
          size="25" 
        />
      </router-link>
      <div class="header__items">
        <div class="d-flex flex-column">
          <span class="title__page fs-5">Cartão de Crédito</span>
          <span class="valor">
            R$
            <!-- {{ formatValue(valueTotalRevenuesMonth) }} -->
          </span>
        </div>
      </div>
    </div>

    <div class="px-3 py-2">
      <v-select
        v-model="selectedCardId"
        label="Selecione o Cartão"
        :items="creditCardAccounts"
        item-title="name"
        item-value="id"
        variant="solo-filled"
        hide-details
        @update:model-value="loadInvoices"
      />
    </div>

    <div
      v-if="creditCardStore.isLoading"
      class="text-center pa-4"
    >
      <v-progress-circular
        indeterminate
        color="primary"
      />
    </div>
    
    <div
      v-else-if="creditCardStore.invoices.length > 0"
      class="px-3"
    >
      <v-expansion-panels>
        <v-expansion-panel
          v-for="invoice in creditCardStore.invoices"
          :key="invoice.id"
          class="mb-2 bg-grey-darken-4"
        >
          <v-expansion-panel-title class="d-flex justify-space-between">
            <div>
              <span>Fatura de {{ formatCompetencia(invoice.competencia) }}</span>
              <div class="text-caption opaco">
                Venc. {{ formatDate(invoice.data_vencimento) }}
              </div>
            </div>
            <v-spacer />
            <v-chip
              :color="invoice.status === 'Paga' ? 'success' : 'warning'"
              size="small"
              class="me-4"
            >
              {{ invoice.status }}
            </v-chip>
          </v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-list
              lines="two"
              bg-color="transparent"
            >
              <v-list-item
                v-for="lancamento in invoice.lancamentos"
                :key="lancamento.id"
              >
                <v-list-item-title>{{ lancamento.descricao }}</v-list-item-title>
                <v-list-item-subtitle>{{ lancamento.categoria }}</v-list-item-subtitle>
                <template #append>
                  <div class="d-flex align-center">
                    <span :class="lancamento.is_estorno ? 'text-success' : ''">
                      R$ {{ (lancamento.valor / 100).toFixed(2) }}
                    </span>
                    <v-btn
                      v-if="!lancamento.is_estorno"
                      icon="mdi-undo-variant"
                      variant="text"
                      size="small"
                      @click="openRefundModal(lancamento)"
                    />
                  </div>
                </template>
              </v-list-item>
            </v-list>
            <v-divider class="my-2" />
            <v-card-actions>
              <span class="text-h6">Total: R$ {{ (calculateInvoiceTotal(invoice) / 100).toFixed(2) }}</span>
              <v-spacer />
              <v-btn
                v-if="invoice.status !== 'Paga'"
                color="primary"
                variant="tonal"
                @click="openPayInvoiceModal(invoice)"
              >
                Pagar Fatura
              </v-btn>
            </v-card-actions>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </div>
    
    <div
      v-else
      class="text-center pa-5 opaco"
    >
      <div v-if="!selectedCardId">
        Selecione um cartão para ver as faturas.
      </div>
      <div v-else>
        Nenhuma fatura encontrada.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCreditCardStore } from "@/store/creditCard";
import { useWalletsStore } from "@/store/wallets";
import type { CreditCardInvoice, Lancamento } from "@/types/transactions.types";
import { computed, onMounted, ref } from "vue";

const creditCardStore = useCreditCardStore();
const walletsStore = useWalletsStore();

const selectedCardId = ref<number | null>(null);
const creditCardAccounts = computed(() => walletsStore.walletsData.cartoes);

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
const formatCompetencia = (comp: string) => new Date(`${comp}-02`).toLocaleDateString("pt-BR", { month: "long", year: "numeric" });
const formatDate = (date: string) => new Date(`${date}T00:00:00`).toLocaleDateString("pt-BR");

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
.content-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}
.title__page {
  color: #fefefe;
}
.card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.card__title {
  display: flex;
  align-items: center;
}
.brand__icon {
  background-color: #fefefe;
  border-radius: 50%;
  padding: 4px;
  margin-right: 12px;
}
.card__details {
  display: flex;
  flex-direction: column;
}
.card__details span {
  font-weight: bold;
  font-size: 1.1rem;
  color: #fefefe;
  margin-top: 0;
}
.mdicon__add {
  height: 45px;
  width: 45px;
  cursor: pointer;
  padding: 10px;
  border-radius: 50px;
  background-color: #0c99ed;
  color: #fefefe;
}
.card__details small {
  font-size: 0.9rem;
  color: #a0a0a0;
}
.logo__sicredi {
  width: 35px;
  height: 35px;
  margin-right: 5px;
}
.type__card {
  margin-top: -5px;
}
.card-actions {
  display: flex;
  gap: 16px;
  color: #a0a0a0;
}

.card__body {
  margin-top: 15px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.info-item {
  display: flex;
  flex-direction: column;
  flex-basis: 33%;
}

.text-center {
  text-align: center;
}
.text-right {
  text-align: right;
}

.label {
  font-size: 0.8rem;
  color: #a0a0a0;
  margin-bottom: 4px;
}

.value {
  font-size: 0.8rem;
  font-weight: 500;
  color: #fefefe;
}

.value-small {
  font-size: 0.8rem;
  font-weight: 500;
  color: #fefefe;
}

.progress__bar__container {
  margin-top: 5px;
  position: relative;
}

/* .progress-bar {
  background-color: #444;
  border-radius: 5px;
  height: 10px;
  width: 100%;
}

.progress {
  background-color: #32c770;
  border-radius: 5px;
  height: 100%;
  width: 83%;
} */

.progress-label {
  position: absolute;
  right: 8px;
  top: 80%;
  transform: translateY(-50%);
  font-size: 0.7rem;
  font-weight: bold;
  color: #92999eff;
}

.card__footer {
  border-top: 1px solid #444;
  margin-top: 10px;
  padding-top: 10px;
}

.fatura-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.fatura-label {
  font-size: 1rem;
  color: #fefefe;
}

.fatura-value {
  font-size: 1.1rem;
  font-weight: bold;
  color: #fefefe;
}

.fatura-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
}

.status-fechada {
  background-color: #d33a3a;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
}

.register-payment {
  color: #3d8eff;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
}

.header {
  display: flex;
  padding: 10px;
  color: #bdbdbd;
}
.link {
  text-decoration: none;
  color: #fefefe;
}
.opaco {
  color: #6c757d !important;
}
.header__items {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}
.valor {
  font-size: 13px;
}
.__limite {
  width: 33%;
}
.__em_aberto {
  width: 33%;
}
.__limite_disponivel {
  width: 33%;
}
.descricao__limite {
  font-size: 13px;
  padding: 0;
}
.descricao__em_aberto {
  font-size: 13px;
  text-align: center;
}
.descricao__limite_disponivel {
  font-size: 13px;
  padding: 0;
  text-align: end;
}
.valor__limite {
  font-size: 13px;
  padding: 0;
}
.valor__em_aberto {
  font-size: 13px;
  text-align: center;
}
.valor__limite_disponivel {
  font-size: 13px;
  padding: 0;
  text-align: end;
}
.porcentagem__utilizado {
  border: 1px solid red;
  height: 8px;
  margin-top: 5px;
  border-radius: 5px;
}
.porcentagem__barra {
  height: 100%;
  background: #77d08e;
  border-radius: 5px;
}
.container__mes {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin-block: 20px;
}
.mdicon {
  cursor: pointer;
  /* padding: 10px; */
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 20px;
}

.container__cards {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-inline: 10px;
}
.__card {
  width: 100%;
  background-color: #2a2d30;
  border-radius: 12px;
  padding: 10px;
  color: #fff;
  font-family: sans-serif;
}
.card__new__conta {
  height: 248px;
  width: 49%;
  margin-block: 10px;
  border-radius: 15px;
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  display: flex;
  justify-content: center;
  align-items: center;
}
.btn__new__conta {
  background: transparent;
  height: 50%;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  color: #77d08e;
  font-weight: 100;
}
.plus {
  height: 68px;
  width: 68px;
  border: solid 1px #77d08e;
  border-radius: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.add__conta {
  font-size: 20px;
  font-weight: 400;
  color: #77d08e;
}
.carteira {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  height: 248px;
  width: 49%;
  margin-block: 10px;
  border-radius: 15px;
  display: flex;
  flex-direction: column;
  padding-inline: 10px;
}
.header__carteira {
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.container__detalhes {
  width: 320px;
  height: 52px;
  background: transparent;
  color: #fefefe;
  font-size: 20px;
  cursor: pointer;
}
.icon {
  color: #77d08e;
}
span {
  color: #77d08e;
  margin-top: -5px;
}
.card__type {
  all: unset;
  display: inline-block;
  line-height: 1;
  margin-top: -10px;
}
.btn__opcoes {
  height: 52px;
  width: 52px;
  border-radius: 30px;
  color: white;
}
.btn__opcoes:hover {
  background-color: rgba(254, 254, 254, 0.1);
}
.body__carteira {
  /* height: 50%; */
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  padding-inline: 5px;
}
.saldo {
  display: flex;
  justify-content: space-around;
}
.saldo__atual {
  font-size: 12px;
  color: #fefefe;
}
.valor {
  font-size: 12px;
  color: #06bb64;
}
.footer__carteira {
  height: 25%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.btn__add__despesa {
  color: #77d08e;
  padding: 10px;
  border-radius: 25px;
}
.btn__add__despesa:hover {
  background-color: rgba(254, 254, 254, 0.1);
}
.container__card__atual_previsto {
  width: 33%;
  height: 248px;
  margin-block: 10px;
  border-radius: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.card__valor {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  height: 110px;
  width: 100%;
  border-radius: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-inline: 15px;
}
.saldo__card {
  color: #fefefe;
  font-size: 25px;
}
.valor__card {
  color: #06bb64;
  font-size: 25px;
}
.pc {
  display: flex;
}
.btn__nova__conta {
  position: fixed;
  /* Calcula a posição relativa ao centro do #app */
  /* right: calc(
    (100vw - 500px) / 2 + 55px
  ); */
  right: 15px;
  bottom: 15px;
  background-color: #1dbb01;
  border: none;
  border-radius: 50%;
  padding: 10px;
  color: #fefefe;
}
</style>