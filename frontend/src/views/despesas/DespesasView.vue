<template>
  <div class="content-wrapper">
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
        <mdicon
          name="arrow-left"
          size="25"
        />
      </router-link>
      <div class="header__items">
        <div class="d-flex flex-column">
          <span class="fs-5"> Despesas </span>
          <span class="valor">
            RS {{ formatValue(valueTotalExpensesMonth) }}
          </span>
        </div>
        <!-- <div>
                    <mdicon
                        name="magnify"
                        class="mdicon me-3"
                        size="25"
                    />
                    <mdicon
                        name="clipboard-text"
                        class="mdicon me-2"
                        size="25"
                    />
                    <mdicon
                        name="dots-vertical"
                        class="mdicon"
                        size="25"
                    />
                </div> -->
      </div>
    </div>
    <div class="container__mes">
      <mdicon
        name="chevron-left"
        class="mdicon"
        size="30"
        @click="mesAnterior()"
      />
      <span class="mes"> {{ mesPorExtenso }} </span>
      <mdicon
        name="chevron-right"
        class="mdicon"
        size="30"
        @click="proximoMes()"
      />
    </div>
    <button
      v-if="!formStoreExpense && !formEditExpense"
      class="btn__nova__despesa"
      @click="formStoreExpense = !formStoreExpense"
    >
      <mdicon
        name="plus"
        class="mdicon"
        size="30"
      />
    </button>

    <!-- <FormLancamentos /> -->
    <!-- ========================================================================= -->
    <!-- ================ inicio formulario lançamentos despesas ================= -->
    <!-- ========================================================================= -->
    <div
      v-if="formStoreExpense"
      class="container-fluid"
    >
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <!-- <form class="form" @submit.prevent="salvarLancamentos"> -->
          <v-form
            v-model="validFormLancamentos"
            class="form"
            @submit.prevent="salvarLancamentos"
          >
            <v-text-field
              v-model="release.valor"
              autofocus
              density="compact"
              prefix="R$"
              placeholder="0,00"
              variant="outlined"
              type="tel"
              hide-details="auto"
              label="Valor"
              :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
              class="mb-5 input"
              @input="formatValueSave()"
            />

            <div class="form-check form-switch text-white">
              <input
                id="flexSwitchCheckChecked"
                v-model="status"
                class="form-check-input mb-5"
                type="checkbox"
                checked
              >
              <label
                class="form-check-label"
                for="flexSwitchCheckChecked"
              >Paga</label>
            </div>

            <v-text-field
              v-model="release.date"
              density="compact"
              variant="outlined"
              type="date"
              hide-details="auto"
              label="Data"
              :rules="[rules.requiredData]"
              class="mb-7 input"
            />

            <v-text-field
              v-model="release.descricao"
              density="compact"
              variant="outlined"
              type="text"
              hide-details="auto"
              label="Descriçao"
              :rules="[rules.requiredDescricao]"
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="release.categoria"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCatagoria]"
              :items="categoriasNames"
              label="Categoria"
              placeholder="Select..."
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="release.carteira"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCarteira]"
              :items="carteiras"
              label="Carteira"
              placeholder="Select..."
              class="mb-7 input"
            />

            <div
              class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4"
            >
              <v-btn
                :disabled="loading"
                :loading="loading"
                style="background-color: #dc3545; color: #fefefe"
                class="px-5"
                @click="
                  {
                    formStoreExpense = !formStoreExpense;
                  }
                  clearInputs();
                "
              >
                Cancelar
              </v-btn>
              <v-btn
                :disabled="
                  loading || !validFormLancamentos || release.valor === '0,00'
                "
                :loading="loading"
                style="background-color: #77d08e"
                class="btn-light px-5"
                type="submit"
              >
                Salvar
              </v-btn>
            </div>
          </v-form>
        </div>
      </div>
    </div>
    <!-- ========================================================================= -->
    <!-- ================= fim formulario lançamentos despesas==================== -->
    <!-- ========================================================================= -->

    <!-- ========================================================================= -->
    <!-- ================== inicio formulario editar despesa =================== -->
    <!-- ========================================================================= -->
    <div
      v-if="formEditExpense"
      class="container-fluid"
    >
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <v-form
            v-model="validFormEdit"
            class="form"
            @submit.prevent="saveEditedExpense"
          >
            <v-text-field
              v-model="expenseEdit.valor"
              density="compact"
              prefix="R$"
              variant="outlined"
              type="tel"
              hide-details="auto"
              label="Valor"
              :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
              class="mb-5 input"
              @input="formatValueEdit()"
            />

            <v-text-field
              v-model="expenseEdit.date"
              density="compact"
              variant="outlined"
              type="date"
              hide-details="auto"
              label="Data"
              :rules="[rules.requiredData]"
              class="mb-7 input"
            />

            <v-text-field
              v-model="expenseEdit.descricao"
              density="compact"
              variant="outlined"
              type="text"
              hide-details="auto"
              label="Descriçao"
              :rules="[rules.requiredDescricao]"
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="expenseEdit.status"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCatagoria]"
              :items="['PAGA', 'AGUARDANDO']"
              label="Status"
              placeholder="Select..."
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="expenseEdit.categoria"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCatagoria]"
              :items="categoriasNames"
              label="Categoria"
              placeholder="Select..."
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="expenseEdit.carteira"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCarteira]"
              :items="carteiras"
              label="Carteira"
              placeholder="Select..."
              class="mb-7 input"
            />

            <div
              class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4"
            >
              <v-btn
                :disabled="loading"
                :loading="loading"
                style="background-color: #dc3545; color: #fefefe"
                class="px-5"
                @click="
                  revertEdit();
                  {
                    formEditExpense = !formEditExpense;
                  }
                "
              >
                Cancelar
              </v-btn>
              <v-btn
                :disabled="
                  loading || !validFormEdit || release.valor === '0,00'
                "
                :loading="loading"
                style="background-color: #77d08e"
                class="btn-light px-5"
                type="submit"
              >
                Salvar
              </v-btn>
            </div>
          </v-form>
        </div>
      </div>
    </div>
    <!-- ========================================================================= -->
    <!-- =================== fim formulario editar despesa ===================== -->
    <!-- ========================================================================= -->

    <div v-if="expensesMonth && expensesMonth.length > 0">
      <div
        v-if="!formStoreExpense && !formEditExpense"
        class="container-fluid"
      >
        <div
          v-for="(expense, key) in expensesMonth"
          :key="expense.id"
          class="container__table"
        >
          <div class="card__lancamento">
            <!-- :class="{ recebida: revenue.status === 'RECEBIDA' }" -->
            <div
              class="mdicon__card"
              :disabled="expense.status === 'RECEBIDA'"
              @click="payExpense(expense)"
            >
              <mdicon
                :name="
                  expense.status === 'PAGA'
                    ? 'check'
                    : new Date() <= new Date(expense.date)
                      ? 'alert'
                      : 'alert-remove'
                "
                class="mdicon__lacamento"
                :class="{
                  paga: expense.status === 'PAGA',
                  aguardando:
                    new Date() <= new Date(expense.date) &&
                    expense.status === 'AGUARDANDO',
                  atrasada:
                    new Date() > new Date(expense.date) &&
                    expense.status === 'AGUARDANDO',
                }"
                size="30"
              />
            </div>
            <div style="width: 100%">
              <div class="header__visao_geral">
                <span style="text-align: start">
                  {{ expense.carteira }}
                </span>
                <div>
                  <span>
                    {{ expense.date }}
                  </span>
                  <span>
                    <mdicon
                      name="dots-vertical"
                      class="mdicon"
                      size="25"
                    />
                    <v-menu
                      activator="parent"
                      location="bottom end"
                      transition="fade-transition"
                    >
                      <!-- style="background-color: rgb(15, 15, 15); box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;" -->
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
                          @click="displayFormEditExpense(expense)"
                        />
                        <v-list-item
                          title="Excluir"
                          link
                          @click="deletar(expense.id)"
                        />
                      </v-list>
                    </v-menu>
                  </span>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between">
                <span class="categoria">
                  {{ expense.descricao }}
                </span>
                <span class="categoria">
                  R$ {{ formatValue(expense.valor) }}
                </span>
              </div>
              <div>
                <span class="sub__categoria">
                  {{ expense.categoria }}
                </span>
                <span class="sub__categoria">
                  {{ expense.categoria }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <NoDataComponent v-else />
  </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";
import NoDataComponent from "@/components/mobile/NoDataComponent.vue";

import { ref, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useExpensesStore } from "@/store/expenses";
import { useUserStore } from "@/store/user";
import http from "@/services/http";
import { formatValue } from "@/utils/formatValue";
import { computed } from "vue";

import type { RevenueEdit } from "@/types/revenueEdit";
import { useWalletsStore } from "@/store/wallets";
import { useRevenuesStore } from "@/store/revenues";

const useRevenues = useRevenuesStore();

const useExpenses = useExpensesStore();
const userStore = useUserStore();
const useWallets = useWalletsStore();

let validFormLancamentos = ref(false);
let validFormEdit = ref(false);
let loading = ref(false);

let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
let valueTotalExpensesMonth = ref(
    useExpenses.expensesData.expenses?.ValueTotalExpensesMonth
);
let valuePending = ref(useExpenses.expensesData.expenses?.ValuePendingExpenses);
let expensesMonth = ref(useExpenses.expensesData.expenses?.ExpensesMonth);
let valuePay = ref(
    formatValue(useExpenses.expensesData.expenses?.ValuePayExpenses)
);
// let categorias = ref(userStore.user.categoriasDespesas);
const categoriasNames = ref([]);
userStore.user.categoriasDespesas.forEach((categoria) => {
    categoriasNames.value.push(categoria.name);
});
// let carteiras = ref(useWallets.walletsData.wallets.map((wallet) => wallet.name));
let carteiras = ref(useWallets.walletsData.walletsNames);
let errorsForm = ref({ errors: {} });
let formStoreExpense = ref(false);
let formEditExpense = ref(false);
let expenseEdit = ref<RevenueEdit>({
    user_id: 0,
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: "",
    created_at: "",
    updated_at: "",
    mesReferencia: mesAnoReferencia.value,
});
let expenseUnedited = ref<RevenueEdit>({
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: "",
});
let release = ref<Lancamentos>({
    valor: "",
    date: "",
    status: "",
    descricao: "",
    categoria: "",
    carteira: "",
    mesReferencia: mesAnoReferencia.value,
});

const mesPorExtenso = computed(() => {
    if (!mesAnoReferencia.value) return "";

    const  mes = mesAnoReferencia.value.split("-");

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
    buscarDadosMes(mesAnoReferencia.value);
};

const proximoMes = () => {
    const [ano, mes] = mesAnoReferencia.value.split("-");
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() + 1);
    mesAnoReferencia.value = `${dataAtual.getFullYear()}-${String(
        dataAtual.getMonth() + 1
    ).padStart(2, "0")}`;
    buscarDadosMes(mesAnoReferencia.value);
};

const buscarDadosMes = async (data) => {
    try {
        const res = await http.post("/buscar-dados-mes", { mes: data });
        useWallets.setMesReferencia(res.data.walletsData.mes_ano_referencia);
        useExpenses.setExpensesData(res.data.expensesData);
        useRevenues.setRevenuesData(res.data.revenuesData);
        useWallets.setWalletsData(res.data.walletsData);

        mesAnoReferencia.value = res.data.walletsData.mes_ano_referencia;

        expensesMonth.value = res.data.expensesData.ExpensesMonth;

        valueTotalExpensesMonth.value =
      res.data.expensesData.ValueTotalExpensesMonth;

        valuePay.value = res.data.expensesData.ValuePayExpenses;
    } catch (error) {
    //
    }
};

const formatValueSave = () => {
    let novoValor = release.value.valor.replace(/[^\d]/g, "");

    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            "."
        );
        release.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
    } else if (novoValor.length === 1) {
        release.value.valor = `0,0${novoValor}`;
    } else {
        release.value.valor = "0,00";
    }
};

const formatValueEdit = () => {
    let novoValor = expenseEdit.value.valor.replace(/[^\d]/g, "");

    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            "."
        );
        expenseEdit.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
    } else if (novoValor.length === 1) {
        expenseEdit.value.valor = `0,0${novoValor}`;
    } else {
        expenseEdit.value.valor = "0,00";
    }
};

let status = ref(true);

const clearInputs = () => {
    release.value.valor = "";
    release.value.date = "";
    release.value.descricao = "";
    release.value.categoria = "";
    release.value.carteira = "";
};

const revertEdit = () => {
    expensesMonth.value.forEach((revenue: RevenueEdit, index: number) => {
        if (revenue.id === expenseEdit.value.id) {
            console.log(expensesMonth.value);
            expensesMonth.value[index] = JSON.parse(
                JSON.stringify(expenseUnedited)
            );
            console.log(expensesMonth.value);
        }
    });
};

const returnExpense = () => {
    formStoreExpense.value =
    formStoreExpense.value === true
        ? !formStoreExpense.value
        : formStoreExpense.value;
    formEditExpense.value =
    formEditExpense.value === true
        ? !formEditExpense.value
        : formEditExpense.value;
};

const salvarLancamentos = async () => {
    try {
        release.value.status = status.value ? "PAGA" : "AGUARDANDO";
        const res = await http.post("/save-expense", release.value);
        useExpenses.setExpensesData(res.data.expensesData);
        valueTotalExpensesMonth.value =
      res.data.expensesData.ValueTotalExpensesMonth;
        valuePay.value = res.data.expensesData.ValuePayExpenses;
        valuePending.value = res.data.expensesData.ValuePendingExpenses;
        expensesMonth.value = res.data.expensesData.ExpensesMonth;
        useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
        useWallets.setWallets(res.data.walletsData.wallets);
        clearInputs();
        formStoreExpense.value = false;
    } catch (error) {
    // console.log(error);
        errorsForm.value["errors"] = error.response.data.errors;
    }
};

const payExpense = async (expense: Lancamentos) => {
    try {
        const res = await http.post("/pay-expense", {
            id: expense.id,
            mesReferencia: mesAnoReferencia.value,
        });
        useExpenses.setExpensesData(res.data.expensesData);
        valuePending.value = res.data.expensesData.ValuePendingExpenses;
        valuePay.value = res.data.expensesData.ValuePayExpenses;
        // expense.status = 'PAGA';
        expensesMonth.value.forEach((expenses) => {
            if (expenses.id === expense.id) {
                expense.status = "PAGA";
            }
        });
        useWallets.setWallets(res.data.walletsData.wallets);
    } catch (error) {
    // console.log(error);
    }
};

function displayFormEditExpense(expense: RevenueEdit) {
    expenseUnedited = JSON.parse(JSON.stringify(expense));
    expenseEdit.value = expense;
    expenseEdit.value.valor = formatValue(Number(expenseEdit.value.valor));
    formEditExpense.value = true;
}

const saveEditedExpense = async () => {
    try {
        const res = await http.post("/edit-expense", expenseEdit);
        useExpenses.setExpensesData(res.data.expensesData);
        useWallets.setWallets(res.data.walletsData.wallets);
        valueTotalExpensesMonth.value =
      res.data.expensesData.ValueTotalExpensesMonth;
        valuePending.value = res.data.expensesData.ValuePendingExpenses;
        valuePay.value = res.data.expensesData.ValuePayExpenses;
        expensesMonth.value = res.data.expensesData.ExpensesMonth;
    } catch (error) {
    // console.log(error);
    }

    formEditExpense.value = false;
};

const deletar = async (id: number) => {
    try {
        const res = await http.post("/delete-expense", {
            id: id,
            mesReferencia: mesAnoReferencia.value,
        });
        useExpenses.setExpensesData(res.data.expensesData);
        valueTotalExpensesMonth.value =
      res.data.expensesData.ValueTotalExpensesMonth;
        valuePending.value = res.data.expensesData.ValuePendingExpenses;
        valuePay.value = res.data.expensesData.ValuePayExpenses;
        expensesMonth.value = res.data.expensesData.ExpensesMonth;
    } catch (error) {
    // console.log(error);
    }
};

const rules = {
    requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
    requiredValorMaiorQue0: (value: string) =>
        parseFloat(value.replace(",", ".")) > 0 ||
    "O campo valor deve ser maior que zero",
    requiredData: (value: string) => !!value || "O campo data é obrigatório",
    requiredDescricao: (value: string) =>
        !!value || "O campo escriçãp é obrigatório",
    requiredCatagoria: (value: string) =>
        !!value || "O campo categoria é obrigatório",
    requiredCarteira: (value: string) =>
        !!value || "O campo categoria é obrigatório",
};
</script>

<style scoped>
.content-wrapper {
  display: flex;
  flex-direction: column;
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
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.btn__nova__despesa {
  position: fixed;
  right: calc(
    (100vw - 500px) / 2 + 55px
  ); /* Calcula a posição relativa ao centro do #app */
  bottom: 15px;
  background-color: #ff0000;
  border: none;
  border-radius: 50%;
  padding: 10px;
  color: #fefefe;
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
  align-items: end;
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
.aguardando {
  color: #e5ff00 !important;
  background: #e5ff0021 !important;
}
.header__visao_geral {
  display: flex;
  justify-content: space-between;
  color: #757575;
}
.color {
  color: #bdbdbd;
}
.categoria {
  font-size: 20px;
  color: #bdbdbd;
  padding-right: 27px;
}
.sub__categoria {
  font-size: 15px;
  background: #1dbb01;
  margin-right: 5px;
  padding-inline: 5px;
  border-radius: 15px;
}

.conta__lancamento {
  display: flex;
  flex-direction: column;
}

.cadastro {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  padding-top: 15px;
  width: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.inputSimples {
  background-color: #1e1e1e;
  margin: 20px 0 0 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 5px;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  background: transparent;
  bottom: 0;
  color: transparent;
  cursor: pointer;
  height: auto;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  width: auto;
}

.error {
  height: 20px;
}

.span-error {
  color: rgb(194, 4, 4);
  position: relative;
  top: 0;
  left: 0;
}

.mdicon {
  color: white;
}

.options {
  background-color: #292d32;
}
.form {
  display: flex;
  flex-direction: column;
  width: 100% !important;
  padding: 10px;
}

.input {
  background-color: #1e1e1e !important;
  height: 40px;
  color: #ccc;
  width: 100%;
  border: none;
}

.input:focus ~ label,
.input:valid ~ label {
  transform: translateY(-30px);
  opacity: 0.9;
}

.input:internal-autofill-selected {
  background-color: transparent;
}

.label {
  color: #ccc;
  position: absolute;
  left: 10px;
  top: 8px;
  opacity: 0.4;
  cursor: text;
  transition: 0.5s ease-in-out;
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

.btn {
  height: 40px;
  /* margin-top: 15px; */
}

.pay {
  color: #1dbb01 !important;
}

@media screen and (max-width: 600px) {
  .card__container {
    flex-direction: column;
  }
  .cards {
    width: 100%;
  }
}
</style>
