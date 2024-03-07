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
            :class="{ opaco: !formStoreRevenue && !formEditRevenue }"
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
              >Recebida</label>
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
                style="background-color: #dc3545; color: #fefefe;;"
                class=" px-5"
                @click="{ formStoreRevenue = !formStoreRevenue }; clearInputs()"
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
          <v-form
            v-model="validFormEdit"
            class="form"
            @submit.prevent="saveEditedRevenue"
          >
            <v-text-field
              v-model="revenueEdit.valor"
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
              v-model="revenueEdit.date"
              density="compact"
              variant="outlined"
              type="date"
              hide-details="auto"
              label="Data"
              :rules="[rules.requiredData]"
              class="mb-7 input"
            />

            <v-text-field
              v-model="revenueEdit.descricao"
              density="compact"
              variant="outlined"
              type="text"
              hide-details="auto"
              label="Descriçao"
              :rules="[rules.requiredDescricao]"
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="revenueEdit.status"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCatagoria]"
              :items="['RECEBIDA', 'AGUARDANDO']"
              label="Status"
              placeholder="Select..."
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="revenueEdit.categoria"
              density="compact"
              variant="outlined"
              :rules="[rules.requiredCatagoria]"
              :items="categoriasNames"
              label="Categoria"
              placeholder="Select..."
              class="mb-7 input"
            />

            <v-autocomplete
              v-model="revenueEdit.carteira"
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
                @click="revertEdit(); { formEditRevenue = !formEditRevenue }"
              >
                Cancelar
              </v-btn>
              <v-btn
                :disabled="loading || !validFormEdit || release.valor === '0,00'"
                :loading="loading"
                style="background-color: #77d08e;"
                class="btn btn-light px-5"
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
          v-if="revenuesMonth && revenuesMonth.length > 0"
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
                    R$ {{ formatValue(revenue.valor) }}
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

import { ref, reactive, onMounted, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useRevenuesStore } from "@/store/revenues";
import { useUserStore } from "@/store/user";
import { userData } from "@/store/data";
import { formatValue } from "@/utils/formatValue";

import http from "@/services/http";
import type { RevenueEdit } from "@/types/revenueEdit";


const useRevenues = useRevenuesStore();
const userStore = useUserStore();

let validFormLancamentos = ref(false);
let validFormEdit = ref(false);
let loading = ref(false);

let valueTotalRevenuesMonth = ref(formatValue(useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth));
let valuePending = ref(formatValue(useRevenues.revenuesData.revenues?.ValuePendingRevenues));
let revenuesMonth = ref(useRevenues.revenuesData.revenues?.RevenuesMonth);
// console.log(revenuesMonth.value);
let valueReceived = ref(formatValue(useRevenues.revenuesData.revenues?.ValueReceivedRevenues));
// let categorias = ref(userStore.user.categoriasReceitas);
const categoriasNames = ref([]);
userStore.user.categoriasReceitas.forEach((categoria) => {
    categoriasNames.value.push(categoria.name);
});
let carteiras = ref(userStore.user.carteiras);
let errorsForm = ref({ errors: {} });
let formStoreRevenue = ref(false);
let formEditRevenue = ref(false);
let revenueEdit: Ref<RevenueEdit> = ref({
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
const revenueUnedited: Ref<RevenueEdit> = ref({
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: ""
}); 
let release: Ref<Lancamentos> = ref({
    valor: "",
    date: "",
    descricao: "",
    categoria: "",
    carteira: "",
    status: ""
});

// onMounted( () => {
// });
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
    let novoValor = revenueEdit.value.valor.replace(/[^\d]/g, "");

    if (novoValor.length > 1) {
        const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
        const parteDecimal = novoValor.slice(-2);
        const parteInteiraFormatada = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        revenueEdit.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
    } else if (novoValor.length === 1) {
        revenueEdit.value.valor = `0,0${novoValor}`;
    } else {
        revenueEdit.value.valor = "0,00";
    }
};
// formatarValor();

let status = ref(true);

const clearInputs = () => {
    release.value.valor = "";
    release.value.date = "";
    release.value.descricao = "";
    release.value.categoria = "";
    release.value.carteira = "";
};

const revertEdit = () => {
    revenuesMonth.value.forEach((revenue: RevenueEdit, index: number) => {
        if (revenue.id === revenueEdit.value.id) {
            console.log(revenuesMonth.value);
            revenuesMonth.value[index] = JSON.parse(JSON.stringify(revenueUnedited.value));
            console.log(revenuesMonth.value);
        }
    });
};

const returnRevenue = () => {
    formStoreRevenue.value = formStoreRevenue.value === true ? !formStoreRevenue.value : formStoreRevenue.value;
    formEditRevenue.value = formEditRevenue.value === true ? !formEditRevenue.value : formEditRevenue.value;
};

const salvarLancamentos = async () => {
    try {
        release.value.status = status.value ? "RECEBIDA" : "AGUARDANDO";
        const res = await http.post("/save-revenue", release.value);
        useRevenues.setRevenuesData(res.data.revenuesData,);
        valueTotalRevenuesMonth.value = formatValue(res.data.revenuesData.ValueTotalRevenuesMonth);
        valuePending.value = formatValue(res.data.revenuesData.ValuePendingRevenues);
        valueReceived.value = formatValue(res.data.revenuesData.ValueReceivedRevenues);
        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
        clearInputs();
        formStoreRevenue.value = false;
    } catch (error) {
        // console.log(error.response.data.errors);
        errorsForm.value["errors"] = error.response.data["errors"];
    }
};

const receivedRevenue = async (revenue: RevenueEdit) => {
    try {
        const res = await http.post("/received-revenue", { "id": revenue.id });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
        valuePending.value = res.data.revenuesData.ValuePendingRevenues;
        // revenue.status = 'PAGA';
        revenuesMonth.value.forEach(revenues => {
            if (revenues.id === revenue.id) {
                revenues.status = "RECEBIDA";
            }
        });

    } catch (error) {
        // console.log(error);
    }
};

function displayFormEditRevenue(revenue: RevenueEdit) {
    revenueUnedited.value = JSON.parse(JSON.stringify(revenue));
    revenueEdit.value = revenue;
    revenueEdit.value.valor = formatValue(revenueEdit.value.valor); 
    formEditRevenue.value = true;
}

const saveEditedRevenue = async () => {
    try {
        const res = await http.post("/edit-revenue", revenueEdit.value);
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value = formatValue(res.data.revenuesData.ValueTotalRevenuesMonth);
        valueReceived.value = formatValue(res.data.revenuesData.ValueReceivedRevenues);
        valuePending.value = formatValue(res.data.revenuesData.ValuePendingRevenues);
        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
    } catch (error) {
        // console.log(error);
    }

    formEditRevenue.value = false;

};

const deletar = async (id: number) => {
    try {
        const res = await http.post("/delete-revenue", { "id": id });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value = formatValue(res.data.revenuesData.ValueTotalRevenuesMonth);
        valuePending.value = formatValue(res.data.revenuesData.ValuePendingRevenues);
        valueReceived.value = formatValue(res.data.revenuesData.ValueReceivedRevenues);
        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
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

<style>
.opaco {
    color: #6c757d !important;
}

.link {
    text-decoration: none;
    color: #fefefe;
}
.form {
    display: flex;
    flex-direction: column;
    width: 100% !important;
    padding: 10px;
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
  background-color: #1e1e1e !important;
  height: 40px;
    color: #ccc;
    width: 100%;
    border: none;
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