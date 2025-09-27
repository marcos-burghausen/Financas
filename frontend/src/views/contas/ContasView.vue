<template>
  <div class="content-wrapper">
    <FormCartaoCredito
      v-if="formulario"
      rota="expense"
      :mes-ano="mesAno"
      wallet-type="Conta"
      @update-data="updateData"
      @close-form="closeForm"
    />
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
      <v-icon icon="mdi-arrow-left" size="25" />
      </router-link>
      <div class="header__items">
        <div class="d-flex flex-column">
          <span class="fs-5"> Contas </span>
          <span class="valor">
            <!-- R$ {{ formatValue(valueTotalRevenuesMonth) }} -->
            R$ {{ "0,00" }}
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

    <div class="mobile">
      <div
        v-for="(wallet, index) in wallets"
        :key="index"
        class="carteira"
      >
        <div class="container__icon pe-2">
            <v-icon
              icon="mdi-circle-outline"
              size="60"
              color="#77d08e"
            />
        </div>
        <div class="descricao__carteira">
          <span class="tipo__conta"> {{ wallet.tipoConta }} </span>
          <span class="nome__conta"> {{ wallet.name }} </span>
          <span class="tipo__conta"> Saldo Previsto </span>
        </div>
        <v-col class="saldo__carteira  p-0">
          <div class="container__opcoes">
            <v-icon
              icon="mdi-menu"
              size="25"
              color="#fefefe"
            />
            <v-icon
              icon="mdi-dots-vertical"
              size="25"
              color="#fefefe"
            />
          </div>
          <div class="saldo">
            <span
              v-if="wallet.saldo == null"
              class="saldo__atual"
            > R$ 5.000,00 </span>
            <span
              v-else
              class="saldo__atual"
            >
              R$ {{ formatValue(wallet.saldo) }}
            </span>
            <span
              v-if="wallet.saldo == null"
              class="saldo__previsto"
            > R$ 0,00 </span>
            <span
              v-else
              class="saldo__previsto"
            >
              R$ {{ formatValue(wallet.saldo) }}
            </span>
          </div>
        </v-col>
      </div>
      <button
        class="btn__nova__conta"
      >
        <v-icon
          icon="mdi-plus"
          class="mdicon"
          size="30"
          @click="openCreateForm"
        />
      </button>
    </div>

  </div>
</template>

<script setup lang="ts">
// import ModalNovaConta from "@/components/ModalNovaConta.vue";
import { formatValue } from "@/utils/formatValue";
import { computed } from "vue";
import FormCartaoCredito from "@/components/FormContaCartao-copy.vue";

import { useUserStore, useWalletsStore } from "@/store";
import { ref } from "vue";

const useWallets = useWalletsStore();
const useUser = useUserStore();
let wallets = ref(useWallets.walletsData.contas);
const mesAno = ref<string>(useUser.mesAno || "");
const formulario = ref(false);

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
  console.log("formulario", formulario.value);
  // selectedRelease.value = undefined;
  formulario.value = true;
  console.log("formulario", formulario.value);
};
const closeForm = () => {
  formulario.value = false;
  // selectedRelease.value = undefined;
};
</script>

<style scoped>
.content-wrapper {
  position: relative;
  height: 100%;
  width: 100%;
  padding: 0 10px;
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
  width: 66%;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding-inline: 15px;
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
  width: 100%;
  display: flex;
  border-bottom: solid 1px #3e4247;
  padding-bottom: 15px;
}
.descricao__carteira {
  width: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
}
.tipo__conta {
  font-size: 12px;
  font-weight: 500;
  color: #888181ff;
  align-self: flex-start;
}
.nome__conta {
  font-size: 20px;
  color: #fefefe;
  align-self: flex-start;
}
.container__icon {
  display: flex;
  justify-content: center;
  align-items: center;
}
.container__opcoes {
  display: flex;
  justify-content: end;
  align-items: center;
  width: 100%;
}
.saldo__atual {
  font-size: 15px;
  color: #fefefe;
  align-self: flex-end;
}
.saldo__previsto {
  font-size: 12px;
  color: #888181ff;
  align-self: flex-end;
}
.icon {
  color: #77d08e;
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
  height: 50%;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  padding-inline: 15px;
}
.saldo {
  display: flex;
  flex-direction: column;
  /* justify-content: space-between; */
}
.valor {
  font-size: 20px;
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
  background-color: #0c99ed;
  border: none;
  border-radius: 50%;
  padding: 10px;
  color: #fefefe;
}

/* @media screen and (max-width: 600px) {
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
  .container__detalhes {
    font-size: 25px;
  }
  .body__carteira {
    margin: 20px 0 30px 0;
  }
} */
</style>
