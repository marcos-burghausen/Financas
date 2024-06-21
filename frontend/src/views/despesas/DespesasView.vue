<template>
  <div class="content-wrapper">
    <div class="pagetitle">
      <nav class="d-flex justify-content-between ms-3 mb-3">
        <ol class="breadcrumb bg-transparent">
          <span class=" me-3 text-white">
            <router-link
              class="link"
              :to="{ name: 'dashboard' }"
            >
              <mdicon
                name="arrow-left"
                size="20"
              />
            </router-link>
          </span>
          <!-- :class="{ opaco: !formStoreExpense && !formEditExpense }" -->
          <li
            class="breadcrumb-item text-white"
            @click="returnExpense"
          >
            despesas
          </li>
          <li
            v-if="formStoreExpense"
            :class="{ opaco: formStoreExpense }"
            class="breadcrumb-item"
          >
            cadastrar de despesa
          </li>
          <li
            v-if="formEditExpense"
            :class="{ opaco: formEditExpense }"
            class="breadcrumb-item"
          >
            editar de despesa
          </li>
        </ol>
        <!-- <FormLancamentos /> -->
        <button
          v-if="!formStoreExpense && !formEditExpense"
          class="btn btn-danger text-whit"
          @click="formStoreExpense = !formStoreExpense"
        >
          nova despesa
        </button>
      </nav>
    </div>

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
            
            <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
              <v-btn
                :disabled="loading"
                :loading="loading"
                style="background-color: #dc3545; color: #fefefe;"
                class="px-5"
                @click="{ formStoreExpense = !formStoreExpense }; clearInputs()"
              >
                Cancelar
              </v-btn>
              <v-btn
                :disabled="loading || !validFormLancamentos || release.valor === '0,00'"
                :loading="loading"
                style="background-color: #77d08e;"
                class=" btn-light px-5"
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
            
            <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
              <v-btn
                :disabled="loading"
                :loading="loading"
                style="background-color: #dc3545; color: #fefefe;;"
                class=" px-5"
                @click="revertEdit(); { formEditExpense = !formEditExpense }"
              >
                Cancelar
              </v-btn>
              <v-btn
                :disabled="loading || !validFormEdit || release.valor === '0,00'"
                :loading="loading"
                style="background-color: #77d08e;"
                class=" btn-light px-5"
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

    <div
      v-if="!formStoreExpense && !formEditExpense"
      class="container-fluid"
    >
      <div class="card__container">
        <Card
          class="cards"
          titulo="Despesas"
          :valor="valueTotalExpensesMonth"
        />
        <Card
          class="cards"
          titulo="Pendentes"
          :valor="valuePending"
        />
        <Card
          class="cards"
          titulo="Pagas"
          :valor="valuePay"
        />
      </div>
      <div class="container__table">
        <div
          v-if="expensesMonth && expensesMonth.length > 0"
          class="col-12 col-lg-12"
        >
          <div
            class="row justify-content-center card-header mx-0 py-1"
            style="background-color: rgba(0, 0, 0, 0.25)"
          >
            <div class="d-flex text-center col-2">
              <button class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-left">
                <mdicon
                  class="mdicon"
                  name="chevron-left"
                />
              </button>
              <p class="m-0 pt-1 border rounded-pill w-75 text-white">
                mes
              </p>
              <button class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-right">
                <mdicon
                  class="mdicon"
                  name="chevron-right"
                />
              </button>
            </div>
          </div>
          <div class="table-responsive">
            <table
              v-if="expensesMonth"
              class="table"
              style="background-color: rgba(0, 0, 0, 0.25); color: black;"
            >
              <thead>
                <tr>
                  <th class="text-white text-center">
                    Data
                  </th>
                  <th class="text-white text-center">
                    Descrição
                  </th>
                  <th class="text-white text-center">
                    Categoria
                  </th>
                  <th class="text-white text-center">
                    Carteira
                  </th>
                  <th class="text-white text-center">
                    Valor
                  </th>
                  <th class="text-white text-center">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(expense, key) in expensesMonth"
                  :key="expense.id"
                >
                  <td class="text-white text-center">
                    {{ expense.date }}
                  </td>
                  <td class="text-white text-center">
                    {{ expense.descricao }}
                  </td>
                  <td class="text-white text-center">
                    {{ expense.categoria }}
                  </td>
                  <td class="text-white text-center">
                    {{ expense.carteira }}
                  </td>
                  <td class="text-white text-center">
                    R$ {{ formatValue(expense.valor) }}
                  </td>
                  <td class="d-flex py-0 justify-content-center">
                    <button
                      class=" btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 text-white"
                      :class="{ pay: expense.status === 'PAGA' }"
                      :disabled="expense.status === 'PAGA'"
                      @click="payExpense(expense)"
                    >
                      <mdicon name="check-circle-outline" />
                    </button>
                    <!-- <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip mx-2"
                                            title="anexar arquivo">
                                            <mdicon name="paperclip" />
                                        </button> -->
                    <button
                      class=" btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
                      title="editar"
                      @click="displayFormEditExpense(expense)"
                    >
                      <mdicon name="pencil-outline" />
                    </button>
                    <button
                      type="submit"
                      class="btn btn-outline-table p-0 text-white fs-4 bi bi-trash3 mx-2"
                      title="deletar"
                      @click="deletar(expense.id)"
                    >
                      <mdicon name="trash-can-outline" />
                    </button>
                    <!-- <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-three-dots-vertical"
                                            title="mais opções">
                                            <mdicon name="dots-vertical" />
                                        </button> -->
                  </td>
                </tr>
              </tbody>
            </table>
            <span v-else>
              Você não possui lançamentos a serem exibidos
            </span>
          </div>
        </div>
        <h5
          v-else
          class="card-title text-white text-center"
        >
          Você não possui despesas a serem exibidas
        </h5>
      </div>
    </div>
    <div class="overlay toggle-menu" />
  </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";

import { ref, reactive, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useExpensesStore } from "@/store/expenses";
import { useUserStore } from "@/store/user";
import http from "@/services/http";
import { formatValue } from "@/utils/formatValue";
import { computed } from "vue";

import type { RevenueEdit } from "@/types/revenueEdit";

const useExpenses = useExpensesStore();
const userStore = useUserStore();

let validFormLancamentos = ref(false);
let validFormEdit = ref(false);
let loading = ref(false);

let valueTotalExpensesMonth = ref(formatValue(useExpenses.expensesData.expenses?.ValueTotalExpensesMonth));
let valuePending = ref(formatValue(useExpenses.expensesData.expenses?.ValuePendingExpenses));
let expensesMonth = ref(useExpenses.expensesData.expenses?.ExpensesMonth);
let valuePay = ref(formatValue(useExpenses.expensesData.expenses?.ValuePayExpenses));
let categorias = ref(userStore.user.categoriasDespesas);
const categoriasNames = ref([]);
userStore.user.categoriasDespesas.forEach((categoria) => {
    categoriasNames.value.push(categoria.name);
});
let carteiras = ref(userStore.user.carteiras);
let errorsForm = ref({ errors: {} });
let formStoreExpense = ref(false);
let formEditExpense = ref(false);
let expenseEdit: Ref<RevenueEdit> = ref({
    id: 0,
    user_id: 0,
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: "",
    created_at: "",
    updated_at: ""
});
const expenseUnedited: Ref<RevenueEdit> = ref({
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: ""
});
let release = ref({
    valor: "",
    date: "",
    status: "",
    descricao: "",
    categoria: "",
    carteira: "",
});

const formatValueSave = () => {
    let novoValor = release.value.valor.replace(/[^\d]/g, "");

    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
        const parteInteiraFormatada = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
            expensesMonth.value[index] = JSON.parse(JSON.stringify(expenseUnedited.value));
            console.log(expensesMonth.value);
        }
    });
};

const returnExpense = () => {
    formStoreExpense.value = formStoreExpense.value === true ? !formStoreExpense.value : formStoreExpense.value;
    formEditExpense.value = formEditExpense.value === true ? !formEditExpense.value : formEditExpense.value;
};

const salvarLancamentos = async () => {
    try {
        release.value.status = status.value ? "PAGA" : "AGUARDANDO";
        const res = await http.post("/save-expense", release.value);
        useExpenses.setExpensesData(res.data.expensesData);
        valueTotalExpensesMonth.value = formatValue(res.data.expensesData.ValueTotalExpensesMonth);
        valuePay.value = formatValue(res.data.expensesData.ValuePayExpenses);
        valuePending.value = formatValue(res.data.expensesData.ValuePendingExpenses);
        expensesMonth.value = res.data.expensesData.ExpensesMonth;
        clearInputs();
        formStoreExpense.value = false;
    } catch (error) {
        // console.log(error);
        errorsForm.value["errors"] = error.response.data.errors;
    }
};

const payExpense = async (expense: Lancamentos) => {
    try {
        const res = await http.post("/pay-expense", { "id": expense.id });
        useExpenses.setExpensesData(res.data.expensesData);
        valuePending.value = formatValue(res.data.expensesData.ValuePendingExpenses);
        valuePay.value = formatValue(res.data.expensesData.ValuePayExpenses);
        // expense.status = 'PAGA';
        expensesMonth.value.forEach(expenses => {
            if (expenses.id === expense.id) {
                expense.status = "PAGA";
            }
        });
    } catch (error) {
        // console.log(error);
    }
};

function displayFormEditExpense(expense: RevenueEdit) {
    expenseUnedited.value = JSON.parse(JSON.stringify(expense));
    expenseEdit.value = expense;
    expenseEdit.value.valor = formatValue(expenseEdit.value.valor); 
    formEditExpense.value = true;
}

const saveEditedExpense = async () => {
    try {
        const res = await http.post("/edit-expense", expenseEdit.value);
        console.log(res);
        useExpenses.setExpensesData(res.data.expensesData);
        valueTotalExpensesMonth.value = formatValue(res.data.expensesData.ValueTotalExpensesMonth);
        valuePending.value = formatValue(res.data.expensesData.ValuePendingExpenses);
        valuePay.value = formatValue(res.data.expensesData.ValuePayExpenses);
        expensesMonth.value = res.data.expensesData.ExpensesMonth;
    } catch (error) {
        // console.log(error);
    }

    formEditExpense.value = false;

};

const deletar = async (id: number) => {
    try {
        const res = await http.post("/delete-expense", { "id": id });
        useExpenses.setExpensesData(res.data.expensesData);
        valueTotalExpensesMonth.value = formatValue(res.data.expensesData.ValueTotalExpensesMonth);
        valuePending.value = formatValue(res.data.expensesData.ValuePendingExpenses);
        valuePay.value = formatValue(res.data.expensesData.ValuePayExpenses);
        expensesMonth.value = res.data.expensesData.expensesMonth;
    } catch (error) {
        // console.log(error);
    }
};

const rules = {
    requiredValor: (value: string) =>
        !!value || "O campo valor é obrigatório",
    requiredValorMaiorQue0: (value: string) =>
        parseFloat(value.replace(",", ".")) > 0 || "O campo valor deve ser maior que zero",
    requiredData: (value: string) =>
        !!value || "O campo data é obrigatório",
    requiredDescricao: (value: string) =>
        !!value || "O campo escriçãp é obrigatório",
    requiredCatagoria: (value: string) =>
        !!value || "O campo categoria é obrigatório",
    requiredCarteira: (value: string) =>
        !!value || "O campo categoria é obrigatório",
};

</script>

<style scoped>
.dashboard {
    width: 100%;
}

.link {
    text-decoration: none;
    color: #fefefe;
}

.opaco {
    color: #6c757d !important;
}

.cadastro {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    padding-top: 15px;
    width: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.container__table {
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    margin-top: 15px;

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

.input:focus~label,
.input:valid~label {
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