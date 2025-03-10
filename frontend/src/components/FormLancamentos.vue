<template>
  <mdicon
    type="button"
    title="adcionar nova receita"
    name="plus"
    class="mdicon"
    @click="openModal = true"
  />

  <div v-if="openModal" class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="form__lançamentos"
      @submit.prevent="salvarLancamentos"
    >
      <!-- <div class="header"> -->
      <!-- <buttom
          :disabled="loading"
          :loading="loading"
          class="px-5 close"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </buttom> -->
      <div class="header__items">
        <buttom
          :disabled="loading"
          :loading="loading"
          class="px-5 close"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </buttom>
        <div class="d-flex flex-column">
          <span class="fs-5"> Nova receitas </span>
        </div>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || releases.valor === '0,00'
          "
          :loading="loading"
          style="background-color: #77d08e"
          class="salvar px-5"
          type="submit"
        >
          Salvar
        </v-btn>
      </div>

      <v-textarea
        v-model="releases.descricao"
        variant="underlined"
        type="text"
        hide-details="auto"
        label="Descricao"
        required
        class="mb-8 imput"
        :rules="[rules.requiredDescricao]"
        rows="1"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="text-long" />
        </template>
      </v-textarea>

      <v-text-field
        v-model="releases.valor"
        variant="underlined"
        placeholder="0,00"
        hide-details="auto"
        label="Valor"
        type="tel"
        class="mb-8 imput"
        :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
        @input="formatValueSave()"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="currency-usd" />
        </template>
      </v-text-field>

      <v-text-field
        v-model="releases.tipo"
        variant="underlined"
        label="Tipo"
        type="text"
        class="mb-8 imput"
        @click="openTipoLancamento = true"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="refresh" />
        </template>
      </v-text-field>

      <div v-if="openTipoLancamento" class="tipo">
        <div class="modal__tipo">
          <div
            v-for="(item, index) in tiposLancamento"
            :key="index"
            class="cor__icon"
          >
            <div class="container__tipos">
              <div class="container__tipo" @click="selecionarTipo(item)">
                <mdicon
                  :class="releases.tipo == item ? 'selected' : ''"
                  :name="
                    releases.tipo == item
                      ? 'radiobox-marked'
                      : 'checkbox-blank-circle-outline'
                  "
                />
                <span class="ms-3">{{ item }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="openParcelas" class="parcelas">
        <div class="container__parcelas pb-5">
          <div class="modal__parcelas">
            <v-text-field
              v-model="releases.numParcelas"
              variant="underlined"
              type="text"
              class="mb-8 imput"
            >
              <template #prepend-inner>
                <mdicon class="me-2" name="refresh" />
                <span class="me-2">Quantidade </span>
              </template>
            </v-text-field>
            <v-text-field
              v-model="releases.periodicidade"
              variant="underlined"
              type="text"
              class="mb-8 imput"
            >
              <template #prepend-inner>
                <mdicon class="me-2" name="refresh" />
                <span class="me-2">Periodicidade </span>
              </template>
            </v-text-field>
          </div>
          <div class="botoes__parcelas mx-5">
            <v-btn
              class="px-5 me-5 cancelar"
              @click="openParcelas = !openParcelas"
            >
              Cancelar
            </v-btn>
            <v-btn class="btn__concluido px-5" type="submit"> Concluido </v-btn>
          </div>
        </div>
      </div>

      <!-- <ModalTipoLancamento
        v-model="openModal"
        :items="items"
        @updateSelectedIcon="updateSelectedIcon"
      /> -->

      <!-- <ModalParcelar v-model="ModalParcelar" /> -->

      <v-text-field
        v-model="releases.date"
        variant="underlined"
        hide-details="auto"
        label="Data vencimento"
        type="date"
        :rules="[rules.requiredData]"
        class="mb-5 imput"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="calendar" />
        </template>
      </v-text-field>

      <v-text-field
        v-model="releases.status"
        variant="underlined"
        hide-details="auto"
        type="text"
        class="mb-5 imput cursor__pointer"
        style="cursor: pointer !important"
        @click="toggleStatus"
      >
        <template #prepend-inner>
          <mdicon
            class="icon__modify"
            :name="
              releases.status == 'Efetivada'
                ? 'check-circle-outline'
                : 'clock-time-three-outline'
            "
          />
        </template>
        <template #append-inner>
          <div
            :class="
              releases.status == 'Efetivada'
                ? 'form__check__efetivada'
                : 'form__check'
            "
          >
            <div
              :class="
                releases.status == 'Efetivada'
                  ? 'switch__check__efetivada'
                  : 'switch__check'
              "
            ></div>
          </div>
        </template>
      </v-text-field>

      <v-autocomplete
        ref="country"
        v-model="releases.categoria"
        :items="categoriasNames"
        :rules="[rules.requiredCatagoria]"
        label="Categoria"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="scatter-plot" />
        </template>
      </v-autocomplete>
      <v-autocomplete
        ref="country"
        v-model="releases.subCategoria"
        :items="countries"
        :rules="[() => !!country || 'This field is required']"
        label="Subcategoria"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="scatter-plot" />
        </template>
      </v-autocomplete>
      <v-autocomplete
        ref="country"
        v-model="releases.carteira"
        :items="carteiras"
        :rules="[rules.requiredCarteira]"
        label="Conta"
        placeholder="Select..."
        required
        style="color: #ccc"
        variant="underlined"
      >
        <template #prepend-inner>
          <mdicon class="icon__modify" name="bank" />
        </template>
      </v-autocomplete>

      <!-- <div class="d-flex justify-content-center"> -->
      <v-btn
        append-icon="mdi-account-circle"
        variant="plain"
        size="x-small"
        style="color: #77d08e"
        block
        @click="informacoes = !informacoes"
      >
        Mais informações
        <template v-slot:append>
          <mdicon
            :name="informacoes ? 'chevron-up' : 'chevron-down'"
            class="pb-2 fs-3"
          />
        </template>
      </v-btn>
      <!-- </div> -->

      <!-- <div
        class="form-group p-0 container d-flex justify-content-between col-12 mt-2 mb-4"
      >
        <v-btn
          :disabled="loading"
          :loading="loading"
          style="background-color: #dc3545; color: #fefefe"
          class="px-5"
          @click="
            {
              openModal = !openModal;
            }
            clearInputs();
          "
        >
          <mdicon name="close" size="25" />
        </v-btn>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || releases.valor === '0,00'
          "
          :loading="loading"
          style="background-color: #77d08e"
          class="btn-light px-5"
          type="submit"
        >
          Salvar
        </v-btn>
      </div> -->
    </v-form>
  </div>
</template>
<script setup lang="ts">
import { useUserStore } from "@/store/user";
import { reactive, ref } from "vue";
import http from "@/services/http";
import type { Lancamentos } from "@/types/lancamentos";
import { useWalletsStore } from "@/store/wallets";
import ModalTipoLancamento from "@/components/ModalTipoLancamento.vue";
import ModalParcelar from "@/components/ModalParcelar.vue";
import { useRevenuesStore } from "@/store/revenues";
import { formatValue } from "@/utils/formatValue";

const userStore = useUserStore();
const useWallets = useWalletsStore();
const useRevenues = useRevenuesStore();

let informacoes = ref(false);
let categorias = reactive(userStore.user.categoriasDespesas);
let openTipoLancamento = ref(false);
let openParcelas = ref(false);
let errorsForm = ref({ errors: {} });
let parcelar = ref(false);
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
const selectedColor = ref("");
const tiposLancamento = ref(["Não recorrente", "Parcelada", "Fixa mensal"]);

const getCurrentDate = () => {
  const today = new Date();
  const day = String(today.getDate()).padStart(2, "0");
  const month = String(today.getMonth() + 1).padStart(2, "0"); // Janeiro é 0!
  const year = today.getFullYear();
  return `${year}-${month}-${day}`;
};

let releases: Ref<Lancamentos> = ref({
  descricao: "",
  valor: "",
  tipo: "Não recorrente",
  numParcelas: 0,
  periodicidade: "",
  date: getCurrentDate(),
  categoria: "Outros",
  subCategoria: "Outros",
  carteira: "Pessoal",
  status: "Efetivada",
  mesReferencia: mesAnoReferencia.value,
});

const categoriasNames = ref([]);
userStore.user.categoriasReceitas.forEach((categoria) => {
  categoriasNames.value.push(categoria.name);
});

let carteiras = ref(useWallets.walletsData.walletsNames);
const selectedIcon = ref("");
const openModal = ref(false);
let validFormLancamentos = ref(false);
let status = ref(true);
const nameCategory = ref("");
// const props = defineProps({
//     categoria: {
//         type: Array,
//     }
// });
const toggleStatus = () => {
  releases.value.status =
    releases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};

const emit = defineEmits(["updateData"]);

const selecionarTipo = (item: string) => {
  releases.value.tipo = item;
  openTipoLancamento.value = false;
  if (item === "Parcelada") {
    openParcelas.value = true;
  }
};

const salvarLancamentos = async () => {
  try {
    releases.value.status = status.value ? "Efetivada" : "pendente";
    const res = await http.post("/save-revenue", releases.value);
    useRevenues.setRevenuesData(res.data.revenuesData);
    valueTotalRevenuesMonth.value =
      res.data.revenuesData.ValueTotalRevenuesMonth;
    emit("updateData", res.data.revenuesData);
    useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
    useWallets.setWallets(res.data.walletsData.wallets);
    clearInputs();
    openModal.value = false;
  } catch (error) {
    console.log(error.response.data.errors);
    errorsForm.value["errors"] = error.response.data["errors"];
  }
};

const updateSelectedIcon = (novoValor: string) => {
  selectedIcon.value = novoValor;
};
const updateSelectedColor = (novoValor: string) => {
  selectedColor.value = novoValor;
};

const clearInputs = () => {
  releases.value.valor = "";
  releases.value.date = "";
  releases.value.descricao = "";
  releases.value.categoria = "";
  releases.value.carteira = "";
};

const saveCategory = async () => {
  const data = ref({
    name: nameCategory.value,
    color: selectedColor.value,
    icon: selectedIcon.value,
    typeCategory: "",
    edit: true,
  });
  try {
    data.value.typeCategory =
      props.color === "color__despesa" ? "despesa" : "receita";

    const res = await http.post("/save-category", data.value);
    useUser.setUserData(res.data.user);
    if (res.data.categoriasDespesas) {
      emit("updateCategoriasDespesas", res.data.categoriasDespesas);
    }
    if (res.data.categoriasReceitas) {
      emit("updateCategoriasReceitas", res.data.categoriasReceitas);
    }
    nameCategory.value = "";
    selectedColor.value = "";
    selectedIcon.value = "";
    openModal.value = false;
  } catch (error) {
    // console.log(error);
  }
};

const formatValueSave = () => {
  let novoValor = releases.value.valor.replace(/[^\d]/g, "");

  if (novoValor.length > 1) {
    const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
    const parteDecimal = novoValor.slice(-2);
    const parteInteiraFormatada = parteInteira.replace(
      /\B(?=(\d{3})+(?!\d))/g,
      "."
    );
    releases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
  } else if (novoValor.length === 1) {
    releases.value.valor = `$ 0,0${novoValor}`;
  } else {
    releases.value.valor = "$ 0,00";
  }
};

const rules = {
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) =>
    parseFloat(value.replace(",", ".")) > 0 ||
    "O campo valor deve ser maior que zero",
  requiredData: (value: string) => !!value || "O campo data é obrigatório",
  requiredDescricao: (value: string) =>
    !!value || "O campo descriçãp é obrigatório",
  requiredCatagoria: (value: string) =>
    !!value || "O campo categoria é obrigatório",
  requiredCarteira: (value: string) =>
    !!value || "O campo categoria é obrigatório",
};
</script>

<style scoped>
.mdicon {
  cursor: pointer;
  padding: 10px;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 50px;
  position: absolute;
  right: 30px;
  bottom: 30px;
  background-color: #77d08e;
  color: #fefefe;
}
.container__modal {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  /* background: rgba(0, 0, 0, 0.5); */
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
}
.botoes__parcelas {
  display: flex;
  justify-content: end;
  margin-top: 20px;
}
.container__parcelas {
  background: #2c2c2e;
  color: #fefefe;
  width: 90%;
  /* height: 200px; */
  margin: 15px auto;
  border-radius: 20px;
  /* padding: 15px; */
}
.modal__parcelas {
  /* background: #2c2c2e; */
  color: #fefefe;
  width: 90%;
  /* height: 200px; */
  margin: 15px auto;
  /* border-radius: 20px; */
  /* padding: 15px; */
}
.parcelas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: end;
}
.tipo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.selected {
  color: #77d08e;
}

.container__tipos {
  width: 100%;
  display: flex;
  /* flex-direction: column; */
  /* align-items: center; */
}

.container__tipo {
  cursor: pointer;
  width: 100%;
  display: flex;
  /* justify-content: center; */
  margin-block: 10px;
}

.modal__tipo {
  background: #2c2c2e;
  /* position: relative; */
  color: #fefefe;
  /* z-index: 999; */
  /* top: 20%; */
  /* left: 50%; */
  width: 50%;
  max-width: 450px;
  height: 200px;
  /* margin-left: 2.5%; */
  border-radius: 20px;
  padding: 15px;
}

/* .container__tipo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
} */

.form__check {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(255, 255, 255, 0.3);
}
.form__check__efetivada {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(119, 208, 142, 0.4);
  display: flex;
  justify-content: flex-end;
}

.switch__check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #fefefe;
}
.switch__check__efetivada {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #77d08e;
}

.form__lançamentos {
  width: 100%;
  padding: 0 10px;
}
.v-field__prefix.custom-prefix {
  color: red !important;
}
.close {
  cursor: pointer;
  border-radius: 50%;
  height: 35px;
  width: 35px;
  color: #fefefe;
  display: flex;
  justify-content: center;
  align-items: center;
}
.cancelar {
  border-radius: 20px;
  background-color: transparent;
  color: #77d08e;
}
.btn__concluido {
  border-radius: 20px;
  background-color: #77d08e;
  /* color: #fefefe; */
}
.close:hover {
  background-color: #1c1c1e;
}
.header__items {
  color: #fefefe;
  margin-bottom: 20px;
}

.color__despesa {
  color: rgb(255, 82, 82);
}

.color__despesa:hover {
  color: rgb(204, 0, 0);
}

.color__receita {
  color: #00c853;
}

.color__receita:hover {
  color: green;
}

.modal {
  background: #2c2c2e;
  position: fixed;
  color: #fefefe;
  z-index: 999;
  top: 20%;
  left: 50%;
  width: 450px;
  height: auto;
  margin-left: -200px;
  border-radius: 20px;
  padding: 15px;
}

.header__modal {
  display: flex;
  justify-content: space-between;
}

.title {
  text-align: center;
}

.mdicon__close {
  cursor: pointer;
  color: rgba(255, 255, 255, 0.3);
}

.mdicon__close:hover {
  color: #fefefe;
}

.inputSimples {
  background-color: transparent;
  border: solid 1px rgba(255, 255, 255, 0.4);
  height: 55px;
  margin: 20px 0 0 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 3px;
}

.imput {
  /* background-color: #1e1e1e !important; */
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
  top: 8px;
  opacity: 0.4;
  cursor: text;
  transition: 0.5s ease-in-out;
}

.input:focus ~ label,
.input:valid ~ label {
  transform: translateY(-30px);
  opacity: 0.9;
}

.error {
  height: 20px;
}

.span__error {
  color: rgb(194, 4, 4);
  position: relative;
  top: 0;
  left: 0;
}

.cor__icon {
  display: flex;
}

.container__cor__categoria {
  width: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.cor__categoria {
  width: 100%;
  display: flex;
  justify-content: space-around;
  margin-block: 10px;
}

.cor__forma {
  width: 25px;
  height: 25px;
  border-radius: 50%;
}

.icon__categoria {
  width: 100%;
  display: flex;
  justify-content: space-around;
  margin-block: 10px;
}

.footer__modal {
  margin-top: 20px;
  display: flex;
  justify-content: end;
}

.btn__modal {
  border: none;
  border-radius: 20px;
  padding-block: 5px;
  padding-inline: 20px;
  color: rgba(255, 255, 255, 0.3);
  background-color: rgba(255, 255, 255, 0.12);
}
</style>
