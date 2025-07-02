<template>
  <v-container class="h-100 p-0 d-flex justify-content-center">
    <FormLancamentos
      v-if="formulario"
      :releases="selectedRelease"
      rota="expense"
      :mes-referencia="mesAnoReferencia"
      transaction-type="Despesa"
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
          <div class="card__lancamento">
            <v-card color="transparent" class="mdicon__card">
              <v-icon
                :icon="
                  expense.status === 'Efetivada'
                    ? 'mdi-check'
                    : expense.dataVencimento &&
                      new Date() <= new Date(expense.dataVencimento) &&
                      expense.status === 'Pendente'
                    ? 'mdi-calendar-remove'
                    : 'mdi-alert'
                "
                class="mdicon__lacamento"
                :class="{
                  paga: expense.status === 'Efetivada',
                  atrasada:
                    expense.dataVencimento &&
                    new Date() > new Date(expense.dataVencimento) &&
                    expense.status === 'Pendente',
                  pendente:
                    expense.dataVencimento &&
                    new Date() <= new Date(expense.dataVencimento) &&
                    expense.status === 'Pendente',
                }"
                size="30"
                :disabled="expense.status === 'Efetivada'"
                @click="payExpense(expense.id!, expense.conta!)"
              />
            </v-card>
            <div style="width: 100%">
              <div class="header__visao_geral">
                <span style="text-align: start">{{ expense.conta }}</span>
                <div>
                  <span>{{ expense.dataVencimento }}</span>
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
                          @click="editExpense(expense)"
                        />
                        <v-list-item
                          title="Excluir"
                          link
                          @click="deletar(expense.id!)"
                        />
                      </v-list>
                    </v-menu>
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between">
                <span class="categoria">{{ expense.descricao }}</span>
                <span class="categoria"
                  >R$ {{ formatValue(Number(expense.valor)) }}</span
                >
              </div>
              <div>
                <span class="sub__categoria">{{ expense.categoria }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <NoDataComponent v-else />
    </div>
    <div v-if="!formulario" class="fixed-bottom d-flex justify-end pe-5 pb-5">
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

const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useExpenses = useExpensesStore();
const useUser = useUserStore();

const formulario = ref(false);
const selectedRelease = ref<Lancamento | undefined>(undefined);
const mesAnoReferencia = ref<string>(useUser.getMesAno() || "");

// CORREÇÃO: Usando storeToRefs e computed para manter a reatividade com a store
const { expensesData } = storeToRefs(useExpenses);
const valueTotalExpensesMonth = computed(
  () => expensesData.value?.valueTotalMonth
);
const expensesMonth = computed(() => expensesData.value?.byMonth || []);

interface ApiError {
  response?: {
    data?: {
      errors?: { [key: string]: string[] };
    };
  };
}

const mesPorExtenso = computed(() => {
  if (!mesAnoReferencia.value) return "";
  const [ano, mes] = mesAnoReferencia.value.split("-");
  const data = new Date(parseInt(ano, 10), parseInt(mes, 10) - 1);
  const anoAtual = new Date().getFullYear();

  if (data.getFullYear() === anoAtual) {
    return new Intl.DateTimeFormat("pt-BR", { month: "long" }).format(data);
  }
  return new Intl.DateTimeFormat("pt-BR", {
    month: "short",
    year: "2-digit",
  }).format(data);
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
  // Os refs locais foram removidos, as propriedades computadas serão atualizadas automaticamente
  closeForm();
};

const mesAnterior = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-").map(Number);
  const dataAtual = new Date(ano, mes - 1, 1);
  dataAtual.setMonth(dataAtual.getMonth() - 1);
  const novoMes = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(novoMes);
};

const proximoMes = () => {
  const [ano, mes] = mesAnoReferencia.value.split("-").map(Number);
  const dataAtual = new Date(ano, mes - 1, 1);
  dataAtual.setMonth(dataAtual.getMonth() + 1);
  const novoMes = `${dataAtual.getFullYear()}-${String(
    dataAtual.getMonth() + 1
  ).padStart(2, "0")}`;
  buscarDadosMes(novoMes);
};

const buscarDadosMes = async (data: string) => {
  try {
    const res = await http.post("/buscar-dados-mes", { mes: data });
    // A store já atualiza os dados, o `computed` vai refletir as mudanças
    useUser.setMesAno(res.data.data.mesAno);
    useExpenses.setExpensesData(res.data.data.expenses);
    useRevenues.setRevenuesData(res.data.data.revenues);
    useWallets.setWalletsData(res.data.data.wallets);
    mesAnoReferencia.value = res.data.data.mesAno;
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao buscar dados do mês::", apiError.response?.data);
  }
};

const deletar = async (id: number) => {
  try {
    const res = await http.delete(`/expense/${id}`, {
      data: { mesReferencia: mesAnoReferencia.value },
    });
    // A store já atualiza os dados
    useExpenses.setExpensesData(res.data.expensesData);
  } catch (error: unknown) {
    const apiError = error as ApiError;
    console.error("Erro ao deletar despesa:", apiError.response?.data);
  }
};

const payExpense = async (expenseId: number, conta: string) => {
  try {
    const payload = {
      conta,
      mesReferencia: mesAnoReferencia.value,
    };
    const res = await http.patch(`/expense/${expenseId}`, payload);
    // Atualiza todas as stores relevantes
    useExpenses.setExpensesData(res.data.expensesData);
    useRevenues.setRevenuesData(res.data.revenuesData);
    useWallets.setWalletsData(res.data.walletsData);
  } catch (error) {
    const apiError = error as ApiError;
    console.error("Erro ao pagar despesa:", apiError.response?.data);
  }
};

// CORREÇÃO: Usando watch para atualizar o mesAnoReferencia se ele mudar na store (ex: login)
watch(useUser.getMesAno, (newVal) => {
  if (newVal) {
    mesAnoReferencia.value = newVal;
  }
});
</script>

<style scoped>
/* SEU CSS AQUI (sem alterações) */
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
  background-color: #ff0000;
  color: #fefefe;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.container__table {
  margin-top: 15px;
}
.card__lancamento {
  border-bottom: solid 1px #757575;
  display: flex;
}
.mdicon__card {
  padding-right: 10px;
  display: flex;
  align-items: center;
}
.mdicon__lacamento {
  border-radius: 50%;
  padding: 5px;
  margin-bottom: 5px;
}
.paga {
  color: #1dbb01 !important;
  background: #24cc0728 !important;
}
.atrasada {
  color: #ff0000 !important;
  background: #ff000021 !important;
}
.pendente {
  color: #e5ff00 !important;
  background: #e5ff0021 !important;
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
.categoria {
  font-size: 20px;
  color: #bdbdbd;
  padding-right: 27px;
  height: 22px;
  display: flex;
  align-items: center;
}
.sub__categoria {
  font-size: 15px;
  background: #1dbb01;
  margin-right: 5px;
  padding-inline: 5px;
  border-radius: 15px;
}
</style>
