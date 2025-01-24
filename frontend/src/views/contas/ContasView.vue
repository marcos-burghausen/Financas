<template>
  <div class="content-wrapper">
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
        <mdicon name="arrow-left" size="25" />
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
      <mdicon
        name="chevron-left"
        class="mdicon text-white"
        size="30"
        @click="$emit('mesAnterior')"
      />
      <span class="mes text-white fs-3"> {{ mesPorExtenso }} </span>
      <mdicon
        name="chevron-right"
        class="mdicon text-white"
        size="30"
        @click="$emit('proximoMes')"
      />
    </div>

    <div class="mobile">
      <div class="body__carteira">
        <div class="saldo">
          <span class="saldo__atual">saldo atual</span>
          <span class="valor">R$ 130,00</span>
        </div>
        <div class="saldo">
          <span class="saldo__atual">saldo previsto</span>
          <span class="valor">R$ 130,00</span>
        </div>
      </div>
      <div v-for="(wallet, index) in wallets" :key="index" class="carteira">
        <div class="header__carteira">
          <div class="container__detalhes">
            <span class="icon">
              <mdicon :name="wallet.icon" size="50" />
            </span>
            {{ wallet.name }}
          </div>
          <button class="btn__opcoes">
            <mdicon name="dots-vertical" size="25" />
          </button>
        </div>
        <div class="body__carteira">
          <div class="saldo">
            <span class="saldo__atual">saldo atual</span>
            <span v-if="wallet.saldo == null" class="valor"> R$ 0,00 </span>
            <span v-else class="valor">
              R$ {{ formatValue(wallet.saldo) }}
            </span>
          </div>
          <div class="saldo">
            <span class="saldo__atual">saldo previsto</span>
            <span class="valor">R$ 130,00</span>
          </div>
        </div>
      </div>
      <button
        class="btn__nova__conta"
        @click="formStoreRevenue = !formStoreRevenue"
      >
        <mdicon name="plus" class="mdicon" size="30" />
      </button>
    </div>

    <!-- <div class="pc">
      <div class="container__cards">
        <div class="card__new__conta">
          <div class="btn__new__conta">
            <div class="plus">
              <mdicon name="plus" size="35" />
              <ModalNovaConta />
            </div>
            <span class="add__conta">Criar conta</span>
          </div>
        </div>
        <div class="carteira">
          <div class="header__carteira">
            <div class="container__detalhes">
              <span class="icon">
                <mdicon name="cash" size="35" />
              </span>
              carteira
            </div>
            <button class="btn__opcoes">
              <mdicon name="dots-vertical" size="35" />
            </button>
          </div>
          <div class="body__carteira">
            <div class="saldo">
              <span class="saldo__atual">saldo atual</span>
              <span class="valor">R$ 130,00</span>
            </div>
            <div class="saldo">
              <span class="saldo__atual">saldo saldo previsto</span>
              <span class="valor">R$ 130,00</span>
            </div>
          </div>
          <div class="footer__carteira">
            <button class="btn__add__despesa">ADICIONAR DESPESA</button>
          </div>
        </div>
      </div>
      <div class="container__card__atual_previsto">
        <div class="card__valor">
          <span class="saldo__card">Saldo atual</span>
          <span class="valor__card">R$ 130,00</span>
        </div>
        <div class="card__valor">
          <span class="saldo__card">Saldo previsto</span>
          <span class="valor__card">R$ 130,00</span>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script setup lang="ts">
import ModalNovaConta from "@/components/ModalNovaConta.vue";
import { formatValue } from "@/utils/formatValue";
import { computed } from "vue";

import { useWalletsStore } from "@/store/wallets";
import { ref } from "vue";

const useWallets = useWalletsStore();
let wallets = ref(useWallets.walletsData.wallets);
let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);

const updateContas = (novoValor) => {
  wallets.value = novoValor;
};

const mesPorExtenso = computed(() => {
  if (!mesAnoReferencia.value) return "";

  const [ano, mes] = mesAnoReferencia.value.split("-");

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

  return mesesPorExtenso[parseInt(mes, 10) - 1];
});
</script>

<style scoped>
.content-wrapper {
  position: relative;
  height: 100%;
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
  height: 25%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.container__detalhes {
  width: 320px;
  height: 52px;
  background: transparent;
  color: #fefefe;
  font-size: 30px;
  padding-left: 15px;
  cursor: pointer;
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
  justify-content: space-between;
}
.saldo__atual {
  font-size: 20px;
  color: #fefefe;
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
  .container__detalhes {
    font-size: 25px;
  }
  .body__carteira {
    margin: 20px 0 30px 0;
  }
}
</style>
