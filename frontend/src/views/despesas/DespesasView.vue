<template>
  <v-container class="h-100 p-0 d-flex justify-content-center">
    <FormLancamentos
      v-if="formulario"
      :releases="selectedRelease"
      rota="expense"
      :mes-ano="mesAno"
      transaction-type="Despesa"
      @update-data="updateData"
      @close-form="closeForm"
    />
    <div
      v-if="!formulario"
      class="receitas"
    >
      <div class="header fixed-top">
        <div class="d-flex justify-content-between">
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
              <span class="fs-5">despesas</span>
              <span class="valor">
                R$ {{ formatValue(Number(valueTotalExpensesMonth)) }}
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
        v-if="expensesMonth && expensesMonth.length > 0"
        class="container-fluid pt-15 mt-15 pb-8 mb-8"
      >
        <div
          v-for="expense in expensesMonth"
          :key="expense.id ?? undefined"
          class="container__table"
        >
          <div class="card__lancamento ps-2 pb-2">
            <v-container
              class="mdicon__card"
              :class="getClassForExpense(expense)"
            >
              <v-icon
                :icon="getIconForExpense(expense)"
                class="mdicon__lancamento"
                size="30"
                :disabled="expense.status === 'Efetivada'"
                @click="payExpense(expense.id!, expense.conta!)"
              />
            </v-container>
            <div style="width: 100%">
              <div class="header__visao_geral">
                <span style="text-align: start; height: 22px">{{ expense.conta }}</span>
                <div>
                  <span>{{ expense.dataVencimento }}</span>
                  <span>
                    <v-icon
                      icon="mdi-dots-vertical"
                      class="mdicon"
                      size="25"
                    />
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
                          @click="editExpense(expense)"
                        />
                        <v-list-item
                          title="Excluir"
                          link
                          @click="deletar(expense)"
                        />
                      </v-list>
                    </v-menu>
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between">
                <span class="descricao">{{ expense.descricao }}</span>
                <span class="descricao">R$ {{ formatValue(Number(expense.valor)) }}</span>
              </div>
              <div>
                <span class="sub__categoria">{{ expense.categoria }}</span>
                <span
                  v-if="expense.subcategoria !== 'Outros'"
                  class="sub__categoria px-3"
                >{{ expense.subcategoria }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <NoDataComponent v-else />
    </div>
    <div
      v-if="!formulario"
      class="fixed-bottom d-flex justify-end pe-6 pb-6"
    >
      <v-icon
        type="button"
        title="Adicionar nova despesa"
        icon="mdi-plus"
        class="mdicon__add"
        @click="openCreateForm"
      />
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { storeToRefs } from "pinia";

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

import { isToday, isPast, differenceInCalendarDays, parseISO } from "date-fns";

const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useExpenses = useExpensesStore();
const useUser = useUserStore();

const formulario = ref(false);
const selectedRelease = ref<Lancamento | undefined>(undefined);
const mesAno = ref<string>(useUser.mesAno || "");
const valueTotalExpensesMonth = ref(useExpenses.expensesData?.valueTotalMonth);
const expensesMonth = ref<Lancamento[]>(useExpenses.expensesData?.byMonth || []);

interface ApiError {
  response?: {
    data?: {
      errors?: { [key: string]: string[] };
    };
  };
}

const getIconForExpense = (expense: Lancamento) => {
  if (expense.status === "Efetivada") {
    return "mdi-check";
  }

  if (expense.status === "Pendente") {
    if (!expense.dataVencimento) {
      return "mdi-calendar-question";
    }

    let dataVencimento: Date;
    if (typeof expense.dataVencimento === "string") {
      dataVencimento = parseISO(expense.dataVencimento);
    } else {
      dataVencimento = expense.dataVencimento;
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

const getClassForExpense = (expense: Lancamento) => {
  if (expense.status === "Efetivada") {
    return "paga";
  }

  if (expense.status === "Pendente") {
    if (!expense.dataVencimento) {
      return "pendente";
    }

    let dataVencimento: Date;
    if (typeof expense.dataVencimento === "string") {
      dataVencimento = parseISO(expense.dataVencimento);
    } else {
      dataVencimento = expense.dataVencimento;
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
  selectedRelease.value = undefined;
  formulario.value = true;
};

const editExpense = (expense: Lancamento) => {
  selectedRelease.value = { ...expense };
  formulario.value = true;
};

const closeForm = () => {
  formulario.value = false;
  selectedRelease.value = undefined;
};

const updateData = (newData: TransactionsData) => {
  useExpenses.setExpensesData(newData);
  valueTotalExpensesMonth.value = newData.valueTotalMonth;
  expensesMonth.value = newData.byMonth || [];
  closeForm();
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
    const res = await http.post("/buscar-dados-mes", { mes: data });
    useUser.setMesAno(res.data.mesAno);
    useExpenses.setExpensesData(res.data.expenses);
    useRevenues.setRevenuesData(res.data.revenues);
    useWallets.setWalletsData(res.data.wallets);
    mesAno.value = res.data.mesAno;
    expensesMonth.value = res.data.expenses.byMonth || [];
    valueTotalExpensesMonth.value = res.data.expenses.valueTotalMonth;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao buscar dados do mês::", apiError.response?.data);
  }
};

const deletar = async (expense: Lancamento) => {
  try {
    const res = await http.delete(`/lancamentos/${expense.id}`, {
      data: {
        mesAno: mesAno.value,
        tipo: expense.tipo
      },
    });
    useExpenses.setExpensesData(res.data.expenses);
    expensesMonth.value = res.data.expenses.byMonth || [];
    useWallets.setSaldoInicial(res.data.wallets.saldoInicial);
    useWallets.setWalletsData(res.data.wallets);
  } catch (error: unknown) {
    const apiError = error as ApiError;
    console.error("Erro ao deletar despesa:", apiError.response?.data);
  }
};

const payExpense = async (expenseId: number, conta: string) => {
  try {
    const payload = {
      conta,
      mesAno: mesAno.value,
    };
    const res = await http.patch(`/lancamentos/${expenseId}`, payload);
    useExpenses.setExpensesData(res.data.expenses);
    valueTotalExpensesMonth.value = res.data.expenses.valueTotalMonth;
    expensesMonth.value = res.data.expenses.byMonth || [];
    useWallets.setSaldoInicial(res.data.wallets.saldoInicial);
    useWallets.setWalletsData(res.data.wallets);
    useUser.setMesAno(res.data.mesAno);
    mesAno.value = res.data.mesAno;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao pagar despesa:", apiError.response?.data);
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
  background-color: #d45959ff;
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
