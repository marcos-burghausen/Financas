<template>
  <v-container class="h-100 p-0 d-flex row justify-content-center">
    <FormLancamentos
      v-if="formulario"
      :releases="selectedRelease"
      rota="revenue"
      :mes-ano="mesAno"
      transaction-type="Receita"
      @update-data="handleUpdateData"
      @close-form="closeForm"
    />

    <v-layout class="">
      <v-navigation-drawer
      v-model="drawer"
      temporary
      color="#212529"
      width="280"
      >
        <v-list>
          <v-list-item
            v-for="(item, index) in filteredItensSideBar"
            :key="index"
            :to="{ name: item.route }"
            :class="{ 'bg-primary': isActiveRoute(item.route) }"
          >
            <template #prepend>
              <v-icon :icon="item.icon" />
            </template>
            <v-list-item-title>{{ item.name }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-main>
        <v-container fluid class="receitas__view pa-6">
          
          <!-- Header -->
          <v-row class="mb-4">
            <v-col cols="12">
              <div class="d-flex align-center justify-space-between mb-2">
                <div class="d-flex align-center">
                  <v-btn
                    icon
                    variant="text"
                    @click="drawer = !drawer"
                    class="mr-2"
                  >
                    <v-icon icon="mdi-menu" size="28" />
                  </v-btn>
                  <div>
                    <h1 class="text-h4 mb-1 d-flex align-center">
                      <v-icon icon="mdi-cash-plus" size="36" class="mr-3" color="success" />
                      Receitas
                    </h1>
                    <p class="text-subtitle-1 text-grey mb-0">
                      Gerencie suas receitas e ganhos
                    </p>
                  </div>
                </div>
                <v-btn
                  color="success"
                  prepend-icon="mdi-plus"
                  @click="openAddDialog"
                >
                  Nova Receita
                </v-btn>
              </div>
            </v-col>
          </v-row>

          <!-- Summary Cards -->
          <v-row class="mb-4">
            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card__gradient card__gradient__success pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Total do Mês</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(summary.totalMes) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-cash-multiple" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="success" class="font-weight-medium">
                    <v-icon icon="mdi-calendar-month" start size="16" />
                    {{ summary.qtdMes }} receitas
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card__gradient card__gradient__info pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Recebido</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(summary.recebido) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-check-circle" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="info" class="font-weight-medium">
                    <v-icon icon="mdi-trending-up" start size="16" />
                    {{ summary.qtdRecebido }} recebidas
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card__gradient card__gradient__warning pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Pendente</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(summary.pendente) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-clock-alert" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="warning" class="font-weight-medium">
                    <v-icon icon="mdi-alert-circle" start size="16" />
                    {{ summary.qtdPendente }} a receber
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card__gradient card__gradient__primary pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Média Mensal</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(summary.mediaMensal) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-chart-line" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="primary" class="font-weight-medium">
                    <v-icon icon="mdi-chart-timeline-variant" start size="16" />
                    Últimos 3 meses
                  </v-chip>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <!-- Filters and Actions -->
          <v-row class="mb-4">
            <v-col cols="12">
              <v-card elevation="2" class="pa-4">
                <v-row>
                  <v-col cols="12" md="3">
                    <v-text-field
                      v-model="filters.search"
                      label="Buscar"
                      prepend-inner-icon="mdi-magnify"
                      variant="outlined"
                      density="compact"
                      clearable
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-select
                      v-model="filters.status"
                      label="Status"
                      prepend-inner-icon="mdi-filter"
                      variant="outlined"
                      density="compact"
                      :items="statusOptions"
                      clearable
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-select
                      v-model="filters.categoria"
                      label="Categoria"
                      prepend-inner-icon="mdi-tag"
                      variant="outlined"
                      density="compact"
                      :items="categorias"
                      clearable
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-text-field
                      v-model="filters.dataInicio"
                      label="Data Início"
                      type="date"
                      variant="outlined"
                      density="compact"
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" md="2">
                    <v-text-field
                      v-model="filters.dataFim"
                      label="Data Fim"
                      type="date"
                      variant="outlined"
                      density="compact"
                      hide-details
                    />
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
          </v-row>



        </v-container>
      </v-main>
    </v-layout>








    <div v-if="!formulario" class="receitas">
      <div class="header fixed-top">
        <div class="d-flex justify-content-between">
          <router-link
            class="link me-7 d-flex align-items-center opaco"
            :to="{ name: 'dashboard' }"
          >
            <v-icon icon="mdi-arrow-left" size="25" />
          </router-link>
          <div class="header__items">
            <div class="d-flex flex-column">
              <span class="fs-5">Receitas</span>
              <span class="valor">
                R$ {{ formatValue(valueTotalRevenuesMonth) }}
              </span>
            </div>
          </div>
        </div>
        <div class="container__mes">
          <v-icon
            icon="mdi-chevron-left"
            class="mdicon"
            size="30"
            @click="mesAnterior"
          />
          <span class="mes">{{ mesPorExtenso }}</span>
          <v-icon
            icon="mdi-chevron-right"
            class="mdicon"
            size="30"
            @click="proximoMes"
          />
        </div>
      </div>
      <div
        v-if="revenuesMonth && revenuesMonth.length > 0"
        class="container-fluid px-0 pt-15 mt-15 pb-8 mb-8"
      >
        <div
          v-for="revenue in revenuesMonth"
          :key="revenue.id ?? undefined"
          class="container__table"
        >
          <div class="card__lancamento ps-2 pb-2">
            <v-container
              class="mdicon__card"
              :class="getClassForRevenue(revenue)"
            >
              <v-icon
                :icon="getIconForRevenue(revenue)"
                class="mdicon__lacamento"
                size="30"
                :disabled="revenue.status_lancamento === 'EFETIVADA'"
                @click="receiveRevenue(revenue.id!)"
              />
            </v-container>
            <div style="width: 100%">
              <div class="header__visao_geral">
                <span style="text-align: start; height: 22px">{{ revenue.conta_model?.nome || null }}</span>
                <div>
                  <span>{{ revenue.data_vencimento }}</span>
                  <span>
                    <v-icon icon="mdi-dots-vertical" class="mdicon" size="25" />
                    <v-menu
                      activator="parent"
                      location="bottom end"
                      transition="fade-transition"
                    >
                      <v-list
                        class="color"
                        style="background-color: rgb(15, 15, 15)"
                        density="compact"
                        min-width="250"
                        rounded="lg"
                        slim
                      >
                        <v-list-item
                          title="Editar"
                          link
                          @click="editRevenue(revenue)"
                        />
                        <v-list-item
                          title="Excluir"
                          link
                          @click="deletar(revenue)"
                        />
                      </v-list>
                    </v-menu>
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between">
                <span class="descricao">{{ revenue.descricao }}</span>
                <span class="descricao">R$ {{ formatValue(Number(revenue.valor)) }}</span>
              </div>
              <div>
                <span class="sub__categoria px-3">{{ revenue.categoria }}</span>
                <span
                  v-if="revenue.subcategoria !== 'Outros'"
                  class="sub__categoria px-3"
                  >{{ revenue.subcategoria }}</span>
              </div>
              <div v-if="revenue.observacoes" class="observacoes-container mt-2">
                <v-icon icon="mdi-note-text-outline" size="small" class="observacoes-icon" />
                <span class="observacoes-text">{{ revenue.observacoes }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <NoDataComponent v-else />
    </div>

    




    <div v-if="!formulario" class="fixed-bottom d-flex justify-end pe-6 pb-6">
      <v-icon
        type="button"
        title="Adicionar nova receita"
        icon="mdi-plus"
        class="mdicon__add"
        @click="openCreateForm"
      />
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";

import FormLancamentos from "@/components/FormLancamentos.vue";
import NoDataComponent from "@/components/mobile/NoDataComponent.vue";

import http from "@/services/http";

import {
  useExpensesStore,
  useRevenuesStore,
  useRolesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";

import type { Lancamento, TransactionsData } from "@/types";

import { formatValue } from "@/utils/formatValue";

import { differenceInCalendarDays, parseISO } from "date-fns";

import { useLancamentos } from "@/composables/useLancamentos";

import { useRoute, useRouter } from 'vue-router';

// Router
const router = useRouter()
const route = useRoute()

// Stores
const rolesStore = useRolesStore()
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useExpenses = useExpensesStore();
const userStore = useUserStore();
const drawer = ref(false)
const dialog = ref(false)
const itensSideBar = ref([
  { name: "Admin", icon: "mdi-shield-crown", route: "admin", adminOnly: true },
  { name: "Trader", icon: "mdi-chart-line", route: "trader", traderOnly: true },
  { name: "Dashboard", icon: "mdi-view-dashboard", route: "dashboard" },
  { name: "Contas", icon: "mdi-bank", route: "contas" },
  { name: "Receitas", icon: "mdi-cash-plus", route: "receitas" },
  { name: "Despesas", icon: "mdi-cash-minus", route: "despesas" },
  { name: "Categorias", icon: "mdi-tag-multiple", route: "categorias" },
  { name: "Cartões de Crédito", icon: "mdi-credit-card-outline", route: "cartoes" },
  { name: "Notificações", icon: "mdi-bell", route: "notificacoes" },
  { name: "Perfil", icon: "mdi-account", route: "perfil" },
])
const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) return false
    if (item.traderOnly && !rolesStore.isTrader) return false
    return true
  })
})

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName
}

const {
  formulario,
  selectedRelease,
  loading,
  error,
  openCreateForm,
  openEditForm,
  closeForm,
  updateData,
  invalidateCache,
} = useLancamentos("receita");

onMounted(async () => {
  await updateData();
});

const openAddDialog = () => {
  dialog.value = true
}

// Options
const statusOptions = ['PENDENTE', 'RECEBIDO', 'ATRASADO']
const categorias = ['Salário', 'Freelance', 'Investimentos', 'Vendas', 'Aluguel', 'Outros']
const contas = ['Nubank', 'Banco do Brasil', 'Caixa', 'Inter']

// const formulario = ref(false);
// const selectedRelease = ref<Lancamento | undefined>(undefined);
const mesAno = ref<string>(userStore.mesAno || "");
const valueTotalRevenuesMonth = ref(useRevenues.revenuesData?.valueTotalMonth);
const revenuesMonth = ref<Lancamento[]>(useRevenues.revenuesData?.byMonth || []);

// Watch para atualizar as variáveis reativas quando o store mudar
watch(
  () => useRevenues.revenuesData,
  (newData) => {
    if (newData) {
      valueTotalRevenuesMonth.value = newData.valueTotalMonth;
      revenuesMonth.value = newData.byMonth || [];
    }
  },
  { deep: true }
);

interface ApiError {
  response?: {
    data?: {
      errors?: { [key: string]: string[] };
    };
  };
}

const getIconForRevenue = (revenue: Lancamento) => {
  if (revenue.status_lancamento === "EFETIVADA") {
    return "mdi-check";
  }

  if (revenue.status_lancamento === "PENDENTE") {
    if (!revenue.data_vencimento) {
      return "mdi-calendar-question";
    }

    let dataVencimento: Date;
    if (typeof revenue.data_vencimento === "string") {
      dataVencimento = parseISO(revenue.data_vencimento);
    } else {
      dataVencimento = revenue.data_vencimento;
    }

    const hoje = new Date();

    const diffEmDias = differenceInCalendarDays(dataVencimento, hoje);

    if (diffEmDias < 0) {
      return "mdi-calendar-alert";
    }

    if (diffEmDias >= 0 && diffEmDias <= 3) {
      return "mdi-alert";
    }

    if (diffEmDias >= 4) {
      return "mdi-clock-outline";
    }
  }
};

const getClassForRevenue = (revenue: Lancamento) => {
  if (revenue.status_lancamento === "EFETIVADA") {
    return "paga";
  }

  if (revenue.status_lancamento === "PENDENTE") {
    if (!revenue.data_vencimento) {
      return "pendente";
    }

    let dataVencimento: Date;
    if (typeof revenue.data_vencimento === "string") {
      dataVencimento = parseISO(revenue.data_vencimento);
    } else {
      dataVencimento = revenue.data_vencimento; // Já é um objeto Date
    }

    const hoje = new Date();

    const diffEmDias = differenceInCalendarDays(dataVencimento, hoje);

    if (diffEmDias < 0) {
      return "atrasada";
    }

    if (diffEmDias >= 0 && diffEmDias <= 3) {
      return "pendente";
    }

    if (diffEmDias >= 4) {
      return "em__dia";
    }
  }
};

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

const receitas = ref<any[]>([])

// Computed
const summary = computed(() => {
  const totalMes = receitas.value.reduce((sum, r) => sum + r.valor, 0)
  const recebido = receitas.value.filter(r => r.status === 'RECEBIDO').reduce((sum, r) => sum + r.valor, 0)
  const pendente = receitas.value.filter(r => r.status === 'PENDENTE').reduce((sum, r) => sum + r.valor, 0)
  
  return {
    totalMes,
    recebido,
    pendente,
    mediaMensal: totalMes, // Simplificado - deveria calcular média real
    qtdMes: receitas.value.length,
    qtdRecebido: receitas.value.filter(r => r.status === 'RECEBIDO').length,
    qtdPendente: receitas.value.filter(r => r.status === 'PENDENTE').length
  }
})

// Filters
const filters = ref({
  search: '',
  status: null,
  categoria: null,
  dataInicio: '',
  dataFim: ''
})

const filteredReceitas = computed(() => {
  return receitas.value.filter(receita => {
    if (filters.value.search && !receita.descricao.toLowerCase().includes(filters.value.search.toLowerCase())) {
      return false
    }
    if (filters.value.status && receita.status !== filters.value.status) {
      return false
    }
    if (filters.value.categoria && receita.categoria !== filters.value.categoria) {
      return false
    }
    if (filters.value.dataInicio && receita.data_vencimento < filters.value.dataInicio) {
      return false
    }
    if (filters.value.dataFim && receita.data_vencimento > filters.value.dataFim) {
      return false
    }
    return true
  })
})

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

// Função para lidar com os dados retornados do FormLancamentos
const handleUpdateData = (newData: TransactionsData) => {
  useRevenues.setRevenuesData(newData);
  valueTotalRevenuesMonth.value = newData.valueTotalMonth;
  revenuesMonth.value = newData.byMonth || [];
  invalidateCache(userStore.mesAno);
  closeForm();
};

const editRevenue = (revenue: Lancamento) => {
  openEditForm(revenue);
};

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
    revenuesMonth.value = res.data.revenues.byMonth;
    valueTotalRevenuesMonth.value = res.data.revenues.valueTotalMonth;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao buscar dados do mês::", apiError.response?.data);
  }
};

const deletar = async (revenue: Lancamento) => {
  try {
    const payload = {
      tipo: "Receita",
      mesAno: mesAno.value,
    };
    const res = await http.delete(`/lancamentos/${revenue.id}`, {
      data: payload
      });
    useRevenues.setRevenuesData(res.data.revenues);
    valueTotalRevenuesMonth.value = res.data.revenues.valueTotalMonth;
    revenuesMonth.value = res.data.revenues.byMonth;
    useWallets.setSaldoInicial(res.data.wallets.saldoInicial);
    useWallets.setWalletsData(res.data.wallets);
  } catch (error: unknown) {
    const apiError = error as ApiError;
    console.error("Erro ao deletar receita:", apiError.response?.data);
  }
};

const receiveRevenue = async (revenueId: number) => {
  try {
    const payload = {
      mesAno: mesAno.value,
    };
    const res = await http.post(`/lancamentos/${revenueId}/efetivar`, payload);
    useRevenues.setRevenuesData(res.data.revenues);
    valueTotalRevenuesMonth.value = res.data.revenues.valueTotalMonth;
    revenuesMonth.value = res.data.revenues.byMonth;
    useWallets.setSaldoInicial(res.data.wallets.saldoInicial);
    useWallets.setWalletsData(res.data.wallets);
    userStore.setMesAno(res.data.mesAno);
    mesAno.value = res.data.mesAno;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao receber receita:", apiError);
  }
};
</script>

<style scoped>
.receitas__view {
  background-color: #f5f5f5;
  min-height: 100vh;
  margin-top: 150px;
}

/* Summary cards */
.summary__card {
  transition: transform 0.2s;
}

.summary__card:hover {
  transform: translateY(-4px);
}

/* Card gradients */
.card__gradient {
  background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
  border-radius: 8px 8px 0 0;
}

.card__gradient__success {
  --gradient-start: #4CAF50;
  --gradient-end: #388E3C;
}

.card__gradient__error {
  --gradient-start: #F44336;
  --gradient-end: #D32F2F;
}

.card__gradient__primary {
  --gradient-start: #2196F3;
  --gradient-end: #1976D2;
}

.card__gradient__warning {
  --gradient-start: #FF9800;
  --gradient-end: #F57C00;
}

.card__gradient__info {
  --gradient-start: #00BCD4;
  --gradient-end: #0097A7;
}







.receitas {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100%;
  width: 100%;
  max-width: 600px;
  overflow: auto;
  padding-bottom: 10px;
}
.header {
  display: flex;
  flex-direction: column;
  padding: 10px;
  color: #bdbdbd;
  background-color: rgb(15, 15, 15);
}
.link {
  text-decoration: none;
  color: #fefefe;
}
.opaco {
  color: #757575 !important;
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
  width: 100%;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding-top: 15px;
}
.mdicon {
  color: #757575;
  cursor: pointer;
}
.mdicon__add {
  height: 45px;
  width: 45px;
  cursor: pointer;
  padding: 10px;
  border-radius: 50px;
  background-color: #77d08e;
  color: #fefefe;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.container__table {
  margin-top: 5px;
}
.card__lancamento {
  border-bottom: solid 1px #75757588;
  display: flex;
}
.mdicon__card {
  padding-top: 7px;
  display: flex;
  justify-content: center;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  margin-top: 12px;
  margin-right: 10px;
}
.mdicon__lacamento {
  background: #1dbb01;
  border-radius: 50%;
  padding: 5px;
  margin-bottom: 5px;
  background: transparent;
}
.paga {
  color: #00ff00 !important;
  background: #24cc0728 !important;
}
.atrasada {
  color: #ff000093 !important;
  background: #ff000021 !important;
}
.pendente {
  color: #e5ff00c4 !important;
  background: #e5ff0021 !important;
}
.em__dia {
  color: #727272ff !important;
  background: #81818121 !important;
}
.header__visao_geral {
  display: flex;
  justify-content: space-between;
  color: #757575;
  height: 22px;
}
.color {
  color: #bdbdbd;
}
.descricao {
  font-size: 16px;
  color: #bdbdbd;
  padding-right: 27px;
  height: 22px;
  display: flex;
  align-items: center;
}
.sub__categoria {
  font-size: 12px;
  background: #1dbb01;
  margin-right: 5px;
  padding-inline: 2px;
}
.observacoes-container {
  display: flex;
  align-items: flex-start;
  gap: 8px;
}
.observacoes-icon {
  color: #9e9e9e;
  margin-top: 2px;
}
.observacoes-text {
  font-size: 13px;
  color: #9e9e9e;
  font-style: italic;
  line-height: 1.4;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
