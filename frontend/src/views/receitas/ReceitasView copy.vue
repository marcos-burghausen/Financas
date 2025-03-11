<template>
  <v-card
    class="mx-auto"
    max-width="448"
  >
    <v-layout>
      <v-app-bar
        color="info"
        density="prominent"
      >
        <template #prepend>
          <v-app-bar-nav-icon />
        </template>

        <v-app-bar-title>My Recent Trips</v-app-bar-title>

        <template #append>
          <v-btn icon>
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
      </v-app-bar>

      <v-main>
        <v-container fluid>
          <v-card
            class="mb-2"
            density="compact"
            prepend-avatar="https://randomuser.me/api/portraits/women/10.jpg"
            subtitle="Salsa, merengue, y cumbia"
            title="Cuba"
            variant="text"
            border
          >
            <v-img
              height="128"
              src="https://picsum.photos/512/128?image=660"
              cover
            />

            <v-card-text>
              During my last trip to South America, I spent 2 weeks traveling through Patagonia in Chile.
            </v-card-text>

            <template #actions>
              <v-btn
                color="primary"
                variant="text"
              >
                View More
              </v-btn>

              <v-btn
                color="primary"
                variant="text"
              >
                See in Map
              </v-btn>
            </template>
          </v-card>

          <v-card
            density="comfortable"
            prepend-avatar="https://randomuser.me/api/portraits/women/17.jpg"
            subtitle="Salsa, merengue, y cumbia"
            title="Florida"
            variant="text"
            border
          >
            <v-img
              height="128"
              src="https://picsum.photos/512/128?random"
              cover
            />

            <v-card-text>
              During my last trip to Florida, I spent 2 weeks traveling through the Everglades.
            </v-card-text>

            <template #actions>
              <v-btn
                color="primary"
                variant="text"
              >
                View More
              </v-btn>

              <v-btn
                color="primary"
                variant="text"
              >
                See in Map
              </v-btn>
            </template>
          </v-card>
        </v-container>
      </v-main>
    </v-layout>
  </v-card>
</template>





<script setup lang="ts">
import Card from "@/components/Card.vue";

import { ref, reactive, onMounted, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useRevenuesStore } from "@/store/revenues";
import { useUserStore } from "@/store/user";
import { formatValue } from "@/utils/formatValue";


import http from "@/services/http";
import type { RevenueEdit } from "@/types/revenueEdit";
import { useAuthStore } from "@/store/auth";
import { useWalletsStore } from "@/store/wallets";


const useRevenues = useRevenuesStore();
const userStore = useUserStore();
const useWallets = useWalletsStore();

const useAuth = useAuthStore();

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
let carteiras = ref(useWallets.walletsData.wallets.walletsNames);
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
            revenuesMonth.value[index] = JSON.parse(JSON.stringify(revenueUnedited.value));
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
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value = formatValue(res.data.revenuesData.ValueTotalRevenuesMonth);
        valuePending.value = formatValue(res.data.revenuesData.ValuePendingRevenues);
        valueReceived.value = formatValue(res.data.revenuesData.ValueReceivedRevenues);
        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
        useWallets.setWalletsData(res.data.walletsData);
        clearInputs();
        formStoreRevenue.value = false;
    } catch (error) {
        // console.log(error.response.data.errors);
        errorsForm.value["errors"] = error.response.data["errors"];
    }
};

const receivedRevenue = async (revenue: RevenueEdit) => {
    try {
        const res = await http.post("/received-revenue", { "id": revenue.id, "carteira": revenue.carteira });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
        valuePending.value = res.data.revenuesData.ValuePendingRevenues;
        // revenue.status = 'PAGA';
        revenuesMonth.value.forEach(revenues => {
            if (revenues.id === revenue.id) {
                revenues.status = "RECEBIDA";
            }
        });
        useWallets.setWallets(res.data.walletsData.wallets);

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
        // console.log(error.response.data.message);
        // if (error.response.data.message === "Token has expired") {
        //     alert("sessão expirada");
        // }
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

.received {
    color: #1dbb01 !important;
}

.container__table {
    background-color: rgba(0, 0, 0, 0.1);
}

.table {
    background-color: rgba(0, 0, 0, 0.1);
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