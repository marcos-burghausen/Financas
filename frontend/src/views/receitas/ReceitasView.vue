<template>
  <div class="content-wrapper">
    <div class="header">
      <router-link
        class="link me-7 d-flex align-items-center opaco"
        :to="{ name: 'dashboard' }"
      >
        <mdicon name="arrow-left" size="25" />
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
    <button
      v-if="!formStoreRevenue && !formEditRevenue"
      class="btn__nova__receita"
      @click="formStoreRevenue = !formStoreRevenue"
    >
      <mdicon name="plus" class="mdicon" size="30" />
    </button>

    <!-- ========================================================================= -->
    <!-- ================ inicio formulario lançamentos receitas ================= -->
    <!-- ========================================================================= -->
    <div v-if="formStoreRevenue" class="container-fluid">
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
              />
              <label class="form-check-label" for="flexSwitchCheckChecked"
                >Recebida</label
              >
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
                    formStoreRevenue = !formStoreRevenue;
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
    <!-- ================= fim formulario lançamentos receitas =================== -->
    <!-- ========================================================================= -->

    <!-- ========================================================================= -->
    <!-- =================== inicio formulario editar receita= =================== -->
    <!-- ========================================================================= -->
    <div
      v-if="formEditRevenue"
      class="container-fluid"
      style="padding: 0 !important"
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
                    formEditRevenue = !formEditRevenue;
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

    <div v-if="revenuesMonth && revenuesMonth.length > 0">
      <div v-if="!formStoreRevenue && !formEditRevenue" class="container-fluid">
        <div
          class="container__table"
          v-for="(revenue, key) in revenuesMonth"
          :key="revenue.id"
        >
          <div class="card__lancamento">
            <!-- :class="{ recebida: revenue.status === 'RECEBIDA' }" -->
            <div
              class="mdicon__card"
              :disabled="revenue.status === 'RECEBIDA'"
              @click="receivedRevenue(revenue)"
            >
              <mdicon
                :name="
                  revenue.status === 'RECEBIDA'
                    ? 'check'
                    : new Date() <= new Date(revenue.date)
                    ? 'alert'
                    : 'alert-remove'
                "
                class="mdicon__lacamento"
                :class="{
                  paga: revenue.status === 'RECEBIDA',
                  atrasada:
                    new Date() > new Date(revenue.date) &&
                    revenue.status === 'AGUARDANDO',
                  aguardando:
                    new Date() <= new Date(revenue.date) &&
                    revenue.status === 'AGUARDANDO',
                }"
                size="30"
              />
            </div>
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
                    <mdicon name="dots-vertical" class="mdicon" size="25" />
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
                          @click="displayFormEditRevenue(revenue)"
                        ></v-list-item>
                        <v-list-item
                          title="Excluir"
                          link
                          @click="deletar(revenue.id)"
                        ></v-list-item>
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
    <NoDataComponent v-else />
  </div>
</template>

<script setup lang="ts">
import Card from "@/components/Card.vue";
import FormLancamentos from "@/components/FormLancamentos.vue";
import NoDataComponent from "@/components/mobile/NoDataComponent.vue";

import { ref, reactive, computed, onMounted, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

import { useRevenuesStore } from "@/store/revenues";
import { useUserStore } from "@/store/user";
import { formatValue } from "@/utils/formatValue";

import http from "@/services/http";
import type { RevenueEdit } from "@/types/revenueEdit";
import { useAuthStore } from "@/store/auth";
import { useWalletsStore } from "@/store/wallets";
import { useExpensesStore } from "@/store/expenses";

const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const userStore = useUserStore();
const useWallets = useWalletsStore();

const useAuth = useAuthStore();

let validFormLancamentos = ref(false);
let validFormEdit = ref(false);
let loading = ref(false);

let mesAnoReferencia = ref(useWallets.walletsData?.mes_ano_referencia);
console.log(mesAnoReferencia.value);
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
let carteiras = ref(useWallets.walletsData.walletsNames);
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
  updated_at: "",
  mesReferencia: mesAnoReferencia.value,
});
const revenueUnedited: Ref<RevenueEdit> = ref({
  valor: "",
  date: "",
  descricao: "",
  categoria: "",
  carteira: "",
  status: "",
});
let release: Ref<Lancamentos> = ref({
  valor: "",
  date: "",
  descricao: "",
  categoria: "",
  carteira: "",
  status: "",
  mesReferencia: mesAnoReferencia.value,
});

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
    return `${mesAbreviado}.${ano.slice(2)}`;
  }
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
  let novoValor = revenueEdit.value.valor.replace(/[^\d]/g, "");

  if (novoValor.length > 1) {
    const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
    const parteDecimal = novoValor.slice(-2);
    const parteInteiraFormatada = parteInteira.replace(
      /\B(?=(\d{3})+(?!\d))/g,
      "."
    );
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
      revenuesMonth.value[index] = JSON.parse(
        JSON.stringify(revenueUnedited.value)
      );
    }
  });
};

const returnRevenue = () => {
  formStoreRevenue.value =
    formStoreRevenue.value === true
      ? !formStoreRevenue.value
      : formStoreRevenue.value;
  formEditRevenue.value =
    formEditRevenue.value === true
      ? !formEditRevenue.value
      : formEditRevenue.value;
};

const salvarLancamentos = async () => {
  try {
    release.value.status = status.value ? "RECEBIDA" : "AGUARDANDO";
    const res = await http.post("/save-revenue", release.value);
    useRevenues.setRevenuesData(res.data.revenuesData);
    valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;
    valuePending.value = res.data.revenuesData.ValuePendingRevenues;
    valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
    revenuesMonth.value = res.data.revenuesData.RevenuesMonth;
    useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
    useWallets.setWallets(res.data.walletsData.wallets);
    clearInputs();
    formStoreRevenue.value = false;
  } catch (error) {
    // console.log(error.response.data.errors);
    errorsForm.value["errors"] = error.response.data["errors"];
  }
};

const receivedRevenue = async (revenue: RevenueEdit) => {
  try {
    const res = await http.post("/received-revenue", {
      id: revenue.id,
      carteira: revenue.carteira,
      mesReferencia: mesAnoReferencia.value,
    });
    useRevenues.setRevenuesData(res.data.revenuesData);
    valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
    valuePending.value = res.data.revenuesData.ValuePendingRevenues;
    // revenue.status = 'PAGA';
    revenuesMonth.value.forEach((revenues) => {
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
    useWallets.setWallets(res.data.walletsData.wallets);
    valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;
    valueReceived.value = res.data.revenuesData.ValueReceivedRevenues;
    valuePending.value = res.data.revenuesData.ValuePendingRevenues;
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

<style>
.content-wrapper {
  position: relative;
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
.mdicon {
  color: #757575;
  cursor: pointer;
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
