<template>
  <div class="container__modal">
    <v-form
      v-model="validFormLancamentos"
      class="px-3 w-100"
      @submit.prevent="salvarLancamentos"
    >
      <div
        class="header__items d-flex justify-content-between fixed-top py-10 align-items-center"
      >
        <div class="d-flex">
          <v-btn
            :disabled="loading"
            class="close fs-5 ms-2"
            prepend-icon="mdi-close"
            @click="closeForm"
          />
          <span class="fs-5">
            {{ isEditMode ? "Editar" : "Nova" }}
            {{ transactionType }}
          </span>
        </div>
        <v-btn
          :disabled="
            loading || !validFormLancamentos || formReleases.valor === '0,00'
          "
          :loading="loading"
          class="btn m-0 me-3 p-0 px-2"
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
        v-model="formReleases.recorrencia"
        variant="underlined"
        label="Recorrência"
        type="text"
        class="mb-8 imput"
        readonly
        prepend-inner-icon="mdi-refresh"
        @click="openRecorrenciaModal = true"
      />

      <div v-if="openRecorrenciaModal" class="tipo">
        <div
          class="d-flex flex-column align-start justify-space-around modal__tipo"
        >
          <v-btn
            v-for="item in tiposRecorrencia"
            :key="item"
            :disabled="loading"
            style="background: transparent"
            :class="formReleases.recorrencia === item ? 'selected' : ''"
            flat
            :prepend-icon="
              formReleases.recorrencia === item
                ? 'mdi-radiobox-marked'
                : 'mdi-checkbox-blank-circle-outline'
            "
            @click="selecionarRecorrencia(item)"
          >
            <span>{{ item }}</span>
          </v-btn>
        </div>
      </div>

      <div v-if="openParcelas" class="parcelas" />

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
      />

      <v-autocomplete
        v-model="formReleases.subcategoria"
        :items="subcategoriesNames"
        label="Subcategoria"
        placeholder="Selecione..."
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-scatter-plot"
      />

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
      />
    </v-form>
  </div>
  <ErrorsForm />
  <ErrorMessage />
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import http from "@/services/http";
import {
  useExpensesStore,
  useRevenuesStore,
  useWalletsStore,
  useErrorStore,
} from "@/store";
import type { Lancamento, ApiErrorResponse } from "@/types";
import { formatValue } from "@/utils/formatValue";
import type { AxiosError } from "axios";
import { computed, ref, watch } from "vue";
import { format as formatDate, isValid } from "date-fns";

const useWallets = useWalletsStore();
const useRevenues = useRevenuesStore();
const useExpenses = useExpensesStore();
const errorStore = useErrorStore();

const emit = defineEmits(["updateData", "closeForm"]);

const props = defineProps<{
  releases?: Lancamento;
  rota: string;
  mesReferencia: string;
  transactionType: "Receita" | "Despesa";
}>();

const validateDate = (date: string | Date | undefined): string => {
  if (!date) return formatDate(new Date(), "yyyy-MM-dd");
  const parsedDate = new Date(date);
  if (!isValid(parsedDate)) {
    return formatDate(new Date(), "yyyy-MM-dd");
  }
  return formatDate(parsedDate, "yyyy-MM-dd");
};

let informacoes = ref(false);
let subcategoriesNames = ref<string[]>([]);
const parcelaInicial = ref<number | null>(null);
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref<
  | "Mensal"
  | "Diario"
  | "Semanal"
  | "Quinzenal"
  | "Trimenstral"
  | "Anual"
  | undefined
>("Mensal");
const loading = ref(false);
const validFormLancamentos = ref(false);
const openParcelas = ref(false);
const errorsForm = ref<{ [key: string]: string[] }>({});

// CORREÇÃO: Lógica de tipos separada para recorrência
const openRecorrenciaModal = ref(false);
const tiposRecorrencia = ref<("Único" | "Fixo" | "Parcelado")[]>([
  "Único",
  "Fixo",
  "Parcelado",
]);

const toggleStatus = () => {
  formReleases.value.status =
    formReleases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};

const formatValueSave = () => {
  let novoValor = formReleases.value.valor.replace(/[^\d]/g, "");
  if (novoValor.length > 2) {
    const parteInteira = novoValor.slice(0, -2).replace(/^0+/, "") || "0";
    const parteDecimal = novoValor.slice(-2);
    const parteInteiraFormatada = parteInteira.replace(
      /\B(?=(\d{3})+(?!\d))/g,
      "."
    );
    formReleases.value.valor = `${parteInteiraFormatada},${parteDecimal}`;
  } else if (novoValor.length === 2) {
    formReleases.value.valor = `0,${novoValor}`;
  } else if (novoValor.length === 1) {
    formReleases.value.valor = `0,0${novoValor}`;
  } else {
    formReleases.value.valor = "0,00";
  }
};

// CORREÇÃO: A lista de categorias agora é uma propriedade computada para reagir a mudanças no tipo
const categoriasNames = computed(() => {
  if (props.transactionType === "Receita") {
    return useRevenues.revenuesData?.categories.map((cat) => cat.name) || [];
  } else {
    return useExpenses.expensesData?.categories.map((cat) => cat.name) || [];
  }
});

const contasNames = ref(useWallets.walletsData.contasNames);
const isEditMode = computed(() => !!props.releases?.id);

const isToday = (dateValue: string | Date | undefined | null): boolean => {
  if (!dateValue) return false;
  const today = formatDate(new Date(), "yyyy-MM-dd");
  const selectedDate = formatDate(new Date(dateValue), "yyyy-MM-dd");
  return selectedDate === today;
};

const isTodayVencimento = computed(() =>
  isToday(formReleases.value.dataVencimento)
);
const isTodayLancamento = computed(() =>
  isToday(formReleases.value.dataLancamento)
);
const isTodayEfetivacao = computed(() =>
  isToday(formReleases.value.dataEfetivacao)
);

// CORREÇÃO: `tipo` agora vem direto das props e `recorrencia` é inicializado
const formReleases = ref<Lancamento>({
  id: props.releases?.id || null,
  descricao: props.releases?.descricao || "",
  valor: formatValue(Number(props.releases?.valor)) || "0,00",
  tipo: props.transactionType, // Vem direto das props
  recorrencia: props.releases?.recorrencia || "Único", // Novo campo
  numParcelas: props.releases?.numParcelas || undefined,
  periodicidade: props.releases?.periodicidade || undefined,
  dataVencimento: validateDate(props.releases?.dataVencimento),
  status: props.releases?.status || "Pendente",
  categoria: props.releases?.categoria || "",
  subcategoria: props.releases?.subcategoria || "",
  conta: props.releases?.conta || contasNames.value[0],
  dataLancamento: validateDate(props.releases?.dataLancamento),
  dataEfetivacao: props.releases?.dataEfetivacao || null,
  mesReferencia: props.mesReferencia,
});

watch(
  () => formReleases.value.status,
  (newStatus) => {
    if (newStatus === "Efetivada") {
      formReleases.value.dataEfetivacao = formatDate(new Date(), "yyyy-MM-dd");
    } else {
      formReleases.value.dataEfetivacao = null;
    }
  }
);

watch(
  () => formReleases.value.categoria,
  (newCategoria) => {
    const categoriesSource =
      props.transactionType === "Receita"
        ? useRevenues.revenuesData?.categories
        : useExpenses.expensesData?.categories;

    const selectedCategory = categoriesSource?.find(
      (cat) => cat.name === newCategoria
    );
    if (selectedCategory) {
      subcategoriesNames.value =
        selectedCategory.subcategories?.map((sub) => sub.name) || [];
      // Define a primeira subcategoria como padrão, se houver
      formReleases.value.subcategoria = subcategoriesNames.value[0] || "";
    } else {
      subcategoriesNames.value = [];
      formReleases.value.subcategoria = "";
    }
  },
  { immediate: true } // Executa o watch imediatamente ao criar o componente
);

const formatDateOnWatch = (newValue: any) => {
  if (newValue instanceof Date) {
    return formatDate(newValue, "yyyy-MM-dd");
  }
  return newValue;
};

watch(
  () => formReleases.value.dataVencimento,
  (nv) => (formReleases.value.dataVencimento = formatDateOnWatch(nv))
);
watch(
  () => formReleases.value.dataLancamento,
  (nv) => (formReleases.value.dataLancamento = formatDateOnWatch(nv))
);
watch(
  () => formReleases.value.dataEfetivacao,
  (nv) => (formReleases.value.dataEfetivacao = formatDateOnWatch(nv))
);

const closeForm = () => {
  emit("closeForm");
  // clearInputs() é chamado no final para limpar
};

// CORREÇÃO: Nova função para lidar com a seleção de recorrência
const selecionarRecorrencia = (item: "Único" | "Fixo" | "Parcelado") => {
  formReleases.value.recorrencia = item;
  openRecorrenciaModal.value = false;

  if (item === "Parcelado") {
    // Abre o modal de configuração de parcelas
    openParcelas.value = true;
  } else {
    // Reseta os dados de parcela se não for "Parcelado"
    formReleases.value.numParcelas = undefined;
    formReleases.value.periodicidade = undefined;
  }
};

const salvarLancamentos = async () => {
  errorStore.unsetError();
  try {
    loading.value = true;
    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value
      ? `/${props.rota}/${formReleases.value.id}`
      : `/${props.rota}`;
    const res = await method(url, formReleases.value);

    // SUGESTÃO: A store de despesa/receita deveria ser a única a ser atualizada,
    // as outras (wallets) deveriam reagir a essa mudança internamente ou no backend.
    if (props.transactionType === "Receita") {
      useRevenues.setRevenuesData(res.data.revenuesData);
      emit("updateData", res.data.revenuesData);
    } else {
      useExpenses.setExpensesData(res.data.expensesData);
      emit("updateData", res.data.expensesData);
    }
    useWallets.setWalletsData(res.data.walletsData);

    closeForm();
  } catch (error) {
    const axiosError = error as AxiosError<ApiErrorResponse>;
    if (axiosError.response?.data.errors) {
      errorStore.setErrorFromForm(axiosError);
    } else {
      errorStore.setErrorFromResponse(axiosError);
    }
  } finally {
    loading.value = false;
  }
};

// ... (Resto do script, como rules, formatValueSave, etc., sem grandes alterações)

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
  requiredCatagoria: (value: string) =>
    !!value || "O campo categoria é obrigatório",
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
