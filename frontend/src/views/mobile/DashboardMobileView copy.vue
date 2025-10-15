<template>
  <v-card
    color="transparent"
    style="width: 100%"
  >
    <v-layout>
      <v-app-bar color="transparent">
        <v-app-bar-nav-icon
          variant="text"
          @click.stop="drawer = !drawer"
        >
          <v-icon
            icon="mdi-menu"
            class="mdicon"
            size="30"
          />
        </v-app-bar-nav-icon>

        <div class="container__mes">
          <v-icon
            icon="mdi-chevron-left"
            class="mdicon"
            size="30"
            @click="mesAnterior"
          />
          <span class="mes"> {{ mesPorExtenso }} </span>
          <v-icon
            icon="mdi-chevron-right"
            class="mdicon"
            size="30"
            @click="proximoMes"
          />
        </div>

        <v-spacer />

        <v-menu>
          <template #activator="{ props }">
            <v-icon
              v-if="useAuth.isAuthenticated"
              icon="mdi-dots-vertical"
              class="mdicon me-3"
              v-bind="props"
              size="30"
            />
          </template>
          <v-list
            style="
              width: 150px;
              background: rgb(38, 38, 39);
              box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
              color: #fefefe;
            "
          >
            <v-list-item
              v-for="(item, index) in items"
              :key="index"
              :value="index"
              @click="item.action"
            >
              <v-list-item-title style="font-size: 20px">
                <v-icon
                  :icon="item.icon"
                  class="me-3 fs-3"
                />
                {{ item.title }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <!-- <v-btn
          icon="dots-vertical"
          variant="text"
        /> -->
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        temporary
        color="#212529"
      >
        <v-list>
          <v-list-item
            v-for="(item, index) in filteredItensSideBar"
            :key="index"
            :class="{ efeitoClick: elementoAtivoSideBar === index }"
            :to="{ name: item.route }"
            @click="item.action"
          >
            <!-- <router-link style="text-decoration: none"> -->
            <span class="icon">
              <v-icon :icon="item.icon" />
              <!-- Ícone do item -->
            </span>
            <span class="txt__link">
              {{ item.name }}
            </span>
            <!-- </router-link> -->
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-main class="w-100">
        <div class="container__saldo__conta d-flex justify-content-center">
          <div class="me-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <v-icon
                icon="mdi-check-circle-outline"
                size="18"
              />
              Inicial
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: saldoInicial < 0 ? '#d45959ff' : saldoInicial > 0 ? 'green' : '#757575' }"
                style="font-size: 13px"
              >
                R$ {{ formatValue(saldoInicial) }}
              </span>
            </div>
          </div>
          <div class="d-flex flex-column align-center justify-content-end">
            <span
              class="fs-5"
              style="color: #757575"
            >
              <v-icon
                :icon="
                  (totalBalance ?? 0) < 0
                    ? 'mdi-minus-circle-outline'
                    : (totalBalance ?? 0) > 0
                      ? 'mdi-heart-circle'
                      : 'mdi-circle-outline'
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
                    saldoAtual < 0
                      ? '#d45959ff'
                      : saldoAtual > 0
                        ? 'green'
                        : '#757575',
                }"
                style="font-size: 18px"
              >
                R$ {{ formatValue(saldoAtual) }}
              </span>
            </div>
          </div>
          <div class="ms-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <v-icon
                icon="mdi-clock-outline"
                size="20"
              />
              <!-- <mdicon name="plus-circle-outline" />
                    <mdicon name="minus-circle-outline" />
                    <mdicon name="clock-outline" /> -->
              Previsto
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: valorPrevisto < 0 ? '#d45959ff' : '#757575' }"
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
            <v-icon
              icon="mdi-dots-vertical"
              class="mdicon"
              size="25"
            />
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
import { computed, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

import http from "@/services/http";
import {
    useAuthStore,
    useDashboardStore,
    useExpensesStore,
    useRevenuesStore,
    useUserStore,
    useWalletsStore,
} from "@/store";
import { useRolesStore } from "@/store/roles";
import { formatValue } from "@/utils/formatValue";

const dashboardStore = useDashboardStore();
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useAuth = useAuthStore();
const userStore  = useUserStore();
const rolesStore = useRolesStore();
const router = useRouter();

onMounted(async () => {
  // Carrega dados do localStorage
  useAuth.loadFromSession();
  userStore.loadFromSession();
  dashboardStore.loadFromSession();
  useExpenses.loadFromSession();
  useRevenues.loadFromSession();
  useWallets.loadFromSession();
  
  // Verifica se está autenticado antes de buscar dados
  if (!useAuth.isAuthenticated) {
    console.warn('Usuário não autenticado, redirecionando para login');
    router.push({ name: 'home' });
    return;
  }
  
  // Carregar permissões do usuário se ainda não foram carregadas
  if (rolesStore.myRoles.length === 0) {
    try {
      await rolesStore.fetchMyPermissions();
    } catch (error) {
      console.error('Erro ao carregar permissões no dashboard:', error);
    }
  }
  
  // Só busca dados do backend se realmente não tiver nada no localStorage
  const hasExpensesData = useExpenses.expensesData.byMonth && useExpenses.expensesData.byMonth.length > 0;
  const hasRevenuesData = useRevenues.revenuesData.byMonth && useRevenues.revenuesData.byMonth.length > 0;
  
  if (!hasExpensesData && !hasRevenuesData && userStore.mesAno) {
    await buscarDadosMes(userStore.mesAno);
  }
});

const saldoAtual = computed(() => dashboardStore.summary.saldoAtual);
const totalReceitas = computed(() => dashboardStore.summary.totalReceitas);
const totalDespesas = computed(() => dashboardStore.summary.totalDespesas);
const saldoPrevisto = computed(() => dashboardStore.saldoPrevisto);

// Variáveis computadas baseadas nos stores
const valueTotalExpensesMonth = computed(() => useExpenses.expensesData?.valueTotalMonth || 0);
const valueTotalRevenuesMonth = computed(() => useRevenues.revenuesData?.valueTotalMonth || 0);
const valuePay = computed(() => useExpenses.expensesData?.valuePay || 0);
const valueReceived = computed(() => useRevenues.revenuesData?.valuePay || 0);
const valuePendingExpenses = computed(() => useExpenses.expensesData?.valuePending || 0);
const valuePendingRevenues = computed(() => useRevenues.revenuesData?.valuePending || 0);
const saldoInicial = computed(() => useWallets.walletsData?.saldo_inicial || 0);

// Valor previsto calculado
const valorPrevisto = computed(() => {
  return saldoInicial.value + valueTotalRevenuesMonth.value - valueTotalExpensesMonth.value;
});

const mesAno = ref<string>(userStore.mesAno || "");
// let valuePendingRevenues = ref(useRevenues.revenuesData?.valuePending);
// let valuePendingExpenses = ref(useExpenses.expensesData?.valuePending);
// let valueReceived = ref(useRevenues.revenuesData?.valuePay);
// let totalBalance = ref(useWallets.walletsData?.contas[0].saldo);
// // let saldoAtual = ref(useWallets.walletsData?.saldo_atual);
// let saldoInicial = ref(useWallets.walletsData?.saldo_inicial);
// let valuePay = ref(useExpenses.expensesData?.valuePay);
// // let name = ref(useUser.userData.name.split(" ")[0]);
// let valorPrevisto = ref(
//   saldoInicial.value +
//     valueTotalRevenuesMonth.value -
//     valueTotalExpensesMonth.value
// );
let elementoAtivoSideBar = ref(0);
// let totalCreditCard = ref(0);

watch(router, (value) => {
  switch (value.currentRoute.value.name) {
    case "admin":
      elementoAtivoSideBar.value = 0;
      break;
    case "trader":
      elementoAtivoSideBar.value = 1;
      break;
    case "dashboard":
      elementoAtivoSideBar.value = 2;
      break;
    case "contas":
      elementoAtivoSideBar.value = 3;
      break;
    case "receitas":
      elementoAtivoSideBar.value = 4;
      break;
    case "despesas":
      elementoAtivoSideBar.value = 5;
      break;
    case "categorias":
      elementoAtivoSideBar.value = 6;
      break;
    case "notificacoes":
      elementoAtivoSideBar.value = 7;
      break;
    case "perfil":
      elementoAtivoSideBar.value = 8;
      break;
  }
});

const itensSideBar = ref([
  {
    name: "Admin",
    icon: "mdi-shield-crown",
    route: "admin",
    adminOnly: true,
    action: () => router.push({ name: "admin" }),
  },
  {
    name: "Trader",
    icon: "mdi-chart-line",
    route: "trader",
    traderOnly: true,
    action: () => router.push({ name: "trader" }),
  },
  {
    name: "Dashboard",
    icon: "mdi-view-dashboard",
    route: "dashboard",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "dashboard" }),
  },
  {
    name: "Contas",
    icon: "mdi-bank-outline",
    route: "contas",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "contas" }),
  },
  {
    name: "Receitas",
    icon: "mdi-arrow-top-right-bold-outline",
    route: "receitas",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "receitas" }),
  },
  {
    name: "Despesas",
    icon: "mdi-arrow-bottom-right-bold-outline",
    route: "despesas",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "despesas" }),
  },
  {
    name: "Categorias",
    icon: "mdi-bookmark-minus-outline",
    route: "categorias",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "categorias" }),
  },
  {
    name: "Notificações",
    icon: "mdi-bell-ring",
    route: "notificacoes",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "notificacoes" }),
  },
  {
    name: "Perfil",
    icon: "mdi-account-circle",
    route: "perfil",
    adminOnly: false,
    traderOnly: false,
    action: () => router.push({ name: "perfil" }),
  },
]);

const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly) {
      // Usa rolesStore em vez de userStore.userData.type
      return rolesStore.isAdmin;
    } else if (item.traderOnly) {
      // Usa rolesStore para verificar roles de trader
      return rolesStore.hasAnyPermission(['USER_TRADER', 'TRADER', 'FULL']);
    }
    return true; // Exibe os outros itens normalmente
  });
});

const items = ref([{ title: "Sair", icon: "mdi-power", action: logout }]);

const drawer = ref(false);
const group = ref(null);

watch(group, () => {
  drawer.value = false;
});

// const props = defineProps({
//   mesReferencia: {
//     type: String,
//     default: "",
//   },
// });

async function logout() {
  try {
    // Sanctum: endpoint atualizado
    await http.post("/sanctum/logout");
    
    // Limpa todos os stores
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    
    // Força reload completo para limpar tudo da memória
    window.location.href = "/";
  } catch (error) {
    // Se falhar a requisição, ainda limpa localmente
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    window.location.href = "/";
  }
}

// const isAllZeros = (arr: string[]) => {
//   return arr.every((value: string) => value === "0,00");
// };

const mesPorExtenso = computed(() => {
  if (!mesAno.value) return "";

  const mes = mesAno.value.split("-")[1];

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
    const [ano, mes] = mesAno.value.split("-").map(Number);
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() - 1);
    mesAno.value = `${dataAtual.getFullYear()}-${String(
      dataAtual.getMonth() + 1
    ).padStart(2, "0")}`;
    buscarDadosMes(mesAno.value);
};

const proximoMes = () => {
    const [ano, mes] = mesAno.value.split("-").map(Number);
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() + 1);
    mesAno.value = `${dataAtual.getFullYear()}-${String(
      dataAtual.getMonth() + 1
    ).padStart(2, "0")}`;
    buscarDadosMes(mesAno.value);
};

const buscarDadosMes = async (data: string) => {
  try {
    const res = await http.post("/buscar-dados-mes", { mesAno: data });
    userStore.setMesAno(res.data.mesAno);
    useExpenses.setExpensesData(res.data.expenses);
    useRevenues.setRevenuesData(res.data.revenues);
    useWallets.setWalletsData(res.data.wallets);
    mesAno.value = res.data.mesAno;
  } catch (error) {
    console.error("Erro ao buscar dados do mês:", error);
  }
};

// =============================== grafico de barras inicio =============================== //

// let totalYearValueExpenses = ref(Object.values(expensesAddTotalValueMonth.value));
// let totalYearValueRevenues = ref(Object.values(revenuesAddTotalValueMonth.value));

// const options = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "receitas & despesas",
//         align: "left",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     dataLabels: {
//         enabled: false,
//     },
//     labels: {
//         style: {
//             color: "#fefefe"
//         }
//     },
//     colors: ["#fb0404", "#77d08e"],
//     xaxis: {
//         categories: ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"],
//     },
//     tooltip: {
//         y: {
//             formatter: function (val: Number) {
//                 return "R$ " + val;
//             }
//         }, theme: "dark",
//     }
// };
// const series = [
//     {
//         name: "despesas",
//         data: totalYearValueExpenses.value,
//     },
//     {
//         name: "receitas",
//         data: totalYearValueRevenues.value,
//     }
// ];
// =============================== grafico de barras fim =============================== //

// =============================== grafico de pizza inicio =============================== //
// let category = ref(Object.keys(totalByCategoryExpenses.value));
// let valuesCategory = ref(Object.values(totalByCategoryExpenses.value));

// const options1 = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "despesas por categoria",
//         align: "center",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     legend: {
//         position: "bottom"
//     },
//     labels: category.value
// };
// const series1 = valuesCategory.value;
// =============================== grafico de pizza fim =============================== //
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
