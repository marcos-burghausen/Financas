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
        placeholder="Descrição"
        required
        class="mb-3 imput"
        :rules="[rules.requiredDescricao]"
        rows="1"
        prepend-inner-icon="mdi-text-long"
      >
      </v-textarea>

      <v-text-field
        v-model="formReleases.valor"
        variant="underlined"
        hide-details="auto"
        type="tel"
        class="mb-3 imput"
        :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
        prepend-inner-icon="mdi-currency-usd"
        @input="formatValueSave"
      >
      </v-text-field>

      <div class="custom__input__container mb-3">
        
        <!-- <div class="custom-input-label">Recorrência</div> -->
        <div class="custom-input-content" @click="openRecorrenciaModal = true">
          <v-icon icon="mdi-refresh" class="me-2" />
          <div class="d-flex flex-column">
            <span>{{ formReleases.recorrencia }}</span>
            <span v-if="detalheRecorrencia" class="detalhe-parcela-interno">
              {{ detalheRecorrencia }}
            </span>
          </div>
          <v-spacer />
          <v-icon
            v-if="formReleases.recorrencia === 'Parcelado'"
            icon="mdi-pencil"
            size="x-small"
            class="edit-icon"
            @click.stop="openParcelas = true"
          />
        </div>

        <v-btn-toggle
          v-if="formReleases.recorrencia === 'Parcelado'"
          v-model="tipoCalculoParcela"
          mandatory
          class="parcela-toggle mt-4"
          variant="flat"
        >
          <v-btn class="toggle-btn" value="total" rounded="lg">Valor total</v-btn>
          <v-btn class="toggle-btn" value="parcela" rounded="lg">Valor parcela</v-btn>
        </v-btn-toggle>

        <div class="custom__underline" />
      </div>

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

      <div class="mb-2 pt-1">
        <v-menu
          v-model="menuDataVencimento"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div class="custom__display__input" v-bind="props">
              <div class="d-flex align-center text-grey">
                <v-icon icon="mdi-calendar" class="me-3" />
                <span>Data de vencimento</span>
              </div>
              <v-spacer class="m-0 p-0" />
              <span class="font-weight-medium">{{ displayDataVencimento }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataVencimento"
            @update:modelValue="menuDataVencimento = false"
            color="#77d08e"
            hide-header
            show-adjacent-months
          />
        </v-menu>
      </div>

      <v-text-field
        v-model="formReleases.status"
        variant="underlined"
        hide-details="auto"
        type="text"
        class="mb-6 imput"
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
        required
        variant="underlined"
        class="mb-6 imput"
      >
        <template #prepend-inner>
          <v-icon :icon="categoriaIcon" :class="categoriaColorClass" />
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.subcategoria"
        :items="subcategoriesNames"
        label="Subcategoria"
        variant="underlined"
        class="mb-6 imput"
      >
        <template #prepend-inner>
          <v-icon :icon="subcategoriaIcon" :class="subcategoriaColorClass" />
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
        class="mb-6 imput"
        prepend-inner-icon="mdi-bank"
      />

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

      <div
        v-if="informacoes"
        class="mb-6">
        <v-menu
          v-model="menuDataLancamento"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div class="custom__display__input" v-bind="props">
              <div class="d-flex align-center text-grey">
                <v-icon icon="mdi-calendar" class="me-3" />
                <span>Data de lançamento</span>
              </div>
              <v-spacer />
              <span class="font-weight-medium">{{ displayDataLancamento }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataLancamento"
            @update:modelValue="menuDataLancamento = false"
            color="#77d08e"
            hide-header
            show-adjacent-months
          />
        </v-menu>
      </div>
      
      <div
        v-if="informacoes"
        class="mb-1">
        <v-menu
          v-model="menuDataEfetivacao"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div class="custom__display__input" v-bind="props">
              <div class="d-flex align-center text-grey">
                <v-icon icon="mdi-calendar" class="me-3" />
                <span>Data de efetivação</span>
              </div>
              <v-spacer />
              <span class="font-weight-medium">{{ displayDataEfetivacao }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataEfetivacao"
            @update:modelValue="menuDataEfetivacao = false"
            color="#77d08e"
            hide-header
            show-adjacent-months
          />
        </v-menu>
      </div>
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
import { isToday, isYesterday, isTomorrow, parseISO, format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

const useWallets = useWalletsStore();
const useRevenues = useRevenuesStore();
const useExpenses = useExpensesStore();
const errorStore = useErrorStore();

const emit = defineEmits(["updateData", "closeForm"]);
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);

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
const tiposRecorrencia = ref<("Não recorrente" | "Fixa" | "Parcelado")[]>([
  "Não recorrente",
  "Fixa",
  "Parcelado",
]);

const toggleStatus = () => {
  formReleases.value.status =
    formReleases.value.status === "Efetivada" ? "Pendente" : "Efetivada";
};

const formatValueSave = () => {
  // 1. Pega apenas os dígitos do valor
  let digits = formReleases.value.valor.replace(/\D/g, "");

  // 2. Remove zeros à esquerda, tratando o caso de ser tudo zero
  digits = digits.replace(/^0+/, "") || "0";

  // 3. Garante que o valor tenha pelo menos 3 dígitos para a formatação (ex: 50 vira 050)
  while (digits.length < 3) {
    digits = "0" + digits;
  }

  // 4. Separa a parte inteira e a decimal
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);

  // 5. Formata a parte inteira com pontos como separadores de milhar
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  // 6. Monta o valor final formatado
  formReleases.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

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

  // Pega a data de hoje, já formatada corretamente.
  const todayStr = formatDate(new Date(), "yyyy-MM-dd");

  let selectedDateStr: string;

  if (typeof dateValue === 'string') {
    // Se o valor já for uma string (ex: '2025-07-23'), 
    // usamos apenas os 10 primeiros caracteres para evitar problemas de fuso horário.
    selectedDateStr = dateValue.substring(0, 10);
  } else {
    // Se for um objeto Date (vindo do seletor de data), nós o formatamos.
    selectedDateStr = formatDate(dateValue, "yyyy-MM-dd");
  }

  return todayStr === selectedDateStr;
};

const displayDataVencimento = computed(() => {
  // Se não houver data, não mostre nada.
  if (!formReleases.value.dataVencimento) return 'Selecione...';

  // Converte a string 'yyyy-MM-dd' do seu model para um objeto Date
  const data = parseISO(formReleases.value.dataVencimento);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return 'Hoje';
  if (isYesterday(data)) return 'Ontem';
  if (isTomorrow(data)) return 'Amanhã';
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, 'EEEE', { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, 'dd/MM/yyyy');

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const displayDataLancamento = computed(() => {
  // Se não houver data, não mostre nada.
  if (!formReleases.value.dataLancamento) return 'Selecione...';

  // Converte a string 'yyyy-MM-dd' do seu model para um objeto Date
  const data = parseISO(formReleases.value.dataLancamento);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return 'Hoje';
  if (isYesterday(data)) return 'Ontem';
  if (isTomorrow(data)) return 'Amanhã';
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, 'EEEE', { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, 'dd/MM/yyyy');

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const displayDataEfetivacao = computed(() => {
  // Se não houver data, não mostre nada.
  if (!formReleases.value.dataEfetivacao) return null;

  // Converte a string 'yyyy-MM-dd' do seu model para um objeto Date
  const data = parseISO(formReleases.value.dataEfetivacao);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return 'Hoje';
  if (isYesterday(data)) return 'Ontem';
  if (isTomorrow(data)) return 'Amanhã';
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, 'EEEE', { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, 'dd/MM/yyyy');

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const isTodayVencimento = computed(() =>
  isToday(formReleases.value.dataVencimento)
);
console.log(isTodayVencimento);
const isTodayLancamento = computed(() =>
  isToday(formReleases.value.dataLancamento)
);
const isTodayEfetivacao = computed(() =>
  isToday(formReleases.value.dataEfetivacao)
);

const tipoCalculoParcela = ref<'total' | 'parcela'>('total');

const formReleases = ref<Lancamento>({
  id: props.releases?.id || null,
  descricao: props.releases?.descricao || "",
  valor: formatValue(Number(props.releases?.valor)) || "0,00",
  tipo: props.transactionType,
  recorrencia: props.releases?.recorrencia || "Não recorrente",
  numParcelas: props.releases?.numParcelas || null,
  periodicidade: props.releases?.periodicidade || null,
  dataVencimento: validateDate(props.releases?.dataVencimento),
  status: props.releases?.status || "Pendente",
  categoria: props.releases?.categoria || "Outros",
  subcategoria: props.releases?.subcategoria || "Outros",
  conta: props.releases?.conta || contasNames.value[0],
  dataLancamento: validateDate(props.releases?.dataLancamento),
  dataEfetivacao: props.releases?.dataEfetivacao || null,
  mesReferencia: props.mesReferencia,
});

const detalheRecorrencia = computed(() => {
  if (
    formReleases.value.recorrencia === "Parcelado" &&
    formReleases.value.numParcelas &&
    formReleases.value.numParcelas > 0
  ) {
    const valorInput = parseFloat(
      formReleases.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (isNaN(valorInput) || valorInput <= 0) return "";

    if (tipoCalculoParcela.value === 'total') {
      const valorParcela = valorInput / formReleases.value.numParcelas;
      return `Em ${formReleases.value.numParcelas}x de R$ ${valorParcela}`;
    }
    else {
      return `Em ${formReleases.value.numParcelas}x de R$ ${valorInput}`;
    }
  }
  return "";
});

// Busca a lista de categorias correta (Receita ou Despesa)
const categoriesSource = computed(() => 
  props.transactionType === "Receita"
    ? useRevenues.revenuesData?.categories
    : useExpenses.expensesData?.categories
);

// Encontra o objeto da categoria selecionada
const selectedCategoryObject = computed(() =>
  categoriesSource.value?.find(
    (cat) => cat.name === formReleases.value.categoria
  )
);

// Encontra o objeto da subcategoria selecionada
const selectedSubcategoryObject = computed(() =>
  selectedCategoryObject.value?.subcategories?.find(
    (sub) => sub.name === formReleases.value.subcategoria
  )
);

// Retorna o ícone e a cor para a CATEGORIA
const categoriaIcon = computed(() => selectedCategoryObject.value?.icon || 'mdi-scatter-plot');
const categoriaColorClass = computed(() => selectedCategoryObject.value?.color || '');

// Retorna o ícone e a cor para a SUBCATEGORIA
const subcategoriaIcon = computed(() => selectedSubcategoryObject.value?.icon || 'mdi-scatter-plot');
const subcategoriaColorClass = computed(() => selectedSubcategoryObject.value?.color || '');

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
  formReleases.value.numParcelas = tempNumParcelas.value;
  formReleases.value.periodicidade = tempPeriodicidade.value;

  // Fecha o modal
  openParcelas.value = false;
};

const closeForm = () => {
  emit("closeForm");
  clearInputs();
};

// CORREÇÃO: Nova função para lidar com a seleção de recorrência
const selecionarRecorrencia = (item: "Não recorrente" | "Fixa" | "Parcelado") => {
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

// const selecionarTipo = (item: string) => {
//   formReleases.value.tipo = item;
//   openTipoLancamento.value = false;

//   if (item === "Parcelada") {
//     inicializarValoresTemporarios();

//     if (formReleases.value.numParcelas > 0) {
//       tempNumParcelas.value = formReleases.value.numParcelas;
//     }

//     if (formReleases.value.periodicidade) {
//       tempPeriodicidade.value = formReleases.value.periodicidade;
//     }

//     openParcelas.value = true;
//   } else {
//     formReleases.value.numParcelas = 0;
//     formReleases.value.periodicidade = "Mensal";
//   }
// };

const salvarLancamentos = async () => {
  errorStore.unsetError();

  if (!validFormLancamentos.value) {
    return;
  }

  // const payload = { ...formReleases.value };
  // if (
  //   payload.recorrencia === 'Parcelado' &&
  //   tipoCalculoParcela.value === 'parcela' &&
  //   payload.numParcelas
  // ) {
  //   const valorParcela = parseFloat(payload.valor.replace(/\./g, "").replace(",", "."));
  //   const valorTotalReal = valorParcela * payload.numParcelas;
  //   // Atualiza o valor no payload para ser o total
  //   payload.valor = String(valorTotalReal);
  // }
  // // FIM DA PARTE NOVA

  // try {
  //   loading.value = true;
  //   const method = isEditMode.value ? http.put : http.post;
  //   const url = isEditMode.value
  //     ? `/${props.rota}/${payload.id}`
  //     : `/${props.rota}`;
  //   // Use o 'payload' modificado aqui
  //   const res = await method(url, payload); 



  try {
    loading.value = true;
    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value
      ? `/${props.rota}/${formReleases.value.id}`
      : `/${props.rota}`;
    const res = await method(url, formReleases.value);

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
  background: rgba(0, 0, 0, 0.5);
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
.detalhe-parcela {
  display: flex;
  align-items: center;
  /* Remove o justify-content para que o lápis fique ao lado do texto */
  
  color: #b0b0b0; /* Cor mais suave para o texto de detalhe */
  
  /* Ajuste fino na margem para ficar logo abaixo do campo, sem sobrepor */
  margin-top: 2px;
  padding-bottom: 8px; /* Espaçamento abaixo da linha de detalhe */

  /* Alinha o texto com o início do input (após o ícone) */
  margin-left: 40px; 
  font-size: 14px;
  height: 24px; /* Garante altura consistente */
}

.detalhe-parcela .v-icon {
  cursor: pointer;
  color: #77d08e;
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
  background: rgba(12, 12, 12, 0.8);
  z-index: 999;
  display: flex;
  justify-content: center;
  align-items: flex-end;
}
.container__parcelas {
  background: #161616ff;
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
  overflow: hidden;
  color: #fefefe;
  padding-bottom: 20px;
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
.custom__input__container {
  position: relative;
  padding-top: 20px;
  padding-bottom: 4px; /* Espaço antes da linha de baixo */
}

.custom-input-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 4px;
}

.custom-input-content {
  display: flex;
  align-items: center;
  color: #fff;
  cursor: pointer;
}

.detalhe-parcela-interno {
  font-size: 14px;
  color: #e0e0e0;
  line-height: 1.2;
  margin-top: 4px;
}

.edit-icon {
  color: #77d08e;
}

/* Linha de baixo do input */
.custom__underline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.7);
}

/* ESTILOS NOVOS PARA OS BOTÕES TOGGLE */
.parcela-toggle {
  display: flex;
  border-radius: 10px;
  border: 1px solid #4F4F4F;
  background-color: transparent;
  overflow: hidden;
}

.parcela-toggle .toggle-btn {
  flex: 1;
  text-transform: none;
  font-size: 14px;
  color: #bdbdbd;
  background-color: transparent;
}

.parcela-toggle .v-btn--active {
  background-color: #77d08e;
  color: #121212 !important; /* Cor do texto do botão ativo */
  font-weight: bold;
}

.custom__display__input {
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  padding: 8px 0;
  cursor: pointer;
  color: #fff;
}
</style>
