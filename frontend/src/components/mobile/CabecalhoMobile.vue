<template>
  <v-card color="transparent" style="width: 100%">
    <v-layout>
      <v-app-bar color="transparent">
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer">
          <mdicon name="menu" class="mdicon" size="30" />
        </v-app-bar-nav-icon>

        <!-- <v-toolbar-title> -->
        <div class="container__mes">
          <mdicon
            name="chevron-left"
            class="mdicon"
            size="30"
            @click="$emit('mesAnterior')"
          />
          <span class="mes"> {{ mesReferencia }} </span>
          <mdicon
            name="chevron-right"
            class="mdicon"
            size="30"
            @click="$emit('proximoMes')"
          />
        </div>
        <!-- </v-toolbar-title> -->

        <v-spacer></v-spacer>

        <template v-if="$vuetify.display.mdAndUp">
          <v-btn icon="mdi-magnify" variant="text"></v-btn>

          <v-btn icon="mdi-filter" variant="text"></v-btn>
        </template>

        <v-btn icon="mdi-dots-vertical" variant="text"></v-btn>
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        temporary
        color="#212529"
      >
        <v-list>
          <v-list-item
            v-for="(item, index) in itensSideBar"
            :key="index"
            :value="item.route"
            :class="{ efeitoClick: elementoAtivoSideBar === index }"
          >
            <router-link
              :to="{ name: item.route }"
              style="text-decoration: none"
            >
              <span class="icon">
                <mdicon :name="item.icon"></mdicon>
                <!-- Ícone do item -->
              </span>
              <span class="txt__link">
                {{ item.name }}
              </span>
            </router-link>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-main class="w-100">
        <div class="container__saldo__conta d-flex justify-content-center">
          <div class="me-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <mdicon name="check-circle-outline" size="18" />
              Inicial
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: saldoInicial < 0 ? 'red' : '#757575' }"
                style="font-size: 13px"
              >
                <!-- R$ {{ formatValue(saldoInicial) }} -->
              </span>
            </div>
          </div>
          <div class="d-flex flex-column align-center justify-content-end">
            <span class="fs-5" style="color: #757575">
              <mdicon
                :name="
                  totalBalance < 0
                    ? 'minus-circle-outline'
                    : totalBalance > 0
                    ? 'heart-circle'
                    : 'circle-outline'
                "
                size="22"
              />
              Saldo
            </span>
            <div style="display: flex; align-items: center">
              <!-- <dir style="background: green; width:5px; height: 45px; margin-inline-end: 10px; margin-top: 5px;"></dir> -->
              <span
                :style="{
                  color:
                    totalBalance < 0
                      ? 'red'
                      : totalBalance > 0
                      ? 'green'
                      : '#757575',
                }"
                style="font-size: 18px"
              >
                R$ {{ formatValue(totalBalance) }}
              </span>
            </div>
          </div>
          <div class="ms-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <mdicon name="clock-outline" size="20" />
              <!-- <mdicon name="plus-circle-outline" />
                    <mdicon name="minus-circle-outline" />
                    <mdicon name="clock-outline" /> -->
              Previsto
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: valorPrevisto < 0 ? 'red' : '#757575' }"
                style="font-size: 13px"
              >
                R$ {{ formatValue(valorPrevisto) }}
              </span>
            </div>
          </div>
        </div>

        <div class="container__visao__geral">
          <div class="header__visao_geral">
            <span style="text-align: start"> Visão geral </span>
            <mdicon name="dots-vertical" class="mdicon" size="25" />
          </div>
          <router-link
            :to="{ name: 'receitas' }"
            style="display: flex; align-items: center; text-decoration: none"
          >
            <div
              style="
                border-inline-start: solid 5px green;
                margin: 5px 0 5px 0;
                padding: 0 0 0 10px;
                display: flex;
                justify-content: space-between;
                width: 100%;
              "
            >
              <div class="tipo__lancamento">
                <span class="lancamento"> Receitas </span>
                <span class="previsto"> previsto </span>
              </div>
              <div class="tipo__lancamento">
                <span class="lancamento">
                  R$ {{ formatValue(valueReceived) }}
                </span>
                <span class="valor__previsto">
                  R$ {{ formatValue(valueTotalRevenuesMonth) }}
                </span>
              </div>
            </div>
          </router-link>
          <router-link
            :to="{ name: 'despesas' }"
            style="display: flex; align-items: center; text-decoration: none"
          >
            <div
              style="
                border-inline-start: solid 5px red;
                margin: 5px 0 5px 0;
                padding: 0 0 0 10px;
                display: flex;
                justify-content: space-between;
                width: 100%;
              "
            >
              <div class="tipo__lancamento">
                <span class="lancamento"> Despesas </span>
                <span class="previsto"> previsto </span>
              </div>
              <div class="tipo__lancamento">
                <span class="lancamento"> R$ {{ formatValue(valuePay) }} </span>
                <span class="valor__previsto">
                  R$ {{ formatValue(valueTotalExpensesMonth) }}
                </span>
              </div>
            </div>
          </router-link>
        </div>
      </v-main>
    </v-layout>
  </v-card>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";
import http from "@/services/http";
import { ref, watch } from "vue";
import { useUserStore } from "@/store/user";

const itensSideBar = ref([
  // {
  //   name: "Admin",
  //   icon: "view-dashboard",
  //   route: "dashAdmim",
  //   adminOnly: true,
  // },
  // { name: "Trader", icon: "chart-line", route: "dashAdmim", traderOnly: true },
  // {
  //   name: "Dashboard",
  //   icon: "view-dashboard",
  //   route: "dashboard",
  //   adminOnly: false,
  //   traderOnly: false,
  // },
  {
    name: "Contas",
    icon: "bank-outline",
    route: "contas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Receitas",
    icon: "arrow-top-right-bold-outline",
    route: "receitas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Despesas",
    icon: "arrow-bottom-right-bold-outline",
    route: "despesas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Categorias",
    icon: "bookmark-minus-outline",
    route: "categorias",
    adminOnly: false,
    traderOnly: false,
  },
  // { name: "Mais Opçõs", icon: "dots-horizontal", route: "dashboard" },
]);

const items = [
  {
    title: "Foo",
    value: "foo",
  },
  {
    title: "Bar",
    value: "bar",
  },
  {
    title: "Fizz",
    value: "fizz",
  },
  {
    title: "Buzz",
    value: "buzz",
  },
];

const drawer = ref(false);
const group = ref(null);

watch(group, () => {
  drawer.value = false;
});

const props = defineProps({
  mesReferencia: {
    type: String,
    default: "",
  },
});
// const titulo = computed(() => props.name);
const router = useRouter();
const useAuth = useAuthStore();
const useUser = useUserStore();

let name = ref(useUser.user.name.split(" ")[0]);

// const items = ref([{ title: "Sair", icon: "power", action: logout }]);

async function logout() {
  try {
    await http.post("/logout");
    useAuth.clear();
    router.push({ name: "home" });
  } catch (error) {
    // console.log(error);
  }
}

import CabecalhoMobile from "@/components/mobile/CabecalhoMobile.vue";
import MenuLateralMobile from "@/components/mobile/MenuLateralMobile.vue";

import { computed, ref } from "vue";

import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { formatValue } from "@/utils/formatValue";
import { useWalletsStore } from "@/store/wallets";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();

const menuExpandido = ref(false);

// const formatValue = (value: number): string =>{
//     let valueFormatted = (value / 100).toLocaleString("pt-BR", { style: "decimal", minimumFractionDigits: 2, maximumFractionDigits: 2 });
//     return valueFormatted;
// };
// console.log(mesAnoReferencia.value);
// const mesPorExtenso = mesAnoReferencia ? mesAnoReferencia.value.split(" ")[0] : '';
let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
let saldoInicial = ref(useWallets.walletsData?.saldoInicial);
let valueTotalExpensesMonth = ref(
  useExpenses.expensesData.expenses?.ValueTotalExpensesMonth
);
let valueTotalRevenuesMonth = ref(
  useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth
);
let totalBalance = ref(useWallets.walletsData?.wallets[0].saldo);
let valorPrevisto = ref(
  saldoInicial.value +
    valueTotalRevenuesMonth.value -
    valueTotalExpensesMonth.value
);
let valuePay = ref(useExpenses.expensesData.expenses?.ValuePayExpenses);
let valueReceived = ref(
  useRevenues.revenuesData.revenues?.ValueReceivedRevenues
);
// let totalCreditCard = ref(0);
// let expensesAddTotalValueMonth = ref(useExpenses.expensesData.expenses?.ExpensesAddTotalValueMonth);
// let revenuesAddTotalValueMonth = ref(useRevenues.revenuesData.revenues?.RevenuesAddTotalValueMonth);
let totalByCategoryExpenses = ref(
  useExpenses.expensesData.expenses?.TotalByCategoryExpenses
);
let valuePendingRevenues = ref(
  useRevenues.revenuesData.revenues?.ValuePendingRevenues
);
let valuePendingExpenses = ref(
  useExpenses.expensesData.expenses?.ValuePendingExpenses
);

const isAllZeros = (arr) => {
  return arr.every((value) => value === "0,00");
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

const mesAnterior = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-");
  const dataAtual = new Date(ano, mes - 1);
  dataAtual.setMonth(dataAtual.getMonth() - 1);
  mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(mesAnoReferencia.value, "anterior");
};

const proximoMes = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-");
  const dataAtual = new Date(ano, mes - 1);
  dataAtual.setMonth(dataAtual.getMonth() + 1);
  mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(mesAnoReferencia.value, "proximo");
};

const buscarDadosMes = async (data: string, buscar: string) => {
  try {
    const res = await http.post("/buscar-dados-mes", {
      mes: data,
      buscar: buscar,
    });
    useWallets.setMesReferencia(res.data.walletsData.mes_ano_referencia);
    useExpenses.setExpensesData(res.data.expensesData);
    useRevenues.setRevenuesData(res.data.revenuesData);
    useWallets.setWalletsData(res.data.walletsData);

    mesAnoReferencia.value = res.data.walletsData.mes_ano_referencia;

    saldoInicial.value = res.data.walletsData.saldoInicial;
    totalBalance.value = res.data.walletsData.wallets[0].saldo;

    valueTotalExpensesMonth.value =
      res.data.expensesData.ValueTotalExpensesMonth;
    valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;

    valorPrevisto.value =
      saldoInicial.value +
      valueTotalRevenuesMonth.value -
      valueTotalExpensesMonth.value;

    valuePay.value = res.data.expensesData.ValuePayExpenses;
    valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;

    totalByCategoryExpenses.value =
      res.data.expensesData.TotalByCategoryExpenses;

    valuePendingRevenues.value = res.data.revenuesData.ValuePendingRevenues;
    valuePendingExpenses.value = res.data.expensesData.ValuePendingExpenses;
  } catch (error) {
    //
  }
};
</script>

<style scoped>
.containe__mobile {
  /* position: relative; */
  width: 100%;
  height: 100%;
}

.container__saldo__conta {
  color: #fefefe;
  font-size: 20px;
  background: rgba(150, 150, 150, 0.02);
  /* font-weight: bold; */
  padding: 10px;
  margin-top: 10px;
  margin-inline: 10px;
  border-radius: 10px;
}
.icon__text {
  font-size: 15px;
  color: #757575;
}
.teste2 {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.1);
  /* font-weight: bold; */
  /* text-align: center; */
  padding-inline: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  height: 60px;
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.container__visao__geral {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: rgba(150, 150, 150, 0.03);
  /* align-items: center; */
  /* font-weight: bold; */
  /* text-align: center; */
  padding: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  /* height: 60px; */
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.header__visao_geral {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  color: #bdbdbd;
}
.cards__lancamentos {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.tipo__lancamento {
  display: flex;
  flex-direction: column;
}
.lancamento {
  font-size: 15px;
  color: #bdbdbd;
}
.previsto {
  font-size: 12px;
  color: #757575;
}
.valor__previsto {
  font-size: 12px;
  color: #757575;
  display: flex;
  justify-content: end;
}
.valor {
  text-align: center;
}
.opaco {
  color: #6c757d !important;
}

.card__container {
  display: flex;
}

.cards {
  width: 33.33%;
  color: #ccc;
  font-size: 30px;
  background-color: rgba(0, 0, 0, 0.1);
}
.chart__container {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  margin-top: 10px;
  height: calc(100% - 125px);
  padding: 10px 0 0 0;
}

.chart1 {
  background: transparent;
}

.container__charts {
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  background-color: rgba(0, 0, 0, 0.1);
  display: flex;
  padding: 0;
  height: 100%;
}
.icon {
  /* margin-left: 13px; */
  color: #77d08e;
}
.txt__link {
  margin-left: 20px;
  transition: 0.5s;
  color: #fefefe;
  text-align: center !important;
}
.efeitoClick {
  box-shadow: inset -4px -4px 5px #3e4247, inset 7px 7px 7px #1d1f23;
  border-top-right-radius: 32px;
  border-bottom-right-radius: 32px;
}
.cabecalho {
  padding-inline: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* background-color: rgba(0, 0, 0, 0.1); */
  height: 90px;
}

.mdicon {
  /* color: #77d08e; */
  color: #757575;
  cursor: pointer;
  /* padding: 10px; */
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 20px;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.container__mes {
  width: 100%;
  display: flex;
  justify-content: space-around;
}
</style>
