<template>
  <div class="content-wrapper">
    <FormCartaoCredito
      v-if="formCartao"
      :mes-ano="mesAno"
      wallet-type="Cartão"
      @update-data="updateData"
      @close-form="closeForm"
    />
    <FormLancamentos
      v-if="formLancamentos"
      :releases="selectedRelease"
      rota="expense"
      :mes-ano="mesAno"
      transaction-type="Despesa"
      @update-data="updateData"
      @close-form="closeForm"
    />
    <div
      v-if="!formCartao"
      class="receitas"
    >
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
            <span class="title__page fs-5"> Cartão de Crédito </span>
            <span class="valor">
              R$
              <!-- {{ formatValue(valueTotalRevenuesMonth) }} -->
            </span>
          </div>
        </div>
      </div>

      <div class="container__mes">
        <v-icon
          icon="mdi-chevron-left"
          class="mdicon text-white"
          size="30"
          @click="$emit('mesAnterior')"
        />
        <span class="mes text-white fs-3"> {{ mesPorExtenso }} </span>
        <v-icon
          icon="mdi-chevron-right"
          class="mdicon text-white"
          size="30"
          @click="$emit('proximoMes')"
        />
      </div>

      <div
        v-if="creditCard && creditCard.length > 0"
        class="container__cards"
      >
        <div
          v-for="(card, index) in creditCard"
          :key="index"
          class="__card mb-2"
        >
          <div class="card__header">
            <div class="card__title">
              <IconeSicredi class="logo__sicredi" />
              <div class="card__details">
                <span> {{ card.name }}</span>
                <div class="type__card d-flex align-items-center">
                  <IconeMastercard class="logo__mastercard" />
                  <small> {{ card.icon }}</small>
                </div>
              </div>
            </div>
            <div class="card-actions">
              <v-icon>mdi-plus</v-icon>
              <v-icon>mdi-magnify</v-icon>
              <v-icon>mdi-dots-vertical</v-icon>
            </div>
          </div>

          <div class="card__body">
            <div class="info-row">
              <div class="info-item">
                <span class="label">Limite</span>
                <span class="value">R$ {{ formatValue(card.limite) }}</span>
              </div>
              <div class="info-item text-center">
                <span class="label">Em aberto</span>
                <span class="value">R$ {{ formatValue(card.saldo) }}</span>
              </div>
              <div class="info-item text-right">
                <span class="label">Lim. disponível</span>
                <span class="value">R$ {{ formatValue(card.limite - card.saldo) }}</span>
              </div>
            </div>

            <div class="progress__bar__container">
              <v-progress-linear
                :model-value="((card.limite - card.saldo) / card.limite * 100).toFixed(0)"
                color="#32c770"
                height="15"
                rounded
              />
              <span class="progress-label">{{ ((card.limite - card.saldo) / card.limite * 100).toFixed(0) }}% </span>
            </div>
            <v-divider />
            <div class="info-row mt-3">
              <div class="info-item">
                <span class="label">Conta</span>
                <span class="value-small">{{ card.conta }}</span>
              </div>
              <div class="info-item text-center">
                <span class="label">Fechamento</span>
                <span class="value-small">{{ card.dia_fechamento }}</span>
              </div>
              <div class="info-item text-right">
                <span class="label">Vencimento</span>
                <span class="value-small">{{ card.dia_vencimento }}</span>
              </div>
            </div>
          </div>

          <div class="card__footer">
            <div class="fatura-info">
              <span class="fatura-label">Fatura</span>
              <span class="fatura-value">R$ {{ formatValue(card.saldo) }}</span>
            </div>
            <div class="fatura-actions">
              <span class="status-fechada">
                <v-icon
                  size="14"
                  class="me-1"
                > mdi-lock </v-icon>
                Fechada
              </span>
              <a
                href="#"
                class="register-payment"
              >
                <v-icon
                  size="18"
                  color="#3d8eff"
                  class="me-1"
                >
                  mdi-check-circle
                </v-icon>
                Registrar pagamento
              </a>
            </div>
          </div>
        </div>
      </div>

      <NoDataComponent v-else />
    </div>

    <v-bottom-sheet
      v-if="!formCartao"
      v-model="sheet"
      style="border: 1px solid green;"
    >
      <template #activator="{ props: activatorProps }">
        <div class="text-center pa-8">
          <v-btn
            v-bind="activatorProps"
            color="primary"
            size="50"
            class="btn__nova__conta"
          >
            <v-icon size="35">
              mdi-plus
            </v-icon>
          </v-btn>
        </div>
      </template>

      <v-list
        class="position-fixed rounded"
        style="bottom: 10px; right: 10px;"
      >
        <v-list-item
          v-for="tile in tiles"
          :key="tile.title"
          :prepend-icon="tile.img"
          :title="tile.title"
          @click="sheet = false, tile.action()"
        />
      </v-list>
    </v-bottom-sheet>

    <!-- <div v-if="!formCartao" class="fixed-bottom d-flex justify-end pe-6 pb-6">
      <v-icon
        type="button"
        title="Adicionar nova despesa"
        icon="mdi-plus"
        class="mdicon__add"
        @click="openCreateForm"
      />
    </div> -->
  </div>
</template>

<script setup lang="ts">
import IconeMastercard from "@/assets/icons/mastercard.svg";
import IconeSicredi from "@/assets/icons/sicredi35.svg";

import FormCartaoCredito from "@/components/FormContaCartao-copy.vue";
import NoDataComponent from "@/components/mobile/NoDataComponent.vue";
// import ModalNovaConta from "@/components/ModalNovaConta.vue";
import { formatValue } from "@/utils/formatValue";
import { computed } from "vue";

import { useUserStore, useWalletsStore } from "@/store";
import { ref, shallowRef } from "vue";

const sheet = shallowRef(false);
  const tiles = [
    { img: "mdi-credit-card-plus-outline", title: "Novo cartão" },
    { img: "mdi-gift-outline", title: "Novo estorno" },
    { img: "mdi-credit-card-outline", title: "Despesa cartão", action: () => { openCreateForm(); } },
  ];

const useWallets = useWalletsStore();
const useUser = useUserStore();
let creditCard = ref(useWallets.walletsData.cartoes);
const mesAno = ref<string>(useUser.mesAno || "");
const formCartao = ref(false);
const formLancamentos = ref(false);

// const updateContas = (novoValor) => {
//     wallets.value = novoValor;
// };

const mesPorExtenso = computed(() => {
  if (!mesAno.value) return "";
  const [ano, mes] = mesAno.value.split("-");
  const anoAtual = new Date().getFullYear();
  const mesesPorExtenso = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];
  if (parseInt(ano, 10) === anoAtual) {
    return mesesPorExtenso[parseInt(mes, 10) - 1];
  }
  const mesAbreviado = mesesPorExtenso[parseInt(mes, 10) - 1].slice(0, 3);
  return `${mesAbreviado}./${ano.slice(2)}`;
});

const openCreateForm = () => {
  // selectedRelease.value = undefined;
  formCartao.value = true;
};

const closeForm = () => {
  formCartao.value = false;
  // selectedRelease.value = undefined;
};

// const updateData = (newData) => {
//   useExpenses.setExpensesData(newData);
//   valueTotalExpensesMonth.value = newData.valueTotalMonth;
//   expensesMonth.value = newData.byMonth || [];
//   closeForm();
// };
</script>

<style scoped>
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

@media screen and (max-width: 600px) {
  .pc {
    display: none;
  }
  .carteira {
    height: 150px;
    width: 95%;
    margin: 20px 10px 0 10px;
    padding: 20px 10px;
  }
  .btn__new__conta {
    height: 80px;
    width: 100%;
    margin-block: 10px;
    padding: 0 10px;
    display: flex;
    justify-content: center;
    align-items: end;
  }
  .body__carteira {
    margin: 20px 0 30px 0;
  }
}
</style>
