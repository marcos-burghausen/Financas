<template>
    <Cabecalho :name="name" />
    <div class="clearfix"></div>

    <div class="content-wrapper">
        <div class="pagetitle">
            <nav class="d-flex justify-content-between mb-3">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item">
                        <router-link :to="{ name: 'dashboard' }" class="text-white">Dashboard</router-link>
                    </li>
                    <li :class="{ opaco: !cadastrarDespesa }" class="breadcrumb-item text-white"
                        @click="cadastrarDespesa = !cadastrarDespesa">
                        despesas
                    </li>
                    <li :class="{ opaco: cadastrarDespesa }" class="breadcrumb-item" v-if="cadastrarDespesa">
                        cadastro de despesa
                    </li>
                </ol>
                <button v-if="!cadastrarDespesa" class="btn btn-danger text-whit"
                    @click="cadastrarDespesa = !cadastrarDespesa">
                    nova despesa
                </button>
            </nav>
        </div>

        <!-- ========================================================================= -->
        <!-- ================== inicio formulario cadastro despesa =================== -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="cadastrarDespesa">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <form class="form" @submit.prevent="salvarLancamentos">
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
                            <button @click="{ (cadastrarDespesa = !cadastrarDespesa) }; zerarInputs()
                                " class="btn btn-danger px-5">
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
        <!-- =================== fim formulario cadastro despesa ===================== -->
        <!-- ========================================================================= -->


        <!-- ========================================================================= -->
        <!-- ================== inicio formulario editar despesa =================== -->
        <!-- ========================================================================= -->
        <div class="container-fluid" v-if="edit">
            <div class="container d-flex justify-content-center">
                <div class="cadastro">
                    <form class="form" @submit.prevent="editExpense">
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="editForm.valor" class="input" id="valor" name="valor" type="number" required />
                            <!-- <label class="label" for="valor">Valor</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].valor" class="span-error">{{
                                errorsForm["errors"].valor[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="editForm.date" type="date" name="date" class="input" id="date" required />
                            <!-- <label for="date" class="label">Data</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].date" class="span-error">{{
                                errorsForm["errors"].date[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <mdicon class="mdicon" name="account" />
                            <input v-model="editForm.descricao" type="text" name="descricao" class="input" id="descricao"
                                required />
                            <!-- <label for="descricao" class="label">Descricao</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].descricao" class="span-error">{{
                                errorsForm["errors"].descricao[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="editForm.categoria" class="input" name="categoria"
                                aria-label="Default select example" required>
                                <option selected></option>
                                <option v-for="categoria in categorias" value="Carro">{{ categoria }}</option>
                                <option value="casa">casa</option>
                                <option value="Mercado">Mercado</option>
                            </select>
                            <!-- <label for="categoria" class="label">Categoria</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].categoria" class="span-error">{{
                                errorsForm["errors"].categoria[0]
                            }}</span>
                        </div>
                        <div class="inputSimples">
                            <select v-model="editForm.carteira" class="input" name="carteira"
                                aria-label="Default select example" required>
                                <option selected></option>
                                <option v-for="carteira in carteiras" value="Sicredi">{{ carteira }}</option>
                                <!-- <option value="BB">BB</option> -->
                                <!-- <option value="Caixa">Caixa</option> -->
                            </select>
                            <!-- <label for="carteira" class="label">Carteira</label> -->
                        </div>
                        <div class="error">
                            <span v-if="errorsForm['errors'].carteira" class="span-error">{{
                                errorsForm["errors"].carteira[0]
                            }}</span>
                        </div>
                        <div class="form-group m-0 container d-flex justify-content-around col-12 pb-3">
                            <button @click="{ (edit = !edit) }; zerarInputs()
                                " class="btn btn-danger px-5">
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




        <div class="container-fluid" v-if="!cadastrarDespesa">
            <div class="row justify-content-between m-0 mb-4">
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Despesas"
                    :valor="totalExpenses" />
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Despesas pendentes"
                    :valor="totalExpenses" />
                <Card class="card col-12 col-md-6 col-lg-6 col-xl-3 bg-transparent" titulo="Despesas pagas"
                    :valor="totalExpenses" />
            </div>

            <div class="row">
                <div v-if="expenses.length >= 1" class="col-12 col-lg-12">
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
                                <tr v-for="(expense, key) in expenses" :key="expense.id">
                                    <td style="color: #fefefe;">{{ expense.date }}</td>
                                    <td style="color: #fefefe;">{{ expense.descricao }}</td>
                                    <td style="color: #fefefe;">{{ expense.categoria }}</td>
                                    <td style="color: #fefefe;">{{ expense.carteira }}</td>
                                    <td style="color: #fefefe;">R$ {{ expense.valor }}</td>
                                    <td class="d-flex py-0">
                                        <button style="color: #fefefe;"
                                            class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 me-1 ">
                                            <mdicon name="check-circle-outline" />
                                        </button>
                                        <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip me-1"
                                            title="anexar arquivo">
                                            <mdicon name="paperclip" />
                                        </button>
                                        <button @click="edit = !edit, formEdit(expense)"
                                            class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil me-1"
                                            title="editar">
                                            <mdicon name="pencil-outline" />
                                        </button>
                                        <button @click="deletar(expense.id)" type="submit"
                                            class="btn btn-outline-table p-0 text-white fs-4 bi bi-trash3 me-1"
                                            title="deletar">
                                            <mdicon name="trash-can-outline" />
                                        </button>
                                        <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-three-dots-vertical"
                                            title="mais opções">
                                            <mdicon name="dots-vertical" />
                                        </button>
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
import Cabecalho from "@/components/Cabecalho.vue";
import { ref, reactive } from "vue";
import { userData } from "@/stores/data.js";
import Card from "@/components/Card.vue";
import { useRouter } from "vue-router";
import http from "@/services/http.js";

const router = useRouter();
const data = userData();

let cadastrarDespesa = ref(false);
let edit = ref(false);
let categorias = reactive({});
let carteiras = reactive({});
let expenses = reactive({});
let totalExpenses = ref('');
let editForm = reactive({});
// let editedReleases = reactive({
//     Valor: null,
//     date: null,
//     descricao: null,
//     categoria: null,
//     carteira: null,
// });
let releases = reactive({
    valor: null,
    date: null,
    descricao: null,
    categoria: null,
    carteira: null,
});
let name = ref('');

categorias = data.user.categoriasDespesas;
totalExpenses = data.getTotalExpenses;
carteiras = data.user.carteiras;
expenses = data.getExpenses;
name = data.user.name;

const errorsForm = reactive({ errors: {} });

const zerarInputs = () => {
    releases.valor = "";
    releases.date = "";
    releases.descricao = "";
    releases.categoria = "";
    releases.carteira = "";
}

async function salvarLancamentos() {
    try {
        const res = await http.post("/save-expense", releases);
        totalExpenses += releases.valor;
        data.addValor(releases.valor);
        expenses.push(res.data.expense);
        zerarInputs();
        cadastrarDespesa = false;
    } catch (error) {
        console.log(error);
    }
}

const deletar = async (id) => {
    try {
        const res = await http.post('/delete-expense', { 'id': id })
        for (let i = 0; i < expenses.length; i++) {
            if (expenses[i].id === id) {
                totalExpenses -= expenses[i].valor;
                data.decrementValor(expenses[i].valor);
                console.log(totalExpenses);
                expenses.splice(i, 1);
            }
        }
    } catch (error) {
        console.log(error);
    }
}

const formEdit = (expense) => {
    editForm = expense;
    // edit = true;
    console.log(releases);
}

const editExpense = async (releases) => {
    editForm = 1223356;
    console.log(releases);
    const res = await http.post("/edit-expense", editForm);
    console.log(res);

};

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
</style>