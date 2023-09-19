<template>
    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="pagetitle">
            <nav class="d-flex justify-content-between mb-3">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item text-white">
                        Dashboard
                    </li>
                    <li :class="{ opaco: !formStoreRevenue & !formEditRevenue }" @click="returnRevenue"
                        class="breadcrumb-item text-white">
                        receitas
                    </li>
                    <li :class="{ opaco: formStoreRevenue }" class="breadcrumb-item" v-if="formStoreRevenue">
                        cadastrar de receita
                    </li>
                    <li :class="{ opaco: formEditRevenue }" class="breadcrumb-item" v-if="formEditRevenue">
                        editar de receita
                    </li>
                </ol>
                <button v-if="!formStoreRevenue & !formEditRevenue" class="btn btn-danger text-whit"
                    @click="formStoreRevenue = !formStoreRevenue">
                    nova receita
                </button>
            </nav>
        </div>

        <!-- ========================================================================= -->
        <!-- ================ inicio formulario lançamentos receitas ================= -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="formStoreRevenue">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <!-- <form class="form" @submit.prevent="salvarLancamentos"> -->
                    <form class="form">
                        <div class="inputSimples">
                            <!-- <mdicon class="mdicon" name="account" /> -->
                            <input v-model="releases.valor" class="input" id="valor" autocomplete="off" name="valor"
                                type="number" required />
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
                            <label class="form-check-label" for="flexSwitchCheckChecked">Recebida</label>
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
                            <input v-model="releases.descricao" type="text" name="descricao" autocomplete="off"
                                class="input" id="descricao" required />
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
                                <option class="options" selected></option>
                                <option v-for="categoria in categorias" class="options" :value="categoria">{{ categoria }}
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
                                <option class="options" selected></option>
                                <option v-for="carteira in carteiras" class="options" :value="carteira">{{ carteira }}
                                </option>

                            </select>
                            <label for="carteira" class="label">Carteira</label>
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                                errorsForm["errors"].carteira[0]
                            }}</span>
                        </div>
                        <div class="form-group m-0 container d-flex justify-content-around col-12 pb-3">
                            <button @click="{ formStoreRevenue = !formStoreRevenue }; clearInputs()"
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
        <!-- ================= fim formulario lançamentos receitas =================== -->
        <!-- ========================================================================= -->


        <!-- ========================================================================= -->
        <!-- =================== inicio formulario editar receita= =================== -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="formEditRevenue">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <form class="form" @submit.prevent="saveEditedRevenue">
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="revenueEdit.valor" class="input" id="valor" name="valor" type="number"
                                required />
                            <!-- <label class="label" for="valor">Valor</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].valor" class="span-error">{{
                                errorsForm["errors"].valor[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="revenueEdit.date" type="date" name="date" class="input" id="date" required />
                            <!-- <label for="date" class="label">Data</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].date" class="span-error">{{
                                errorsForm["errors"].date[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="revenueEdit.descricao" type="text" name="descricao" class="input" id="descricao"
                                required />
                            <!-- <label for="descricao" class="label">Descricao</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                                errorsForm["errors"].descricao[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="revenueEdit.status" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option class="options" value="RECEBIDA"> RECEBIDA </option>
                                <option class="options" value="AGUARDANDO"> AGUARDANDO </option>
                            </select>
                            <label for="categoria" class="label">Status</label>
                        </div>
                        <div class="inputSimples">
                            <select v-model="revenueEdit.categoria" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option v-for="categoria in categorias" class="options" :value="categoria">{{ categoria }}
                                </option>
                            </select>
                            <!-- <label for="categoria" class="label">Categoria</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].categoria" class="span-error">{{
                                errorsForm["errors"].categoria[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="revenueEdit.carteira" class="input" name="carteira"
                                aria-label="Default select example" required>
                                <option class="options" selected></option>
                                <option v-for="carteira in carteiras" class="options" :value="carteira">{{ carteira }}
                                </option>
                            </select>
                            <!-- <label for="carteira" class="label">Carteira</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                                errorsForm["errors"].carteira[0]
                            }}</span>
                        </div>
                        <div class="form-group m-0 container d-flex justify-content-around col-12 pb-3">
                            <button @click="formEditRevenue = !formEditRevenue" class="btn btn-danger px-5">
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
        <!-- ==================== fim formulario editar receita ====================== -->
        <!-- ========================================================================= -->




        <div class="container-fluid" v-if="!formStoreRevenue & !formEditRevenue">
            <div class="row justify-content-between m-0 mb-4">
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Receitas"
                    :valor="valueTotalRevenuesMonth" />
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Pendentes"
                    :valor="valuePending" />
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Recebidas"
                    :valor="valueReceived" />
            </div>

            <div class="row">
                <div v-if="revenuesMonth.length >= 1" class="col-12 col-lg-12">
                    <div class="row justify-content-center card-header mx-0 py-1"
                        style="background-color: rgba(0, 0, 0, 0.25)">
                        <!-- <div class="row align-items-center col-5 ms-2">Despesas</div> -->
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

                        <!-- <table class="table align-items-center table-flush table-borderless" -->
                        <table class="table" style="background-color: rgba(0, 0, 0, 0.25); color: black;">
                            <thead>
                                <tr>
                                    <th style="color: #fefefe;">Data</th>
                                    <th style="color: #fefefe;">Descrição</th>
                                    <th style="color: #fefefe;">Categoria</th>
                                    <th style="color: #fefefe;">Conta</th>
                                    <th style="color: #fefefe;">Valor</th>
                                    <th style="color: #fefefe;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(revenue, key) in revenuesMonth" :key="revenue.id">
                                    <td style="color: #fefefe;">{{ revenue.date }}</td>
                                    <td style="color: #fefefe;">{{ revenue.descricao }}</td>
                                    <td style="color: #fefefe;">{{ revenue.categoria }}</td>
                                    <td style="color: #fefefe;">{{ revenue.carteira }}</td>
                                    <td style="color: #fefefe;">R$ {{ revenue.valor }}</td>
                                    <td class="d-flex py-0">
                                        <button @click="receivedRevenue(revenue)" style="color: #fefefe;"
                                            class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 "
                                            :class="{ received: revenue.status === 'RECEBIDA' }"
                                            :disabled="revenue.status === 'RECEBIDA'">
                                            <mdicon name="check-circle-outline" />
                                        </button>
                                        <!-- <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip mx-2"
                                            title="anexar arquivo">
                                            <mdicon name="paperclip" />
                                        </button> -->
                                        <button @click="displayFormEditRevenue(revenue)"
                                            class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
                                            title="editar">
                                            <mdicon name="pencil-outline" />
                                        </button>
                                        <button @click="deletar(revenue.id)" type="submit"
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

<script setup>
import { ref, reactive } from "vue";
import { userData } from "@/stores/data.js";
import Card from "@/components/Card.vue";
import { useRouter } from "vue-router";
import http from "@/services/http.js";

const router = useRouter();
const data = userData();

let formStoreRevenue = ref(false);
let formEditRevenue = ref(false);
let categorias = reactive({});
let carteiras = reactive({});
let revenuesMonth = reactive({});
let revenueEdit = reactive({});
let valueReceived = ref(null);
let valuePending = ref(null);
let valueTotalRevenuesMonth = ref(null);
let releases = reactive({
    valor: null,
    date: null,
    descricao: null,
    categoria: null,
    carteira: null,
});
let status = ref(true);

categorias = data.user.categoriasReceitas;
valueTotalRevenuesMonth.value = data.valueTotalRevenuesMonth;
carteiras = data.user.carteiras;
revenuesMonth = data.revenuesMonth;
valueReceived = data.valueReceivedRevenues;
valuePending.value = data.valuePendingRevenues;

const errorsForm = reactive({ errors: {} });

const clearInputs = () => {
    releases.valor = null;
    releases.date = null;
    releases.descricao = null;
    releases.categoria = null;
    releases.carteira = null;
}

const returnRevenue = () => {
    formStoreRevenue.value = formStoreRevenue.value === true ? !formStoreRevenue.value : formStoreRevenue.value;
    formEditRevenue.value = formEditRevenue.value === true ? !formEditRevenue.value : formEditRevenue.value;
}

const salvarLancamentos = async () => {
    console.log(releases);
    try {
        releases.status = status.value ? "RECEBIDA" : "AGUARDANDO";
        const res = await http.post("/save-revenue", releases);
        valueTotalRevenuesMonth = res.data.valueTotalRevenuesMonth;
        data.setValueTotalRevenuesMonth(res.data.valueTotalRevenuesMonth);
        valueReceived = res.data.valueReceived;
        data.setValueReceivedRevenues(res.data.valueReceived);
        valuePending = res.data.valuePending;
        data.setValuePendingRevenues(res.data.valuePending);
        revenuesMonth = res.data.revenues;
        data.setRevenuesMonth(res.data.revenues);
        clearInputs();
        formStoreRevenue.value = false;
    } catch (error) {
        console.log(error);
    }
}

const receivedRevenue = async (revenue) => {
    try {
        const res = await http.post('/received-revenue', { 'id': revenue.id });
        valuePending = res.data.valuePendig;
        data.setValuePendingRevenues(res.data.valuePending);
        valueReceived = res.data.valueReceived;
        data.setValueReceivedRevenues(res.data.valueReceived);
        // revenue.status = 'PAGA';
        revenuesMonth.forEach(revenues => {
            if (revenues.id === revenue.id) {
                revenues.status = "RECEBIDA";
            }
        });

    } catch (error) {
        console.log(error);
    }
}

function displayFormEditRevenue(revenue) {
    revenueEdit = revenue;
    formEditRevenue.value = true;
}

const saveEditedRevenue = async () => {
    try {
        const res = await http.post("/edit-revenue", revenueEdit);
        valuePending.value = res.data.valuePending;
        data.setValuePendingRevenues(res.data.valuePending);
        valueReceived = res.data.valueReceived;
        data.setValueReceivedRevenues(res.data.valueReceived);
        expensesMonth = res.data.expensesMonth
        data.setRevenuesMonth(res.data.revenuesMonth);
    } catch (error) {
        console.log(error);
    }

    formEditRevenue.value = false;

};

const deletar = async (id) => {
    try {
        const res = await http.post('/delete-revenue', { 'id': id })
        valueTotalRevenuesMonth = res.data.valueTotalRevenuesMonth;
        data.setValueTotalRevenuesMonth(res.data.valueTotalRevenuesMonth);
        valuePending = res.data.valuePending;
        data.setValuePendingExpenses(res.data.valuePending);
        valueReceived = res.data.valueReceived;
        data.setValueReceivedRevenues(res.data.valueReceived);
        revenuesMonth = res.data.revenuesMonth;
        data.setRevenuesMonth(res.data.revenuesMonth);
    } catch (error) {
        console.log(error);
    }
}

</script>

<style>
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
    transform: translateY(-35px);
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

.card {
    height: 140px;
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    color: #ccc;
    font-size: 30px;
}

.btn {
    height: 40px;
    /* margin-top: 15px; */
}

.received {
    color: #1dbb01 !important;
}
</style>