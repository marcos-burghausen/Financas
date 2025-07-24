<template>
  <v-container class="h-100 p-0 d-flex justify-content-center">
    <FormLancamentos
      v-if="formulario"
      :releases="selectedRelease"
      rota="revenue"
      :mes-referencia="mesAnoReferencia"
      transaction-type="Receita"
      @update-data="updateData"
      @close-form="closeForm"
    />
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
                :disabled="revenue.status === 'Efetivada'"
                @click="receiveRevenue(revenue.id!, revenue.conta!)"
              />
            </v-container>
            <div style="width: 100%">
              <div class="header__visao_geral">
                <span style="text-align: start; height: 22px">{{
                  revenue.conta
                }}</span>
                <div>
                  <span>{{ revenue.dataVencimento }}</span>
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
                          @click="deletar(revenue.id!)"
                        />
                      </v-list>
                    </v-menu>
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between">
                <span class="descricao">{{ revenue.descricao }}</span>
                <span class="descricao">
                  R$ {{ formatValue(Number(revenue.valor)) }}</span
                >
              </div>
              <div>
                <span class="sub__categoria px-3">{{ revenue.categoria }}</span>
                <span v-if="revenue.subcategoria !== 'Outros'" class="sub__categoria px-3">{{ revenue.subcategoria }}</span>
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
import { computed, ref } from "vue";

import FormLancamentos from "@/components/FormLancamentos.vue";
import NoDataComponent from "@/components/mobile/NoDataComponent.vue";

import http from "@/services/http";

import {
  useExpensesStore,
  useRevenuesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";

import type { Lancamento, TransactionsData } from "@/types";

import { formatValue } from "@/utils/formatValue";

import { isToday, isPast, differenceInCalendarDays, parseISO } from 'date-fns';

const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useExpenses = useExpensesStore();
const useUser = useUserStore();

const formulario = ref(false);
const selectedRelease = ref<Lancamento | undefined>(undefined);
const mesAnoReferencia = ref<string>(useUser.mesAno || "");
const valueTotalRevenuesMonth = ref(useRevenues.revenuesData?.valueTotalMonth);
const revenuesMonth = ref<Lancamento[]>(
  useRevenues.revenuesData?.byMonth || []
);

interface ApiError {
  response?: {
    data?: {
      errors?: { [key: string]: string[] };
    };
  };
}

const getIconForRevenue = (revenue: Lancamento) => {
  if (revenue.status === 'Efetivada') {
    return 'mdi-check';
  }

  if (revenue.status === 'Pendente') {
    if (!revenue.dataVencimento) {
      return 'mdi-calendar-question';
    }

    let dataVencimento: Date;
    if (typeof revenue.dataVencimento === 'string') {
      dataVencimento = parseISO(revenue.dataVencimento);
    } else {
      dataVencimento = revenue.dataVencimento;
    }
    
    const hoje = new Date();

    const diffEmDias = differenceInCalendarDays(dataVencimento, hoje);

    if (diffEmDias < 0) {
      return 'mdi-calendar-alert'; 
    }

    if (diffEmDias >= 0 && diffEmDias <= 3) {
      return 'mdi-alert';
    }

    if (diffEmDias >= 4) {
      return 'mdi-clock-outline';
    }
  }
};

const getClassForRevenue = (revenue: Lancamento) => {
  if (revenue.status === 'Efetivada') {
    return 'paga';
  }

  if (revenue.status === 'Pendente') {
    if (!revenue.dataVencimento) {
      return 'pendente';
    }

    let dataVencimento: Date;
    if (typeof revenue.dataVencimento === 'string') {
      dataVencimento = parseISO(revenue.dataVencimento);
    } else {
      dataVencimento = revenue.dataVencimento; // Já é um objeto Date
    }
    
    const hoje = new Date();

    const diffEmDias = differenceInCalendarDays(dataVencimento, hoje);

    if (diffEmDias < 0) {
      return 'atrasada'; 
    }

    if (diffEmDias >= 0 && diffEmDias <= 3) {
      return 'pendente';
    }

    if (diffEmDias >= 4) {
      return 'em__dia';
    }
  }
};

const mesPorExtenso = computed(() => {
  if (!mesAnoReferencia.value) return "";
  const [ano, mes] = mesAnoReferencia.value.split("-");
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
  selectedRelease.value = undefined;
  formulario.value = true;
};

const editRevenue = (revenue: Lancamento) => {
  console.log(revenue);
  selectedRelease.value = { ...revenue };
  formulario.value = true;
};

const closeForm = () => {
  formulario.value = false;
  selectedRelease.value = undefined;
};

const updateData = (newData: TransactionsData) => {
  useRevenues.setRevenuesData(newData);
  valueTotalRevenuesMonth.value = newData.valueTotalMonth;
  revenuesMonth.value = newData.byMonth || [];
  closeForm();
};

const mesAnterior = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-").map(Number);
  const dataAtual = new Date(ano, mes - 1);
  dataAtual.setMonth(dataAtual.getMonth() - 1);
  mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(mesAnoReferencia.value);
};

const proximoMes = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-").map(Number);
  const dataAtual = new Date(ano, mes - 1);
  dataAtual.setMonth(dataAtual.getMonth() + 1);
  mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(mesAnoReferencia.value);
};

const buscarDadosMes = async (data: string) => {
  try {
    const res = await http.post("/buscar-dados-mes", { mes: data });
    useUser.setMesAno(res.data.walletsData.mes_ano_referencia);
    useExpenses.setExpensesData(res.data.expensesData);
    useRevenues.setRevenuesData(res.data.revenuesData);
    useWallets.setWalletsData(res.data.walletsData);
    mesAnoReferencia.value = res.data.walletsData.mes_ano_referencia;
    revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
    valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao buscar dados do mês::", apiError.response?.data);
  }
};

const deletar = async (id: number) => {
  try {
    const res = await http.delete(`/revenue/${id}`, {
      data: { mesReferencia: mesAnoReferencia.value },
    });
    useRevenues.setRevenuesData(res.data.revenuesData);
    valueTotalRevenuesMonth.value = res.data.revenuesData.valueTotalMonth;
    revenuesMonth.value = res.data.revenuesData.byMonth;
  } catch (error: unknown) {
    const apiError = error as ApiError;
    console.error("Erro ao deletar receita:", apiError.response?.data);
  }
};

const receiveRevenue = async (revenueId: number, conta: string) => {
  try {
    const payload = {
      conta,
      mesReferencia: mesAnoReferencia.value,
    };
    const res = await http.patch(`/revenue/${revenueId}`, payload);
    useRevenues.setRevenuesData(res.data.revenuesData);
    useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
    useWallets.setContas(res.data.walletsData.wallets);
    console.log(res.data.walletsData.wallets);
    // Update UI or emit event
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao receber receita:", apiError.response?.data);
  }
};
</script>

<style scoped>
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
  border-radius: 15px;
}
</style>
