<template>
  <div class="content-wrapper">
    <div class="pagetitle">
      <nav class="d-flex justify-content-between mb-3">
        <ol class="breadcrumb bg-transparent ">
          <li class="breadcrumb-item text-white">
            <router-link
              class="link"
              :to="{ name: 'dashboard' }"
            >
              Dashboard
            </router-link>
          </li>
          <li
            :class="{ opaco: !formStoreRevenue & !formEditRevenue }"
            class="breadcrumb-item text-white"
            @click="returnRevenue"
          >
            receitas
          </li>
          <li
            v-if="formStoreRevenue"
            :class="{ opaco: formStoreRevenue }"
            class="breadcrumb-item"
          >
            cadastrar de receita
          </li>
          <li
            v-if="formEditRevenue"
            :class="{ opaco: formEditRevenue }"
            class="breadcrumb-item"
          >
            editar de receita
          </li>
        </ol>
        <button
          v-if="!formStoreRevenue && !formEditRevenue"
          class="btn btn-danger text-whit"
          @click="formStoreRevenue = !formStoreRevenue"
        >
          nova receita
        </button>
      </nav>
    </div>

    <!-- ========================================================================= -->
    <!-- ================ inicio formulario lançamentos receitas ================= -->
    <!-- ========================================================================= -->
    <div
      v-if="formStoreRevenue"
      class="container-fluid"
    >
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <form class="form">
            <div class="inputSimples">
              <input
                id="valor"
                v-model="releases.valor"
                class="input"
                autocomplete="off"
                name="valor"
                type="tel"
                required
              >
              <label
                class="label"
                for="valor"
              >Valor</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].valor"
                class="span-error"
              >{{
                errorsForm["errors"].valor[0]
              }}</span>
            </div>
            <div class="form-check form-switch text-white">
              <input
                id="flexSwitchCheckChecked"
                v-model="status"
                class="form-check-input"
                type="checkbox"
                checked
              >
              <label
                class="form-check-label"
                for="flexSwitchCheckChecked"
              >Recebida</label>
            </div>
            <div class="inputSimples">
              <input
                id="date"
                v-model="releases.date"
                type="text"
                onfocus="this.type='date'"
                onblur="this.type='text'"
                name="date"
                class="input"
                required
              >
              <label
                for="date"
                class="label"
              >Data</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].date"
                class="span-error"
              >{{
                errorsForm["errors"].date[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <input
                id="descricao"
                v-model="releases.descricao"
                type="text"
                name="descricao"
                autocomplete="off"
                class="input"
                required
              >
              <label
                for="descricao"
                class="label"
              >Descricao</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].descricao"
                class="span-error"
              >{{
                errorsForm["errors"].descricao[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="releases.categoria"
                class="input"
                name="categoria"
                aria-label="Default select example"
                required
              >
                <option
                  class="options"
                  selected
                />
                <option
                  v-for="categoria in categorias"
                  class="options"
                  :value="categoria.name"
                >
                  {{
                    categoria.name }}
                </option>
              </select>
              <label
                for="categoria"
                class="label"
              >Categoria</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].categoria"
                class="span-error"
              >{{
                errorsForm["errors"].categoria[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="releases.carteira"
                class="input"
                name="carteira"
                aria-label="Default select example"
                required
              >
                <option
                  class="options"
                  selected
                />
                <option
                  v-for="carteira in carteiras"
                  class="options"
                  :value="carteira"
                >
                  {{ carteira }}
                </option>
              </select>
              <label
                for="carteira"
                class="label"
              >Carteira</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].carteira"
                class="span-error"
              >{{
                errorsForm["errors"].carteira[0]
              }}</span>
            </div>
            <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
              <button
                class="btn btn-danger px-5"
                @click="{ formStoreRevenue = !formStoreRevenue }; clearInputs()"
              >
                Cancelar
              </button>
              <button
                class="btn btn-light px-5"
                @click.prevent="salvarLancamentos"
              >
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
    <div
      v-if="formEditRevenue"
      class="container-fluid"
      style="padding: 0 !important;"
    >
      <div class="container d-flex justify-content-center">
        <div class="cadastro">
          <form
            class="form"
            @submit.prevent="saveEditedRevenue"
          >
            <div class="inputSimples">
              <input
                id="valor"
                v-model="revenueEdit.valor"
                class="input"
                name="valor"
                type="number"
                required
              >
              <label
                class="label"
                for="valor"
              >Valor</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].valor"
                class="span-error"
              >{{
                errorsForm["errors"].valor[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <input
                id="date"
                v-model="revenueEdit.date"
                type="date"
                name="date"
                class="input"
                required
              >
              <label
                for="date"
                class="label"
              >Data</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].date"
                class="span-error"
              >{{
                errorsForm["errors"].date[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <input
                id="descricao"
                v-model="revenueEdit.descricao"
                type="text"
                name="descricao"
                class="input"
                required
              >
              <label
                for="descricao"
                class="label"
              >Descricao</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].descricao"
                class="span-error"
              >{{
                errorsForm["errors"].descricao[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="revenueEdit.status"
                class="input"
                name="categoria"
                aria-label="Default select example"
                required
              >
                <option
                  class="options"
                  selected
                />
                <option
                  class="options"
                  value="RECEBIDA"
                >
                  RECEBIDA
                </option>
                <option
                  class="options"
                  value="AGUARDANDO"
                >
                  AGUARDANDO
                </option>
              </select>
              <label
                for="categoria"
                class="label"
              >Status</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].date"
                class="span-error"
              >{{
                errorsForm["errors"].date[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="revenueEdit.categoria"
                class="input"
                name="categoria"
                aria-label="Default select example"
                required
              >
                <option
                  class="options"
                  selected
                />
                <option
                  v-for="categoria in categorias"
                  class="options"
                  :value="categoria.name"
                >
                  {{ categoria.name }}
                </option>
              </select>
              <label
                for="categoria"
                class="label"
              >Categoria</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].categoria"
                class="span-error"
              >{{
                errorsForm["errors"].categoria[0]
              }}</span>
            </div>
            <div class="inputSimples">
              <select
                v-model="revenueEdit.carteira"
                class="input"
                name="carteira"
                aria-label="Default select example"
                required
              >
                <option
                  class="options"
                  selected
                />
                <option
                  v-for="carteira in carteiras"
                  class="options"
                  :value="carteira"
                >
                  {{ carteira }}
                </option>
              </select>
              <label
                for="carteira"
                class="label"
              >Carteira</label>
            </div>
            <div class="error">
              <span
                v-if="errorsForm['errors'].carteira"
                class="span-error"
              >{{
                errorsForm["errors"].carteira[0]
              }}</span>
            </div>
            <div class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4">
              <button
                class="btn btn-danger px-5"
                @click="formEditRevenue = !formEditRevenue"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="btn btn-light px-5"
              >
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




    <div
      v-if="!formStoreRevenue && !formEditRevenue"
      class="container-fluid"
    >
      <div class="card__container">
        <Card
          class="card"
          titulo="Receitas"
          :valor="valueTotalRevenuesMonth"
        />
        <Card
          class="card"
          titulo="Pendentes"
          :valor="valuePending"
        />
        <Card
          class="card"
          titulo="Recebidas"
          :valor="valueReceived"
        />
      </div>

      <div class="container__table">
        <div
          v-if="revenuesMonth"
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
            <!-- <table class="table align-items-center table-flush table-borderless" -->
            <table
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
                    Conta
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
                  v-for="(revenue, key) in revenuesMonth"
                  :key="revenue.id"
                >
                  <td class="text-white text-center">
                    {{ revenue.date }}
                  </td>
                  <td class="text-white text-center">
                    {{ revenue.descricao }}
                  </td>
                  <td class="text-white text-center">
                    {{ revenue.categoria }}
                  </td>
                  <td class="text-white text-center">
                    {{ revenue.carteira }}
                  </td>
                  <td class="text-white text-center">
                    R$ {{ revenue.valor }}
                  </td>
                  <td class="d-flex py-0 justify-content-center">
                    <button
                      style="color: #fefefe;"
                      class="btn btn-outline-table p-0 fs-4 bi bi-check2-circle border-0 mx-2 "
                      :class="{ received: revenue.status === 'RECEBIDA' }"
                      :disabled="revenue.status === 'RECEBIDA'"
                      @click="receivedRevenue(revenue)"
                    >
                      <mdicon name="check-circle-outline" />
                    </button>
                    <!-- <button class="btn btn-outline-table p-0 text-white fs-4 bi bi-paperclip mx-2"
                                            title="anexar arquivo">
                                            <mdicon name="paperclip" />
                                        </button> -->
                    <button
                      class="btn btn-outline-table p-0 text-white fs-4 bi bi-pencil mx-2"
                      title="editar"
                      @click="displayFormEditRevenue(revenue)"
                    >
                      <mdicon name="pencil-outline" />
                    </button>
                    <button
                      type="submit"
                      class="btn btn-outline-table p-0 text-white fs-4 bi bi-trash3 mx-2"
                      title="deletar"
                      @click="deletar(revenue.id)"
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
          </div>
        </div>
        <h5
          v-else
          class="card-title text-white text-center"
        >
          Você não possui receitas a serem exibidas
        </h5>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";

import { ref, reactive } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useRevenuesStore } from "@/store/revenues";
import { useUserStore } from "@/store/user";
import { userData } from "@/store/data";
import http from "@/services/http";

const useRevenues = useRevenuesStore();
const data = userData();
const userStore = useUserStore();

let valueTotalRevenuesMonth = ref(useRevenues.revenuesData.revenues.valueTotalRevenuesMonth);
let valuePending = ref(useRevenues.revenuesData.revenues.valuePendingRevenues);
let revenuesMonth = ref(useRevenues.revenuesData.revenues.revenuesMonth);
let valueReceived = ref(useRevenues.revenuesData.revenues.valueReceived);
let categorias = ref(userStore.user.categoriasReceitas);
let carteiras = ref(userStore.user.carteiras);
let errorsForm = ref({ errors: {} });
let formStoreRevenue = ref(false);
let formEditRevenue = ref(false);
let revenueEdit = reactive({});
let releases = reactive({
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: ""
});
let status = ref(true);


const clearInputs = () => {
    releases.valor = "";
    releases.date = "";
    releases.descricao = "";
    releases.categoria = "";
    releases.carteira = "";
};

const returnRevenue = () => {
    formStoreRevenue.value = formStoreRevenue.value === true ? !formStoreRevenue.value : formStoreRevenue.value;
    formEditRevenue.value = formEditRevenue.value === true ? !formEditRevenue.value : formEditRevenue.value;
};

const salvarLancamentos = async () => {
    try {
        releases.status = status.value ? "RECEBIDA" : "AGUARDANDO";
        const res = await http.post("/save-revenue", releases);
        useRevenues.setRevenuesData(res.data.revenuesData,);
        valueTotalRevenuesMonth.value = res.data.revenuesData.valueTotalRevenuesMonth;
        valuePending.value = res.data.revenuesData.valuePendingRevenues;
        valueReceived.value = res.data.revenuesData.valueReceivedRevenues;
        revenuesMonth.value = res.data.revenuesData.revenuesMonth;
        clearInputs();
        formStoreRevenue.value = false;
    } catch (error) {
        console.log(error.response.data.errors);
        errorsForm.value["errors"] = error.response.data["errors"];
    }
};

const receivedRevenue = async (revenue: Lancamentos) => {
    try {
        const res = await http.post("/received-revenue", { "id": revenue.id });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueReceived.value = res.data.revenuesData.valueReceivedRevenues;
        valuePending.value = res.data.revenuesData.valuePendingRevenues;
        // revenue.status = 'PAGA';
        revenuesMonth.value.forEach(revenues => {
            if (revenues.id === revenue.id) {
                revenues.status = "RECEBIDA";
            }
        });

    } catch (error) {
        console.log(error);
    }
};

function displayFormEditRevenue(revenue: Lancamentos) {
    revenueEdit = revenue;
    formEditRevenue.value = true;
}

const saveEditedRevenue = async () => {
    try {
        const res = await http.post("/edit-revenue", revenueEdit);
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value = res.data.revenuesData.valueTotalRevenuesMonth;
        valueReceived.value = res.data.revenuesData.valueReceivedRevenues;
        valuePending.value = res.data.revenuesData.valuePendingRevenues;
        revenuesMonth.value = res.data.revenuesData.revenuesMonth;
    } catch (error) {
        console.log(error);
    }

    formEditRevenue.value = false;

};

const deletar = async (id: number) => {
    try {
        const res = await http.post("/delete-revenue", { "id": id });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value = res.data.revenuesData.valueTotalRevenuesMonth;
        valuePending.value = res.data.revenuesData.valuePendingRevenues;
        valueReceived.value = res.data.revenuesData.valueReceivedRevenues;
        revenuesMonth.value = res.data.revenuesData.revenuesMonth;
    } catch (error) {
        console.log(error);
    }
};

</script>

<style>
.opaco {
    color: #6c757d !important;
}

.link {
    text-decoration: none;
    color: #fefefe;
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

.input:internal-autofill-selected {
    background-color: transparent;
}

.label {
    color: #ccc;
    position: absolute;
    left: 10px;
    top: -25px;
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

.received {
    color: #1dbb01 !important;
}

.container__table {
    background-color: rgba(0, 0, 0, 0.1);
}

.table {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>