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
          <div
            v-if="errorsForm.descricao"
            class="error-message"
          >
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
          <div
            v-if="errorsForm.valor"
            class="error-message"
          >
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

      <div
        v-if="openTipoLancamento"
        class="tipo"
      >
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

      <div
        v-if="openParcelas"
        class="parcelas"
      >
        <div class="container__parcelas">
          <div class="p-3">
            <h2 class="mb-4 text-center">
              Configurar parcelas
            </h2>

            <div class="py-2">
              <div class="d-flex align-center justify-space-between">
                <v-icon
                  class="pe-3"
                  icon="mdi-arrow-right"
                  size="24"
                />
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
                    >
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
                <div class="item-label">
                  Quantidade
                </div>
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
                    >
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
                <v-icon
                  icon="mdi-calendar-blank"
                  size="24"
                  class="pe-3"
                />
                <div class="item-label">
                  Periodicidade
                </div>
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
            <v-btn
              class="btn-cancelar"
              @click="cancelarConfiguracaoRepeticao"
            >
              Cancelar
            </v-btn>
            <v-btn
              class="btn-concluido"
              @click="concluirParcelas"
            >
              Concluído
            </v-btn>
          </div>
        </div>
      </div>

      <v-date-input
        v-model="formReleases.data_vencimento"
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
          <span
            v-if="isTodayVencimento"
            class="today__label"
          >Hoje</span>
        </template>
        <template #message>
          <div
            v-if="errorsForm.date"
            class="error__message"
          >
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
          <div
            v-if="errorsForm.categoria"
            class="error-message"
          >
            {{ errorsForm.categoria[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.subCategoria"
        :items="subCategoriasNames"
        :rules="[rules.requiredSubCatagoriasNames]"
        label="subcategoria"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-scatter-plot"
      >
        <template #message>
          <div
            v-if="errorsForm.categoria"
            class="error-message"
          >
            {{ errorsForm.categoria[0] }}
          </div>
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.conta"
        :items="contasNames"
        :rules="[rules.requiredCarteira]"
        label="Conta"
        placeholder="Selecione..."
        required
        variant="underlined"
        class="mb-8 imput"
        prepend-inner-icon="mdi-bank"
      >
        <template #message>
          <div
            v-if="errorsForm.conta"
            class="error-message"
          >
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
        v-model="formReleases.data_lancamento"
        variant="underlined"
        hide-details="auto"
        label="Data lançamento"
        :rules="[rules.requiredDataLanacamento]"
        class="mb-8 imput"
        show-adjacent-months
        color="#77d08e"
        prepend-icon=""
        prepend-inner-icon="mdi-calendar-clock"
      >
        <template #append-inner>
          <span
            v-if="isTodayLancamento"
            class="today__label"
          >Hoje</span>
        </template>
        <template #message>
          <div
            v-if="errorsForm.date"
            class="error__message"
          >
            {{ errorsForm.date[0] }}
          </div>
        </template>
      </v-date-input>

      <v-date-input
        v-if="informacoes"
        v-model="formReleases.data_efetivacao"
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
          <span
            v-if="isTodayEfetivacao"
            class="today__label"
          >Hoje</span>
        </template>
        <template #message>
          <div
            v-if="errorsForm.date"
            class="error__message"
          >
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

const subCategoriasNames = computed(() => {
  const categories =
    props.transactionType === "receitas"
      ? useRevenues.revenuesData.categories || []
      : useExpenses.expensesData.categories || [];

  const selectedCategory = categories.find(
    (cat) => cat.name === formReleases.value.categoria
  );

  return selectedCategory && Array.isArray(selectedCategory.subcategories)
    ? selectedCategory.subcategories.map((sub) => sub.name)
    : [];
});
console.log(subCategoriasNames.value);

const contasNames = ref(useWallets.walletsData.contasNames);

const isEditMode = computed(() => !!props.releases?.id);

const isTodayVencimento = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.data_lancamento;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return selectedDate === today;
});

const isTodayLancamento = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.data_lancamento;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return selectedDate === today;
});

const isTodayEfetivacao = computed(() => {
  const today = new Date().toISOString().split("T")[0];
  const selectedDate = formReleases.value.data_efetivacao;
  if (selectedDate instanceof Date) {
    return selectedDate.toISOString().split("T")[0] === today;
  }
  return selectedDate === today;
});

const formReleases = ref<Lancamento>({
  id: props.releases?.id || null,
  descricao: props.releases?.descricao || "",
  valor: props.releases?.valor || "0,00",
  tipo: props.releases?.tipo || "Não recorrente",
  num_parcelas: props.releases?.num_parcelas || 0,
  periodicidade: props.releases?.periodicidade || null,
  data_vencimento: validateDate(props.releases?.data_vencimento),
  status: props.releases?.status || "Pendente",
  categoria: props.releases?.categoria || categoriasNames.value[0] || "",
  subcategoria: props.releases?.subcategoria || "",
  conta: props.releases?.conta || contasNames.value[0],
  // mesReferencia: props.releases?.mesReferencia || props.mesReferencia,
  data_lancamento: validateDate(props.releases?.data_lancamento),
  data_efetivacao: props.releases?.data_efetivacao,
});

watch(
  () => formReleases.value.categoria,
  
  (newCategoria) => {
    console.log(formReleases.value.categoria);
    // Redefinir subCategoria quando a categoria muda
    formReleases.value.subcategoria = "";
    // Opcionalmente, defina uma subcategoria padrão, se necessário
    const categories =
      props.transactionType === "receitas"
        ? useRevenues.revenuesData.categories
        : useExpenses.expensesData.categories;
    const selectedCategory = categories.find(
      (cat) => cat.name === newCategoria
    );
    if (selectedCategory && selectedCategory.subcategories.length > 0) {
      formReleases.value.subcategoria = selectedCategory.subcategories[0].name;
    }
  }
);

watch(
  () => categoriasNames.value,
  (newCategorias) => {
    if (
      newCategorias.length > 0 &&
      !newCategorias.includes(formReleases.value.categoria)
    ) {
      formReleases.value.categoria = newCategorias[0] || "";
      formReleases.value.subCategoria = "";
    }
  },
  { immediate: true }
);

watch(
  () => formReleases.value.data,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.data = newValue.toISOString().split("T")[0];
    }
  }
);

watch(
  () => formReleases.value.dateLancamento,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.dateLancamento = newValue.toISOString().split("T")[0];
    }
  }
);

watch(
  () => formReleases.value.dateEfetivacao,
  (newValue) => {
    if (newValue instanceof Date) {
      formReleases.value.dateEfetivacao = newValue.toISOString().split("T")[0];
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
  // Retorna tipo para "Não recorrente"
  formReleases.value.tipo = "Não recorrente";
  formReleases.value.numParcelas = 0;
  formReleases.value.periodicidade = "";

  // Fecha o modal
  openParcelas.value = false;
};

const concluirParcelas = () => {
  // Salva os valores temporários nos valores finais
  parcelaInicial.value = tempParcelaInicial.value;
  formReleases.value.num_parcelas = tempNumParcelas.value;
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

const selecionarTipo = (item: string) => {
  formReleases.value.tipo = item;
  openTipoLancamento.value = false;

  if (item === "Parcelada") {
    // Inicializa valores para parcelamento
    inicializarValoresTemporarios();

    // Se já existirem valores salvos, usa-os como valores temporários
    if (formReleases.value.num_parcelas > 0) {
      tempNumParcelas.value = formReleases.value.num_parcelas;
    }

    if (formReleases.value.periodicidade) {
      tempPeriodicidade.value = formReleases.value.periodicidade;
    }

    // Abre o modal
    openParcelas.value = true;
  } else {
    // Para outros tipos, limpa os valores de parcelamento
    formReleases.value.num_parcelas = 0;
    formReleases.value.periodicidade = "Mensal";
  }
};

const salvarLancamentos = async () => {
  try {
    loading.value = true;
    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value
      ? `/${props.rota}/${formReleases.value.id}`
      : `/${props.rota}`;
    console.log(url);
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
    num_parcelas: 0,
    periodicidade: null,
    data_vencimento: new Date().toISOString().split("T")[0],
    status: "Pendente",
    categoria: "",
    subcategoria: "",
    conta: "",
    // mesReferencia: props.mesReferencia,
    data_lancamento: new Date().toISOString().split("T")[0],
    data_efetivacao: new Date().toISOString().split("T")[0],
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
  requiredPeriodicidade: (value: string) =>
    !!value || "O campo periodicidade é obrigatório",
  requiredDataVencimento: (value: string) => !!value || "O campo data é obrigatório",
  requiredCatagoria: (value: string) =>
    !!value || "O campo categoria é obrigatório",
  requiredCarteira: (value: string) => !!value || "O campo conta é obrigatório",
  requiredSubCatagoriasNames: (value: string) =>
    !!value || "O campo subcategoria é obrigatório",
};
</script>

<style scoped>
/* .v-btn {
  background-color: transparent;
  cursor: pointer;
} */

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
  /* text-transform: uppercase; */
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

/* .stepper-btn:hover:not(:disabled) {
  color: #77d08e;
} */
.stepper-input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield; /* Firefox */
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
