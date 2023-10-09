<template>
    <div class="content-wrapper">
        <div class="pagetitle">
            <nav class="d-flex justify-content-between mb-3">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item text-white">
                        <router-link class="link" :to="{ name: 'dashboard' }">
                            Dashboard
                        </router-link>
                    </li>
                    <li :class="{ opaco: !formStoreExpense & !formEditExpense }" @click="returnExpense"
                        class="breadcrumb-item text-white">
                        despesas
                    </li>
                    <li :class="{ opaco: formStoreExpense }" class="breadcrumb-item" v-if="formStoreExpense">
                        cadastrar de despesa
                    </li>
                    <li :class="{ opaco: formEditExpense }" class="breadcrumb-item" v-if="formEditExpense">
                        editar de despesa
                    </li>
                </ol>
                <button v-if="!formStoreExpense & !formEditExpense" class="btn btn-danger text-whit"
                    @click="formStoreExpense = !formStoreExpense">
                    nova despesa
                </button>
            </nav>
        </div>

        <!-- ========================================================================= -->
        <!-- ================ inicio formulario lançamentos despesas ================= -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="formStoreExpense">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <!-- <form class="form" @submit.prevent="salvarLancamentos"> -->
                    <form class="form">
                        <div class="inputSimples">
                            <!-- <mdicon class="mdicon" name="account" /> -->
                            <input v-model="releases.valor" class="input" id="valor" name="valor" type="number" required />
                            <label class="label" for="valor">Valor</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].valor" class="span-error">{{
                                errorsForm["errors"].valor[0]
                            }}</span>
                        </div>
                        <div class="form-check form-switch text-white">
                            <input v-model="status" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                checked>
                            <label class="form-check-label" for="flexSwitchCheckChecked">Foi paga</label>
                        </div>
                        <div class="inputSimples">
                            <!-- <mdicon class="mdicon" name="account" /> -->
                            <input v-model="releases.date" type="text" onfocus="this.type='date'" onblur="this.type='text'"
                                name="date" class="input" id="date" required />
                            <label for="date" class="label">Data</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].date" class="span-error">{{
                                errorsForm["errors"].date[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <!-- <mdicon class="mdicon" name="account" /> -->
                            <input v-model="releases.descricao" type="text" name="descricao" class="input" id="descricao"
                                required />
                            <label for="descricao" class="label">Descricao</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                                errorsForm["errors"].descricao[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="releases.categoria" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <!-- <option class="options" selected></option> -->
                                <option v-for="categoria in categorias" class="options" :value="categoria">{{
                                    categoria
                                }}
                                </option>
                            </select>
                            <label for="categoria" class="label">Categoria</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].categoria" class="span-error">{{
                                errorsForm["errors"].categoria[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="releases.carteira" class="input" name="carteira"
                                aria-label="Default select example" required>
                                <!-- <option class="options" selected></option> -->
                                <option v-for="carteira in carteiras" class="options" :value="carteira">{{ carteira
                                }}
                                </option>

                            </select>
                            <label for="carteira" class="label">Carteira</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                                errorsForm["errors"].carteira[0]
                            }}</span>
                        </div>
                        <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
                            <button @click="{ formStoreExpense = !formStoreExpense }; clearInputs()"
                                class="btn btn-danger px-5">
                                Cancelar
                            </button>
                            <button @click.prevent="salvarLancamentos" class="btn btn-light px-5">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- ================= fim formulario lançamentos despesas==================== -->
        <!-- ========================================================================= -->


        <!-- ========================================================================= -->
        <!-- ================== inicio formulario editar despesa =================== -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="formEditExpense">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <form class="form" @submit.prevent="saveEditedExpense">
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="expenseEdit.valor" class="input" id="valor" name="valor" type="number"
                                required />
                            <label class="label" for="valor">Valor</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].valor" class="span-error">{{
                                errorsForm["errors"].valor[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="expenseEdit.date" type="date" name="date" class="input" id="date" required />
                            <label for="date" class="label">Data</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].date" class="span-error">{{
                                errorsForm["errors"].date[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="expenseEdit.descricao" type="text" name="descricao" class="input" id="descricao"
                                required />
                            <label for="descricao" class="label">Descricao</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                                errorsForm["errors"].descricao[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="expenseEdit.status" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option class="options" value="PAGA"> PAGA </option>
                                <option class="options" value="AGUARDANDO"> AGUARDANDO </option>
                            </select>
                            <label for="categoria" class="label">Status</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                                errorsForm["errors"].descricao[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="expenseEdit.categoria" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option v-for="categoria in categorias" class="options" :value="categoria">{{
                                    categoria
                                }}
                                </option>
                            </select>
                            <label for="categoria" class="label">Categoria</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].categoria" class="span-error">{{
                                errorsForm["errors"].categoria[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="expenseEdit.carteira" class="input" name="carteira"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option v-for="carteira in carteiras" class="options" :value="carteira">{{ carteira
                                }}
                                </option>
                            </select>
                            <label for="carteira" class="label">Carteira</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                                errorsForm["errors"].carteira[0]
                            }}</span>
                        </div>
                        <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
                            <button @click="formEditExpense = !formEditExpense" class="btn btn-danger px-5">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-light px-5">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- =================== fim formulario editar despesa ===================== -->
        <!-- ========================================================================= -->




        <div class="container-fluid" v-if="!formStoreExpense & !formEditExpense">
            <div class="card__container">
                <Card class="card" titulo="Despesas" :valor="valueTotalExpensesMonth" />
                <Card class="card" titulo="pendentes" :valor="valuePending" />
                <Card class="card" titulo="pagas" :valor="valuePay" />
            </div>
            <div class="container__table">
                <div v-if="expensesMonth" class="col-12 col-lg-12">
                    <div class="row justify-content-center card-header mx-0 py-1"
                        style="background-color: rgba(0, 0, 0, 0.25)">
                        <div class="d-flex text-center col-2">
                            <button class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-left">
                                <mdicon class="mdicon" name="chevron-left" />
                            </button>
                            <p class="m-0 pt-1 border rounded-pill w-75 text-white">mes</p>
                            <button class="btn btn-outline-table text-white p-0 fs-5 bi bi-caret-right">
                                <mdicon class="mdicon" name="chevron-right" />
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">

                        <table class="table" style="background-color: rgba(0, 0, 0, 0.25); color: black;">
                            <thead>
                                <tr>
                                    <th class="text-white text-center">Data</th>
                                    <th class="text-white text-center">Descrição</th>
                                    <th class="text-white text-center">Categoria</th>
                                    <th class="text-white text-center">Carteira</th>
                                    <th class="text-white text-center">Valor</th>
                                    <th class="text-white text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expense, key) in expensesMonth" :key="expense.id">
                                    <td class="text-white text-center">{{ expense.date }}</td>
                                    <td class="text-white text-center">{{ expense.descricao }}</td>
                                    <td class="text-white text-center">{{ expense.categoria }}</td>
                                    <td class="text-white text-center">{{ expense.carteira }}</td>
                                    <td class="text-white text-center">R$ {{ expense.valor }}</td>
                                    <td class="d-flex py-0 justify-content-center">
                                        <button @click="payExpense(expense)"
                                            class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 text-white"
                                            :class="{ pay: expense.status === 'PAGA' }"
                                            :disabled="expense.status === 'PAGA'">
                                            <mdicon name="check-circle-outline" />
                                        </button>
                                        <!-- <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip mx-2"
                                            title="anexar arquivo">
                                            <mdicon name="paperclip" />
                                        </button> -->
                                        <button @click="displayFormEditExpense(expense)"
                                            class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
                                            title="editar">
                                            <mdicon name="pencil-outline" />
                                        </button>
                                        <button @click="deletar(expense.id)" type="submit"
                                            class="btn btn-outline-table p-0 text-white fs-4 bi bi-trash3 mx-2"
                                            title="deletar">
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
                    </div>
                </div>
                <h5 v-else class="card-title text-white text-center">
                    Você não possui despesas a serem exibidas
                </h5>
            </div>
        </div>
        <div class="overlay toggle-menu"></div>
    </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";

import { ref, reactive } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useExpensesStore } from "@/stores/expenses";
import { useUserStore } from "@/stores/user";
import { userData } from "@/stores/data";
import { useRouter } from "vue-router";
import http from "@/services/http";

const useExpenses = useExpensesStore();
const userStore = useUserStore();
const router = useRouter();
const data = userData();

let errorsForm = reactive({ errors: {} });
let valueTotalExpensesMonth = ref();
let formStoreExpense = ref(false);
let formEditExpense = ref(false);
let expensesMonth = reactive({});
let expenseEdit = reactive({});
let categorias = reactive({});
let carteiras = reactive({});
let valuePending = ref();
let valuePay = ref();
let releases = reactive({
    valor: '',
    date: '',
    status: '',
    descricao: '',
    categoria: '',
    carteira: '',
});
let status = ref(true);

categorias = userStore.user.categoriasDespesas;
valueTotalExpensesMonth.value = useExpenses.expensesData.valueTotalExpensesMonth;
valuePending.value = useExpenses.expensesData.valuePendingExpenses;
expensesMonth = useExpenses.expensesData.expensesMonth;
valuePay.value = useExpenses.expensesData.valuePayExpenses;
carteiras = userStore.user.carteiras;

const clearInputs = () => {
    releases.valor = '';
    releases.date = '';
    releases.descricao = '';
    releases.categoria = '';
    releases.carteira = '';
}

const returnExpense = () => {
    formStoreExpense.value = formStoreExpense.value === true ? !formStoreExpense.value : formStoreExpense.value;
    formEditExpense.value = formEditExpense.value === true ? !formEditExpense.value : formEditExpense.value;
}

const salvarLancamentos = async () => {
    try {
        releases.status = status.value ? "PAGA" : "AGUARDANDO";
        const res = await http.post("/save-expense", releases);
        useExpenses.setExpensesData(res.data.expenses, res.data.valueTotalExpensesMonth, res.data.valuePay, res.data.valuePending, res.data.expensesMonth);
        valueTotalExpensesMonth.value = res.data.valueTotalExpensesMonth;
        valuePay.value = res.data.valuePay;
        valuePending.value = res.data.valuePending;
        expensesMonth = res.data.expensesMonth;
        clearInputs();
        formStoreExpense.value = false;
    } catch (error) {
        console.log(error);
        errorsForm["errors"] = error.response.data.errors;
    }
}

const payExpense = async (expense: Lancamentos) => {
    try {
        const res = await http.post('/pay-expense', { 'id': expense.id });
        useExpenses.setExpensesData(res.data.expenses, res.data.valueTotalExpensesMonth, res.data.valuePay, res.data.valuePending, res.data.expensesMonth);
        valuePending.value = res.data.valuePendig;
        valuePay.value = res.data.valuePay;
        // expense.status = 'PAGA';
        expensesMonth.forEach(expenses => {
            if (expenses.id === expense.id) {
                expense.status = "PAGA";
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function displayFormEditExpense(expense: Lancamentos) {
    expenseEdit = expense;
    formEditExpense.value = true;
}

const saveEditedExpense = async () => {
    try {
        const res = await http.post("/edit-expense", expenseEdit);
        useExpenses.setExpensesData(res.data.expenses, res.data.valueTotalExpensesMonth, res.data.valuePay, res.data.valuePending, res.data.expensesMonth);
        valueTotalExpensesMonth.value = res.data.valueTotalExpensesMonth;
        valuePending.value = res.data.valuePending;
        expensesMonth = res.data.expensesMonth
        valuePay.value = res.data.valuePay;
    } catch (error) {
        console.log(error);
    }

    formEditExpense.value = false;

};

const deletar = async (id: number) => {
    try {
        const res = await http.post('/delete-expense', { 'id': id });
        useExpenses.setExpensesData(res.data.expenses, res.data.valueTotalExpensesMonth, res.data.valuePay, res.data.valuePending, res.data.expensesMonth);
        valueTotalExpensesMonth.value = res.data.valueTotalExpensesMonth;
        valuePending.value = res.data.valuePending;
        expensesMonth = res.data.expensesMonth;
        valuePay.value = res.data.valuePay;
    } catch (error) {
        console.log(error);
    }
}

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

.input {
    color: #ccc;
    width: 300px;
    height: 40px;
    background-color: transparent;
    border: 0;
    outline: 0;
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
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    display: flex;
}

.card {
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
</style>