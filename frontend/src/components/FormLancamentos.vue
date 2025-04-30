<template>
  <div class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="px-3 pt-16 w-100"
      @submit.prevent="salvarLancamentos"
    >
      <div
        class="header__items d-flex justify-content-between fixed-top py-10 pe-2"
      >
        <v-btn
          :disabled="loading"
          class="close fs-5 ms-3"
          prepend-icon="mdi-close"
          @click="closeForm"
        />
        <div class="d-flex flex-column">
          <span class="fs-5">
            {{ isEditMode ? "Editar" : "Nova" }}
            {{ transactionType === "receitas" ? "Receita" : "Despesa" }}
          </span>
        </div>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || formReleases.valor === '0,00'
          "
          :loading="loading"
          class="btn"
          type="submit"
          rounded="xl"
        >
          Salvar
        </v-btn>
      </div>

      <v-textarea
        v-model="formReleases.descricao"
        variant="underlined"
        type="text"
        hide-details="auto"
        label="Descrição"
        required
        class="mb-8 imput"
        :rules="[rules.requiredDescricao]"
        rows="1"
        prepend-inner-icon="mdi-text-long"
      >
        <template #message>
          <div v-if="errorsForm.descricao" class="error-message">
            {{ errorsForm.descricao[0] }}
          </div>
        </template>
      </v-textarea>

      <v-text-field
        v-model="formReleases.valor"
        variant="underlined"
        placeholder="0,00"
        hide-details="auto"
        label="Valor"
        type="tel"
        class="mb-8 imput"
        :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
        prepend-inner-icon="mdi-currency-usd"
        @input="formatValueSave"
      >
        <template #message>
          <div v-if="errorsForm.valor" class="error-message">
            {{ errorsForm.valor[0] }}
          </div>
        </template>
      </v-text-field>

      <v-text-field
        v-model="formReleases.tipo"
        variant="underlined"
        label="Tipo"
        type="text"
        class="mb-8 imput"
        readonly
        prepend-inner-icon="mdi-refresh"
        @click="openTipoLancamento = true"
      />

      <div v-if="openTipoLancamento" class="tipo">
        <div
          class="d-flex flex-column align-start justify-space-around modal__tipo"
        >
          <v-btn
            v-for="(item, index) in tiposLancamento"
            :key="index"
            :disabled="loading"
            :loading="loading"
            :class="formReleases.tipo === item ? 'selected' : ''"
            flat
            :prepend-icon="
              formReleases.tipo === item
                ? 'mdi-radiobox-marked'
                : 'mdi-checkbox-blank-circle-outline'
            "
            @click="selecionarTipo(item)"
          >
            <span>{{ item }}</span>
          </v-btn>
        </div>
      </div>

      <div v-if="openParcelas" class="parcelas">
        <div class="container__parcelas">
          <div class="p-3">
            <h2 class="mb-4 text-center">Configurar parcelas</h2>

            <div class="py-2">
              <div class="d-flex align-center justify-space-between">
                <v-icon class="pe-3" icon="mdi-arrow-right" size="24" />
                <span class="item-label"> Parcela inicial </span>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      :disabled="tempParcelaInicial <= 1"
                      prepend-icon="mdi-chevron-down"
                      flat
                      variant="text"
                      class="stepper-btn"
                      @click="decrementParcelaInicial"
                    />
                    <input
                      v-model="tempParcelaInicial"
                      type="number"
                      class="stepper-input"
                      min="1"
                    />
                    <v-btn
                      prepend-icon="mdi-chevron-up"
                      flat
                      class="stepper-btn"
                      @click="incrementParcelaInicial"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="divider" />

            <div class="">
              <div class="d-flex align-center justify-space-between">
                <v-icon
                  icon="mdi-plus-circle-outline"
                  name="plus-circle-outline"
                  size="24"
                  class="pe-3"
                />
                <div class="item-label">Quantidade</div>
                <div class="item-value">
                  <div class="number-stepper">
                    <v-btn
                      class="stepper-btn"
                      :disabled="tempNumParcelas <= 2"
                      prepend-icon="mdi-chevron-down"
                      variant="text"
                      @click="decrementQuantidade"
                    />
                    <input
                      v-model="tempNumParcelas"
                      type="number"
                      class="stepper-input"
                      min="2"
                    />
                    <v-btn
                      class="stepper-btn"
                      prepend-icon="mdi-chevron-up"
                      variant="text"
                      @click="incrementQuantidade"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="divider" />

            <div class="">
              <div class="d-flex align-center justify-space-between">
                <v-icon icon="mdi-calendar-blank" size="24" class="pe-3" />
                <div class="item-label">Periodicidade</div>
                <div class="item-value pb-2">
                  <v-select
                    v-model="tempPeriodicidade"
                    :items="['Mensal', 'Semanal', 'Quinzenal', 'Bimestral']"
                    variant="plain"
                    hide-details
                    class="select-dark"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-space-between align-center p-3">
            <v-btn class="btn-cancelar" @click="cancelarConfiguracaoRepeticao">
              Cancelar
            </v-btn>
            <v-btn class="btn-concluido" @click="concluirParcelas">
              Concluído
            </v-btn>
          </div>
        </div>
      </div>

      <v-date-input
        v-model="formReleases.dataVencimento"
        variant="underlined"
        hide-details="auto"
        label="Data de Vencimento"
        :rules="[rules.requiredDataVencimento]"
        class="mb-8 imput"
        show-adjacent-months
        color="#77d08e"
        prepend-icon=""
        prepend-inner-icon="mdi-calendar"
      >
        <template #append-inner>
          <span v-if="isTodayVencimento" class="today__label">Hoje</span>
        </template>
        <template #message>
          <div v-if="errorsForm.date" class="error__message">
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-date-input>

      <v-text-field
        v-model="formReleases.status"
        variant="underlined"
        hide-details="auto"
        label="Status"
        type="text"
        class="mb-8 imput"
        readonly
        :prepend-inner-icon="
          formReleases.status === 'Efetivada'
            ? 'mdi-check-circle-outline'
            : 'mdi-clock-time-three-outline'
        "
        @click="toggleStatus"
      >
        <template #append-inner>
          <div
            :class="
              formReleases.status === 'Efetivada'
                ? 'form__check__efetivada'
                : 'form__check'
            "
          >
            <div
              :class="
                formReleases.status === 'Efetivada'
                  ? 'switch__check__efetivada'
                  : 'switch__check'
              "
            />
          </div>
        </template>
      </v-text-field>

      <v-autocomplete
        v-model="formReleases.categoria"
        :items="categoriasNames"
        :rules="[rules.requiredCatagoria]"
        label="Categoria"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-scatter-plot"
      >
        <template #message>
          <div v-if="errorsForm.categoria" class="error-message">
            {{ errorsForm.categoria[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.subcategoria"
        :items="subcategoriesNames"
        :rules="[rules.requiredSubcatagoria]"
        label="subcategoria"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-scatter-plot"
      >
        <template #message>
          <div v-if="errorsForm.categoria" class="error-message">
            {{ errorsForm.categoria[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.conta"
        :items="contasNames"
        :rules="[rules.requiredConta]"
        label="Conta"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-bank"
      >
        <template #message>
          <div v-if="errorsForm.conta" class="error-message">
            {{ errorsForm.conta[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-btn
        v-if="!informacoes"
        :append-icon="informacoes ? 'mdi-chevron-up' : 'mdi-chevron-down'"
        variant="plain"
        size="x-small"
        style="color: #77d08e"
        block
        @click="informacoes = !informacoes"
      >
        Mais informações
      </v-btn>

      <v-date-input
        v-if="informacoes"
        v-model="formReleases.dataLancamento"
        variant="underlined"
        hide-details="auto"
        label="Data lançamento"
        :rules="[rules.requiredDataLancamento]"
        class="mb-8 imput"
        show-adjacent-months
        color="#77d08e"
        prepend-icon=""
        prepend-inner-icon="mdi-calendar-clock"
      >
        <template #append-inner>
          <span v-if="isTodayLancamento" class="today__label">Hoje</span>
        </template>
        <template #message>
          <div v-if="errorsForm.date" class="error__message">
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-date-input>

      <v-date-input
        v-if="informacoes"
        v-model="formReleases.dataEfetivacao"
        variant="underlined"
        hide-details="auto"
        label="Data efetivação"
        :rules="[rules.requiredDataEfetivacao]"
        class="mb-8 imput"
        show-adjacent-months
        color=""
        prepend-icon=""
        prepend-inner-icon="mdi-calendar-check"
      >
        <template #append-inner>
          <span v-if="isTodayEfetivacao" class="today__label">Hoje</span>
        </template>
        <template #message>
          <div v-if="errorsForm.date" class="error__message">
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-date-input>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import http from "@/services/http";
import { useExpensesStore, useRevenuesStore, useWalletsStore } from "@/store";
import type { Lancamento } from "@/types";
import { computed, ref, watch } from "vue";

const useWallets = useWalletsStore();
const useRevenues = useRevenuesStore();
const useExpenses = useExpensesStore();

const emit = defineEmits(["updateData", "closeForm"]);

const props = defineProps<{
  releases?: Lancamento;
  rota: string;
  mesReferencia: string;
  transactionType: "receitas" | "despesas";
}>();

const validateDate = (date: string | undefined): string => {
  if (!date) return new Date().toISOString().split("T")[0];
  const parsedDate = new Date(date);
  if (isNaN(parsedDate.getTime())) {
    console.warn("Data inválida recebida, usando data atual:", date);
    return new Date().toISOString().split("T")[0];
  }
  return parsedDate.toISOString().split("T")[0];
};

let informacoes = ref(false);
let subcategoriesNames = ref<string[]>([]);
const parcelaInicial = ref(1);
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref("Mensal");
const loading = ref(false);
const validFormLancamentos = ref(false);
const openTipoLancamento = ref(false);
const openParcelas = ref(false);
const errorsForm = ref<{ [key: string]: string[] }>({});
const tiposLancamento = ref(["Não recorrente", "Parcelada", "Fixa mensal"]);

const categoriasNames = ref(
  props.transactionType === "receitas"
    ? useRevenues.revenuesData.categories.map((categoria) => categoria.name)
    : useExpenses.expensesData.categories.map((categoria) => categoria.name)
);

const contasNames = ref(useWallets.walletsData.contasNames);

const isEditMode = computed(() => !!props.releases?.id);

const isTodayVencimento = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.dataVencimento;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return typeof selectedDate === "string" && selectedDate === today;
});

const isTodayLancamento = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.dataLancamento;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return typeof selectedDate === "string" && selectedDate === today;
});

const isTodayEfetivacao = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.dataEfetivacao;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return typeof selectedDate === "string" && selectedDate === today;
});

const formReleases = ref<Lancamento>({
  id: props.releases?.id || null,
  descricao: props.releases?.descricao || "",
  valor: props.releases?.valor || "0,00",
  tipo: props.releases?.tipo || "Não recorrente",
  numParcelas: props.releases?.numParcelas || null,
  periodicidade: props.releases?.periodicidade || null,
  dataVencimento: validateDate(props.releases?.dataVencimento),
  status: props.releases?.status || "Pendente",
  categoria: props.releases?.categoria || "Outros",
  subcategoria: props.releases?.subcategoria || "Outros",
  conta: props.releases?.conta || contasNames.value[0],
  dataLancamento: validateDate(props.releases?.dataLancamento),
  dataEfetivacao: props.releases?.dataEfetivacao,
  mesReferencia: props.mesReferencia,
});

watch(
  () => formReleases.value.status,
  (newStatus) => {
    if (newStatus === "Efetivada") {
      formReleases.value.dataEfetivacao = new Date()
        .toISOString()
        .split("T")[0];
    } else {
      formReleases.value.dataEfetivacao = null;
    }
  }
);

watch(
  () => formReleases.value.categoria,
  (newCategoria) => {
    const categories =
      props.transactionType === "receitas"
        ? useRevenues.revenuesData.categories
        : useExpenses.expensesData.categories;
    const selectedCategory = categories.find(
      (cat) => cat.name === newCategoria
    );
    if (selectedCategory) {
      subcategoriesNames.value =
        selectedCategory.subcategories_data?.map(
          (subcategoria) => subcategoria.name
        ) || [];
      formReleases.value.subcategoria = subcategoriesNames.value[0] || "";
    } else {
      subcategoriesNames.value = [];
      formReleases.value.subcategoria = "";
    }
  }
);

watch(
  () => formReleases.value.dataVencimento,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.dataVencimento = newValue.toISOString().split("T")[0];
    }
  }
);

watch(
  () => formReleases.value.dataLancamento,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.dataLancamento = newValue.toISOString().split("T")[0];
    }
  }
);

watch(
  () => formReleases.value.dataEfetivacao,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.dataEfetivacao = newValue.toISOString().split("T")[0];
    }
  }
);

const incrementParcelaInicial = () => {
  tempParcelaInicial.value++;
};

const decrementParcelaInicial = () => {
  if (tempParcelaInicial.value > 1) {
    tempParcelaInicial.value--;
  }
};

// Funções para incrementar e decrementar quantidade de parcelas
const incrementQuantidade = () => {
  tempNumParcelas.value++;
};

const decrementQuantidade = () => {
  if (tempNumParcelas.value > 2) {
    tempNumParcelas.value--;
  }
};

const inicializarValoresTemporarios = () => {
  tempParcelaInicial.value = 1;
  tempNumParcelas.value = 2;
  tempPeriodicidade.value = "Mensal";
};

const cancelarConfiguracaoRepeticao = () => {
  formReleases.value.tipo = "Não recorrente";
  formReleases.value.numParcelas = 0;
  formReleases.value.periodicidade = "";

  // Fecha o modal
  openParcelas.value = false;
};

const concluirParcelas = () => {
  // Salva os valores temporários nos valores finais
  parcelaInicial.value = tempParcelaInicial.value || null;
  formReleases.value.numParcelas = tempNumParcelas.value;
  formReleases.value.periodicidade = tempPeriodicidade.value;

  // Fecha o modal
  openParcelas.value = false;
};

const toggleStatus = () => {
  formReleases.value.status =
    formReleases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};

const closeForm = () => {
  emit("closeForm");
  clearInputs();
};

const selecionarTipo = (
  item: "Não recorrente" | "Parcelada" | "Fixa mensal"
) => {
  formReleases.value.tipo = item;
  openTipoLancamento.value = false;

  if (item === "Parcelada") {
    inicializarValoresTemporarios();

    if (formReleases.value.numParcelas > 0) {
      tempNumParcelas.value = formReleases.value.numParcelas;
    }

    if (formReleases.value.periodicidade) {
      tempPeriodicidade.value = formReleases.value.periodicidade;
    }

    openParcelas.value = true;
  } else {
    formReleases.value.numParcelas = 0;
    formReleases.value.periodicidade = "Mensal";
  }
};

// interface ApiError {
//   response?: {
//     data?: {
//       errors?: { [key: string]: string[] };
//     };
//   };
// }

const salvarLancamentos = async () => {
  console.log(formReleases.value);
  try {
    loading.value = true;
    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value
      ? `/${props.rota}/${formReleases.value.id}`
      : `/${props.rota}`;
    const res = await method(url, formReleases.value);

    useRevenues.setRevenuesData(res.data.revenuesData);
    useWallets.setSaldoInicial(res.data.walletsData.saldoInicial);
    useWallets.setWalletsData(res.data.walletsData.wallets);
    emit("updateData", res.data.revenuesData);
    closeForm();
  } catch (error) {
    errorsForm.value = error.response?.data?.errors || {};
    console.error("Erro ao salvar lançamento:", error.response?.data);
    console.error("Validation errors:", errorsForm.value);
  } finally {
    loading.value = false;
  }
};

const clearInputs = () => {
  formReleases.value = {
    id: null,
    descricao: "",
    valor: "0,00",
    tipo: "Não recorrente",
    numParcelas: 0,
    periodicidade: null,
    dataVencimento: new Date().toISOString().split("T")[0],
    status: "Pendente",
    categoria: "",
    subcategoria: "",
    conta: "",
    // mesReferencia: props.mesReferencia,
    dataLancamento: new Date().toISOString().split("T")[0],
    dataEfetivacao: new Date().toISOString().split("T")[0],
  };
  errorsForm.value = {};
};

const formatValueSave = () => {
  let novoValor = formReleases.value.valor.replace(/[^\d]/g, "");
  if (novoValor.length > 1) {
    const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
    const parteDecimal = novoValor.slice(-2);
    const parteInteiraFormatada = parteInteira.replace(
      /\B(?=(\d{3})+(?!\d))/g,
      "."
    );
    formReleases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
  } else if (novoValor.length === 1) {
    formReleases.value.valor = `0,0${novoValor}`;
  } else {
    formReleases.value.valor = "0,00";
  }
};

const rules = {
  requiredDescricao: (value: string) =>
    !!value || "O campo descrição é obrigatório",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) => {
    if (!value) return "O campo valor é obrigatório";
    const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
    return (
      (!isNaN(numericValue) && numericValue > 0) ||
      "O campo valor deve ser maior que zero"
    );
  },
  requiredDataVencimento: (value: string) =>
    !!value || "O campo data vencimento é obrigatório",
  requiredStatus: (value: string) => !!value || "O campo Status é obrigatório",
  requiredCatagoria: (value: string) =>
    !!value || "O campo categoria é obrigatório",
  requiredSubcatagoria: (value: string) =>
    !!value || "O campo subcategoria é obrigatório",
  requiredConta: (value: string) => !!value || "O campo conta é obrigatório",
  requiredDataLancamento: (value: string) =>
    !!value || "O campo data lançamento é obrigatório",
  requiredDataEfetivacao: (value: string) => {
    if (formReleases.value.status === "Efetivada") {
      return !!value || "O campo data efetivação é obrigatório";
    }
    return true;
  },
};
</script>

<style scoped>
.container__modal {
  width: 100%;
  max-width: 600px;
  height: 100%;
  min-height: 100%;
  background: rgb(15, 15, 15);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.header__items {
  background-color: rgb(15, 15, 15);
  color: #fefefe;
  height: 70px;
}
.close {
  cursor: pointer;
  border-radius: 50%;
  height: 40px;
  width: 40px;
  background-color: transparent;
  color: #fefefe;
  display: flex;
  justify-content: center;
  align-items: center;
}
.btn {
  color: #fff;
  cursor: pointer;
  font-weight: bold;
  align-self: center;
  border: none;
  margin-top: 1rem;
  font-size: 20px;
  background-color: #77d08e;
  border: 1px solid #77d08e;
  transition: background-color 0.5s;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
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
.modal__tipo {
  background: #2c2c2e;
  color: #fefefe;
  height: 200px;
  border-radius: 20px;
  padding: 15px;
}
.selected {
  color: #77d08e;
}
.parcelas {
  position: fixed;
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
.container__parcelas {
  background: #1e1e1e;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
}
.item-label {
  flex-grow: 1;
  font-size: 18px;
  font-weight: 400;
}
.item-value {
  margin-right: 20px;
  font-size: 18px;
  font-weight: 500;
}
.number-stepper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  width: 120px;
}
.stepper-btn {
  background-color: transparent;
  border: none;
  width: 30px;
  color: #999;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stepper-input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield;
}
.stepper-input::-webkit-outer-spin-button,
.stepper-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.divider {
  height: 1px;
  background-color: #333;
  margin: 5px 0;
}
.select-dark {
  color: white;
  width: 120px;
  text-align: right;
}
.btn-cancelar {
  color: #77d08e;
  background-color: transparent;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.btn-concluido {
  background-color: #77d08e;
  color: white;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.today__label {
  font-size: 16px;
  color: #77d08e;
  font-weight: 500;
  margin-right: 8px;
}
.form__check__efetivada {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(119, 208, 142, 0.4);
  display: flex;
  justify-content: flex-end;
}
.form__check {
  width: 40px;
  height: 20px;
  border-radius: 15px;
  background-color: rgba(255, 255, 255, 0.3);
}
.switch__check__efetivada {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #77d08e;
}
.switch__check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: #fefefe;
}
.error__message {
  color: red;
  font-size: 12px;
  margin-top: 4px;
}
h2 {
  font-size: 28px;
  font-weight: 500;
  color: white;
  margin-bottom: 30px;
}
</style>
