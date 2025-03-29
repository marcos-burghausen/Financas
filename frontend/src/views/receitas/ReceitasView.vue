<template>
  <v-container class="h-100 p-0">
    <FormReceitas
      v-if="formulario"
      :releases="releases"
      :categorias-names="categoriasNames"
      :contas="contas"
      @updateData="updateData"
      @closeForm="formulario = false"
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
            <mdicon
              name="arrow-left"
              size="25"
            />
          </router-link>
          <div class="header__items">
            <div class="d-flex flex-column">
              <span class="fs-5"> Receitas </span>
              <span class="valor">
                R$ {{ formatValue(valueTotalRevenuesMonth) }}
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
      </div>
      <!-- <button
        v-if="!formStoreRevenue && !formEditRevenue"
        class="btn__nova__receita"
        @click="formStoreRevenue = !formStoreRevenue"
      >
        <mdicon name="plus" class="mdicon" size="30" />
      </button> -->

      <div v-if="revenuesMonth && revenuesMonth.length > 0">
        <!-- v-if="!formStoreRevenue && !formEditRevenue" -->
        <div class="container-fluid pt-15 mt-15 pb-8 mb-8">
          <div
            v-for="(revenue, key) in revenuesMonth"
            :key="revenue.id"
            class="container__table"
          >
            <div class="card__lancamento">
              <!-- :class="{ Efetivada: revenue.status === 'Efetivada' }" -->
              <v-card
                color="transparent"
                class="mdicon__card"
                :disabled="revenue.status === 'Efetivada'"
              >
                <!-- @click="receivedRevenue(revenue)" -->
                <mdicon
                  :name="
                    revenue.status === 'Efetivada'
                      ? 'check'
                      : new Date() <= new Date(revenue.date)
                        ? 'alert'
                        : 'alert-remove'
                  "
                  class="mdicon__lacamento"
                  :class="{
                    paga: revenue.status === 'Efetivada',
                    atrasada:
                      new Date() > new Date(revenue.date) &&
                      revenue.status === 'Pendente',
                    Pendente:
                      new Date() <= new Date(revenue.date) &&
                      revenue.status === 'Pendente',
                  }"
                  size="30"
                />
              </v-card>
              <div style="width: 100%">
                <div class="header__visao_geral">
                  <span style="text-align: start">
                    {{ revenue.carteira }}
                  </span>
                  <div>
                    <span>
                      {{ revenue.date }}
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
                          />
                          <!-- @click="displayFormEditRevenue(revenue)" -->
                          <v-list-item
                            title="Excluir"
                            link
                            @click="deletar(revenue.id)"
                          />
                        </v-list>
                      </v-menu>
                    </span>
                  </div>
                </div>
                <div style="display: flex; justify-content: space-between">
                  <span class="categoria">
                    {{ revenue.descricao }}
                  </span>
                  <span class="categoria">
                    R$ {{ formatValue(revenue.valor) }}
                  </span>
                </div>
                <div>
                  <span class="sub__categoria">
                    {{ revenue.categoria }}
                  </span>
                  <span class="sub__categoria">
                    {{ revenue.categoria }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- <v-footer></v-footer> -->
      <NoDataComponent v-else />
    </div>
    <div
      v-if="!formulario"
      class="fixed-bottom d-flex justify-end pe-5 pb-5"
      color=""
    >
      <mdicon
        type="button"
        title="adcionar nova receita"
        name="plus"
        class="mdicon__add"
        @click="formulario = true"
      />
    </div>
  </v-container>
</template>

<script setup lang="ts">
import Card from "../../components/Card.vue";
import FormReceitas from "../../components/FormReceitas.vue";
import NoDataComponent from "../../components/mobile/NoDataComponent.vue";

import { ref, computed } from "vue";
import type { Ref } from "vue";

import type { Lancamentos } from "../../types/lancamentos";

import { useRevenuesStore } from "../../store/revenues";
import { useUserStore } from "../../store/user";
import { formatValue } from "../../utils/formatValue";

import http from "../../services/http";
// import type { RevenueEdit } from "../../types/revenueEdit";
import { useWalletsStore } from "../../store/wallets";
import { useExpensesStore } from "../../store/expenses";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const userStore = useUserStore();
const useWallets = useWalletsStore();
let formulario = ref(false);

let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
let valueTotalRevenuesMonth = ref(
    useRevenues.revenuesData.revenues?.ValueTotalRevenuesMonth
);
let valuePending = ref(
    formatValue(useRevenues.revenuesData.revenues?.ValuePendingRevenues)
);
let revenuesMonth = ref(useRevenues.revenuesData.revenues?.RevenuesMonth);
// console.log(revenuesMonth.value);
let valueReceived = ref(
    formatValue(useRevenues.revenuesData.revenues?.ValueReceivedRevenues)
);
// let categorias = ref(userStore.user.categoriasReceitas);

const categoriasNames = ref([]);
userStore.user.categoriasReceitas.forEach((categoria) => {
    categoriasNames.value.push(categoria.name);
});
let contas = ref(useWallets.walletsData.walletsNames);
console.log(contas.value);
// let errorsForm = ref({ errors: {} });
// let formStoreRevenue = ref(false);
// let formEditRevenue = ref(false);
// let revenueEdit: Ref<RevenueEdit> = ref({
//     id: 0,
//     user_id: 0,
//     valor: "",
//     date: "",
//     descricao: "",
//     categoria: "",
//     carteira: "",
//     status: "",
//     created_at: "",
//     updated_at: "",
//     mesReferencia: mesAnoReferencia.value,
// });
// const revenueUnedited: Ref<RevenueEdit> = ref({
//     valor: "",
//     date: "",
//     descricao: "",
//     categoria: "",
//     carteira: "",
//     status: "",
// });
let releases = ref<Lancamentos>({
    id: null,
    descricao: "",
    valor: "",
    tipo: "Não recorente",
    numParcelas: 0,
    periodicidade: "",
    date: new Date().toLocaleDateString("en-CA"),
    status: "Efetivada",
    categoria: "Outros",
    carteira: "Outros",
    subCategoria: "",
    conta: "",
    mesReferencia: mesAnoReferencia.value,
    dateLancamento: new Date().toLocaleDateString("en-CA"),
    dateEfetivacao: new Date().toLocaleDateString("en-CA"),
});
// let categorias = reactive(userStore.user.categoriasReceitas);

// onMounted( () => {
// });

const mesPorExtenso = computed(() => {
    if (!mesAnoReferencia.value) return "";

    const [ano, mes] = mesAnoReferencia.value.split("-");
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
    } else {
        const mesAbreviado = mesesPorExtenso[parseInt(mes, 10) - 1].slice(0, 3);
        return `${mesAbreviado}./${ano.slice(2)}`;
    }
});

const updateData = (novoValor) => {
    valueTotalRevenuesMonth.value = novoValor.ValueTotalRevenuesMonth;
    valueReceived.value = novoValor.ValueReceivedRevenues;
    valuePending.value = novoValor.ValuePendingRevenues;
    revenuesMonth.value = novoValor.RevenuesMonth;
};

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
    console.log(data);
    try {
        const res = await http.post("/buscar-dados-mes", { mes: data });
        useWallets.setMesReferencia(res.data.walletsData.mes_ano_referencia);
        useExpenses.setExpensesData(res.data.expensesData);
        useRevenues.setRevenuesData(res.data.revenuesData);
        useWallets.setWalletsData(res.data.walletsData);

        mesAnoReferencia.value = res.data.walletsData.mes_ano_referencia;

        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;

        valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;

        valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
    } catch (error) {
    //
    }
};

// const formatValueSave = () => {
//     let novoValor = releases.value.valor.replace(/[^\d]/g, "");

//     if (novoValor.length > 1) {
//         const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
//         const parteDecimal = novoValor.slice(-2);
//         const parteInteiraFormatada = parteInteira.replace(
//             /\B(?=(\d{3})+(?!\d))/g,
//             "."
//         );
//         releases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
//     } else if (novoValor.length === 1) {
//         releases.value.valor = `0,0${novoValor}`;
//     } else {
//         releases.value.valor = "0,00";
//     }
// };
// const formatValueEdit = () => {
//     let novoValor = revenueEdit.value.valor.replace(/[^\d]/g, "");

//     if (novoValor.length > 1) {
//         const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
//         const parteDecimal = novoValor.slice(-2);
//         const parteInteiraFormatada = parteInteira.replace(
//             /\B(?=(\d{3})+(?!\d))/g,
//             "."
//         );
//         revenueEdit.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
//     } else if (novoValor.length === 1) {
//         revenueEdit.value.valor = `0,0${novoValor}`;
//     } else {
//         revenueEdit.value.valor = "0,00";
//     }
// };
// formatarValor();

// let status = ref(true);

// const clearInputs = () => {
//     releases.value.descricao = "",
//     releases.value.valor = "",
//     releases.value.tipo = "Não recorente",
//     releases.value.numParcelas = 0,
//     releases.value.periodicidade = "",
//     releases.value.date = new Date().toLocaleDateString("en-CA"),
//     releases.value.status = "",
//     releases.value.categoria = "",
//     releases.value.subCategoria = "",
//     releases.value.conta = "",
//     releases.value.mesReferencia = "",
//     releases.value.dateLancamento = new Date().toLocaleDateString("en-CA"),
//     releases.value.dateEfetivacao = new Date().toLocaleDateString("en-CA"); 
// };

// const revertEdit = () => {
//     revenuesMonth.value.forEach((revenue: RevenueEdit, index: number) => {
//         if (revenue.id === revenueEdit.value.id) {
//             revenuesMonth.value[index] = JSON.parse(
//                 JSON.stringify(revenueUnedited.value)
//             );
//         }
//     });
// };

// const returnRevenue = () => {
//     formStoreRevenue.value =
//     formStoreRevenue.value === true
//         ? !formStoreRevenue.value
//         : formStoreRevenue.value;
//     formEditRevenue.value =
//     formEditRevenue.value === true
//         ? !formEditRevenue.value
//         : formEditRevenue.value;
// };

// const salvarLancamentos = async () => {
//     try {
//         release.value.status = status.value ? "Efetivada" : "Pendente";
//         const res = await http.post("/save-revenue", release.value);
//         useRevenues.setRevenuesData(res.data.revenuesData);
//         valueTotalRevenuesMonth.value =
//       res.data.revenuesData.ValueTotalRevenuesMonth;
//         valuePending.value = res.data.revenuesData.ValuePendingRevenues;
//         valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
//         revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
//         useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
//         useWallets.setWallets(res.data.walletsData.wallets);
//         clearInputs();
//         formStoreRevenue.value = false;
//     } catch (error) {
//     // console.log(error.response.data.errors);
//         errorsForm.value["errors"] = error.response.data["errors"];
//     }
// };

// const receivedRevenue = async (revenue: RevenueEdit) => {
//     try {
//         const res = await http.post("/received-revenue", {
//             id: revenue.id,
//             carteira: revenue.carteira,
//             mesReferencia: mesAnoReferencia.value,
//         });
//         useRevenues.setRevenuesData(res.data.revenuesData);
//         valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
//         valuePending.value = res.data.revenuesData.ValuePendingRevenues;
//         // revenue.status = 'PAGA';
//         revenuesMonth.value.forEach((revenues) => {
//             if (revenues.id === revenue.id) {
//                 revenues.status = "Efetivada";
//             }
//         });
//         useWallets.setWallets(res.data.walletsData.wallets);
//     } catch (error) {
//     // console.log(error);
//     }
// };

// function displayFormEditRevenue(revenue: RevenueEdit) {
//     revenueUnedited.value = JSON.parse(JSON.stringify(revenue));
//     revenueEdit.value = revenue;
//     revenueEdit.value.valor = formatValue(revenueEdit.value.valor);
//     formEditRevenue.value = true;
// }

// const saveEditedRevenue = async () => {
//     try {
//         const res = await http.post("/edit-revenue", revenueEdit.value);
//         useRevenues.setRevenuesData(res.data.revenuesData);
//         useWallets.setWallets(res.data.walletsData.wallets);
//         valueTotalRevenuesMonth.value =
//       res.data.revenuesData.ValueTotalRevenuesMonth;
//         valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
//         valuePending.value = res.data.revenuesData.ValuePendingRevenues;
//         revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
//     } catch (error) {
//     // console.log(error.response.data.message);
//     // if (error.response.data.message === "Token has expired") {
//     //     alert("sessão expirada");
//     // }
//     }

//     formEditRevenue.value = false;
// };

const deletar = async (id: number) => {
    try {
        const res = await http.post("/delete-revenue", {
            id: id,
            mesReferencia: mesAnoReferencia.value,
        });
        useRevenues.setRevenuesData(res.data.revenuesData);
        valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;
        valuePending.value = res.data.revenuesData.ValuePendingRevenues;
        valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
        revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
    } catch (error) {
    // console.log(error);
    }
};

// const rules = {
//     requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
//     requiredValorMaiorQue0: (value: string) =>
//         parseFloat(value.replace(",", ".")) > 0 ||
//     "O campo valor deve ser maior que zero",
//     requiredData: (value: string) => !!value || "O campo data é obrigatório",
//     requiredDescricao: (value: string) =>
//         !!value || "O campo escriçãp é obrigatório",
//     requiredCatagoria: (value: string) =>
//         !!value || "O campo categoria é obrigatório",
//     requiredCarteira: (value: string) =>
//         !!value || "O campo categoria é obrigatório",
// };
</script>

<style>
.receitas {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100%;
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
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 50px;
  /* position: absolute; */
  /* left: calc(50% + 110px); */
  /* bottom: 30px; */
  background-color: #77d08e;
  color: #fefefe;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.btn__nova__receita {
  position: fixed;
  right: 15px;
  bottom: 15px;
  background-color: #1dbb01;
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
.Pendente {
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
