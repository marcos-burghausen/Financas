<template>
  <div class="container__modal">
    <v-form 
      v-model="isFormValid" 
      class="px-3 w-100" 
      @submit.prevent="submitForm"
    >
      <div class="header__items d-flex justify-content-between fixed-top py-10 align-items-center">
        <div class="d-flex align-items-center">
          <v-btn 
            :disabled="loading" 
            class="close fs-5 ms-2" 
            icon="mdi-close" 
            @click="closeForm" 
          />
          <div class="d-flex flex-column ms-2">
            <span class="fs-5">
              {{ isEditMode ? "Editar" : "Nova" }}
              {{ transactionType }}
            </span>
            <span
              v-if="isCard"
              style="font-size: 12px;"
            >
              Cartão de Crédito
            </span>
          </div>
        </div>
        <v-btn 
          :disabled="loading || !isFormValid" 
          :loading="loading" 
          class="btn m-0 me-3 p-0 px-2" 
          type="submit"
          rounded="xl"
        >
          Salvar
        </v-btn>
      </div>

      <div class="form__body">
        <!-- <v-textarea

         -->

        <v-text-field
          v-model="form.descricao" 
          label="Descrição" 
          variant="underlined" 
          :rules="[rules.required]"
          prepend-inner-icon="mdi-text-long" 
          class="mb-4" 
        />
  
        <v-text-field
          v-model="form.valor"
          variant="underlined"
          hide-details="auto"
          label="Valor" 
          type="tel"
          class="mb-3"
          :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
          prepend-inner-icon="mdi-currency-usd"
          @input="formatValueSave"
        />

        <div
          v-if="props.lancamento?.recorrencia === 'Não recorrente' || !isEditMode"
          class="custom__input__container mb-3"
        >
          <div
            class="custom__input__content"
            @click="openRecorrenciaModal = true"
          >
            <v-icon
              icon="mdi-refresh"
              class="me-2"
            />
            <div class="d-flex flex-column">
              <span>{{ form.recorrencia }}</span>
              <span
                v-if="detalheRecorrencia"
                class="detalhe__parcela__interno"
              >
                {{ detalheRecorrencia }}
              </span>
            </div>
            <v-spacer />
            <v-icon
              v-if="form.recorrencia === 'Parcelado'"
              icon="mdi-pencil"
              size="x-small"
              class="edit__icon"
              @click.stop="openParcelas = true"
            />
          </div>

          <v-btn-toggle
            v-if="form.recorrencia === 'Parcelado'"
            v-model="tipoCalculoParcela"
            mandatory
            class="parcela__toggle mt-4"
            variant="flat"
          >
            <v-btn
              class="toggle__btn"
              value="total"
              rounded="lg"
            >
              Valor total
            </v-btn>
            <v-btn
              class="toggle__btn"
              value="parcela"
              rounded="lg"
            >
              Valor parcela
            </v-btn>
          </v-btn-toggle>

          <div class="custom__underline" />
        </div>

        <div
          v-if="openRecorrenciaModal"
          class="tipo"
        >
          <div
            class="d-flex flex-column align-start justify-space-around modal__tipo"
          >
            <v-btn
              v-for="item in tiposRecorrencia"
              :key="item"
              :disabled="loading"
              style="background: transparent"
              :class="form.recorrencia === item ? 'selected' : ''"
              flat
              :prepend-icon="
                form.recorrencia === item
                  ? 'mdi-radiobox-marked'
                  : 'mdi-checkbox-blank-circle-outline'
              "
              @click="selecionarRecorrencia(item)"
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
                  <span class="item__label"> Parcela inicial </span>
                  <div class="item__value">
                    <div class="number__stepper">
                      <v-btn
                        :disabled="tempParcelaInicial <= 1"
                        prepend-icon="mdi-chevron-down"
                        flat
                        variant="text"
                        class="stepper__btn"
                        @click="decrementParcelaInicial"
                      />
                      <input
                        v-model="tempParcelaInicial"
                        type="number"
                        class="stepper__input"
                        min="1"
                      >
                      <v-btn
                        prepend-icon="mdi-chevron-up"
                        flat
                        class="stepper__btn"
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
                  <div class="item__label">
                    Quantidade
                  </div>
                  <div class="item__value">
                    <div class="number__stepper">
                      <v-btn
                        class="stepper__btn"
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
                        class="stepper__btn"
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
                  <div class="item__label">
                    Periodicidade
                  </div>
                  <div class="item__value pb-2">
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

        <template v-if="isCard">
          <v-select
            v-model="form.cartao_id"
            label="Cartão de Crédito"
            :items="creditCardAccounts"
            item-title="name"
            item-value="id"
            variant="underlined"
            :rules="[rules.required]"
          >
            <template #selection="{ item }">
              <div class="d-flex align-center">
                <!-- <component :is="getCardIcon(item.raw.bandeira)" class="brand-icon me-2" /> -->
                <v-icon
                  :icon="getBankIcon(item.raw.name)"
                  size="25"
                  class="mr-2"
                />
                <span>{{ item.title }}</span>
              </div>
            </template>
            <template #append-inner>
              <v-icon
                v-if="selectedCreditCard"
                :icon="getBankIcon(selectedCreditCard.bandeira)"
                size="25"
                class="mr-2"
              />
            </template>
            <template #item="{ props, item }">
              <v-list-item
                with="80"
                v-bind="props" 
                :prepend-icon="getBankIcon(item.raw.name)" 
                :append-icon="getBankIcon(item.raw.bandeira)" 
                :title="item.raw.name"
              />
            </template>
          </v-select>

          <div class="fatura-custom-select" ref="faturaSelectRef" @click="toggleDropdown">
  
            <div class="fatura-prefix">
              <v-icon icon="mdi-calendar" size="20" class="me-2" />
              <span>Fatura</span>
            </div>

            <div class="fatura-selection">
              <span>{{ form.fatura || 'Selecione' }}</span>
              <v-icon icon="mdi-chevron-down" size="20" class="ms-2" />
            </div>

            <div v-if="isDropdownOpen" class="fatura-dropdown">
              <div
                v-for="item in invoiceList"
                :key="item"
                class="dropdown-item"
                @click.stop="selectInvoice(item)"
              >
                {{ item }}
              </div>
            </div>
          </div>
        </template>


        <v-text-field
          v-if="isCard"
          :model-value="linkedAccountName"
          label="Conta"
          variant="underlined"
          readonly
          prepend-inner-icon="mdi-bank"
          class="mt-4"
        />
        <v-select
          v-else
          v-model="form.conta_id"
          label="Conta"
          :items="availableBankAccounts"
          item-title="name"
          item-value="id"
          variant="underlined"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-bank"
          class="mt-4"
        />

        <v-row>
          <v-col cols="6">
            <v-select
              v-model="form.categoria"
              label="Categoria"
              :items="availableCategories"
              item-title="name"
              item-value="name"
              variant="underlined"
              :rules="[rules.required]"
              @update:model-value="form.subcategoria = ''"
            />
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="form.subcategoria"
              label="Subcategoria"
              :items="availableSubcategories"
              item-title="name"
              item-value="name"
              variant="underlined"
            />
          </v-col>
        </v-row>
        
        <v-select
          v-if="!isCard"
          v-model="form.status_lancamento"
          label="Status"
          :items="['Pendente', 'Efetivada']"
          variant="underlined"
        />
      </div>
    </v-form>
  </div>
</template>

<script setup lang="ts">
import http from "@/services/http";
import { useErrorStore, useWalletsStore } from "@/store";
import { useLancamentoStore } from "@/store/lancamentos";
import type { CategoryData, Lancamento, Wallet } from "@/types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon } from "@/utils/iconMapper"; // Supondo que você tenha este utilitário
import { onClickOutside } from '@vueuse/core';
import { computed, nextTick, onMounted, ref } from "vue";

// --- PROPS & EMITS ---
const props = defineProps<{
  transactionType?: "Receita" | "Despesa";
  isCard?: boolean;
  rota: string;
  mesAno: string;
  lancamento?: Lancamento;
  modelValue: { type: [string, number, Object, null], default: null },
  items: { type: Array, default: () => [] },
  /** Se true, clicar em qualquer lugar do wrapper abre o menu */
  openOnWrapper: { type: boolean, default: false },
}>();
const emit = defineEmits(["close"]);

// --- STORES ---
const walletsStore = useWalletsStore();
const lancamentoStore = useLancamentoStore();
const errorStore = useErrorStore();

// ---  STATE (REFS) ---
const form = ref<Partial<Lancamento & { cartao_id: number | null, fatura: string | null }>>({
  tipo_lancamento: props.transactionType || "Despesa",
  descricao: "",
  valor: formatValue(Number(props.lancamento?.valor)) || "0,00",
  conta_id: null,
  cartao_id: null,
  fatura: "",
  data_lancamento: new Date().toISOString().split("T")[0],
  recorrencia: "Não recorrente",
  categoria: "",
  subcategoria: "",
  status_lancamento: props.isCard ? "Efetivada" : "Pendente",
});

const loading = ref(false);
const isFormValid = ref(false);
const isEditMode = computed(() => !!props.lancamento?.id);
const isDropdownOpen = ref(false);
const faturaSelectRef = ref(null);
const dropdownContainerRef = ref<HTMLElement | null>(null); // Ref para o container da lista


// --- LÓGICA DE INTERAÇÃO (onClickOutside) ---
onClickOutside(faturaSelectRef, () => {
  isDropdownOpen.value = false; // A mágica acontece aqui!
});

// ---  COMPUTED PROPERTIES ---
const formTitle = computed(() => `${props.lancamento ? "Editar" : "Novo(a)"} ${form.value.tipo_lancamento}`);

const creditCardAccounts = computed<Wallet[]>(() => walletsStore.walletsData.cartoes || []);
const availableBankAccounts = computed<Wallet[]>(() => walletsStore.walletsData.contas || []);

const selectedCreditCard = computed(() => {
  if (!form.value.cartao_id) return null;
  return creditCardAccounts.value.find(c => c.id === form.value.cartao_id);
});
const linkedAccountName = computed(() => {
  if (!selectedCreditCard.value || !selectedCreditCard.value.conta_pai_id) return "Nenhuma conta vinculada";
  const conta = availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
  return conta?.name || "Conta não encontrada";
});

const invoiceList = computed(() => {
  const list = [];
  const now = new Date();
  
  const formatDate = (date: Date) => {
    // Pega o mês abreviado (ex: 'out.'), converte para maiúsculas e remove o ponto.
    const month = date.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace('.', '');
    const year = date.getFullYear();
    return `${month}/${year}`;
  };

  for (let i = 12; i > 0; i--) {
    list.push(formatDate(new Date(now.getFullYear(), now.getMonth() - i, 1)));
  }
  for (let i = 0; i <= 12; i++) {
    list.push(formatDate(new Date(now.getFullYear(), now.getMonth() + i, 1)));
  }
  return list;
});

const availableCategories = computed<CategoryData[]>(() => {
  if (!walletsStore.walletsData.categories) return [];
  const typeMap: { [key: string]: string } = { "Receita": "receita", "Despesa": "despesa" };
  const currentType = typeMap[props.transactionType];
  return walletsStore.walletsData.categories.filter(c => c.type === currentType || c.type === "ambas");
});

const availableSubcategories = computed(() => {
  const selectedCategory = availableCategories.value.find(c => c.name === form.value.categoria);
  return selectedCategory ? selectedCategory.subcategories : [];
});

// ---  MÉTODOS ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) => {
    if (!value) return "O campo valor é obrigatório";
    const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
    return (!isNaN(numericValue) && numericValue > 0) || "O valor deve ser maior que zero";
  },
};

const formatValueSave = () => {
  // 1. Pega apenas os dígitos do valor
  let digits = form.value.valor.replace(/\D/g, "");

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
  form.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

const toggleDropdown = async () => {
  isDropdownOpen.value = !isDropdownOpen.value;

  // Se acabamos de ABRIR o dropdown...
  if (isDropdownOpen.value) {
    // Espera o Vue renderizar os elementos da lista no DOM.
    await nextTick();

    const container = dropdownContainerRef.value;
    if (container && form.value.fatura) {
      // Procura pelo elemento que tem o 'data-invoice' igual à fatura selecionada.
      const selectedItem = container.querySelector(`[data-invoice="${form.value.fatura}"]`) as HTMLElement;
      
      if (selectedItem) {
        // Usa a mágica do 'scrollIntoView' para centralizar o item.
        selectedItem.scrollIntoView({
          block: 'start', // 'start', 'center', 'end'
          behavior: 'auto' // 'auto' ou 'smooth'
        });
      }
    }
  }
};

const selectInvoice = (invoice: string) => {
  form.value.fatura = invoice;
  isDropdownOpen.value = false;
};

const closeForm = () => emit("close");

const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    // Ajusta o payload para o backend se necessário
    const payload = { ...form.value };
    if (isCreditCard.value) {
      payload.conta_id = selectedCreditCard.value?.conta_pai_id;
    }

    await lancamentoStore.saveLancamento(payload);
    closeForm();
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// ---  LIFECYCLE HOOKS ---
onMounted(() => {
  if (props.isCard && creditCardAccounts.value.length > 0) {
    form.value.cartao_id = creditCardAccounts.value[0].id;
  }

  // Define o valor inicial da fatura no novo formato abreviado.
  const now = new Date();
  const month = now.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace('.', '');
  const year = now.getFullYear();
  form.value.fatura = `${month}/${year}`;
  
  if (props.lancamento) {
    form.value = { ...props.lancamento, valor: formatValue(Number(props.lancamento.valor)) };
  }
});








const menuOpen = ref(false);
const innerValue = computed({
  get: () => props.modelValue,
  set: v => emit("update:modelValue", v),
});
const faturaSelect = ref();






const tipoCalculoParcela = ref<"total" | "parcela">("total");
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

const showEditOptionsModal = ref(false);
const editScope = ref<"apenas esta" | "esta e as próximas" | "todas" | "apenas este mês" | "mês atual e os próximos">("apenas esta");

// --- FORM STATE ---


const tiposRecorrencia = ref<("Não recorrente" | "Fixa" | "Parcelado")[]>([
  "Não recorrente",
  "Fixa",
  "Parcelado",
]);


const incrementParcelaInicial = () => {
  tempParcelaInicial.value++;
};
const decrementParcelaInicial = () => {
  if (tempParcelaInicial.value > 1) {
    tempParcelaInicial.value--;
  }
};
const inicializarValoresTemporarios = () => {
  tempParcelaInicial.value = 1;
  tempNumParcelas.value = 2;
  tempPeriodicidade.value = "Mensal";
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

// Lógica para abrir/fechar o dropdown



// Lógica para selecionar um item e fechar o dropdown


// Define o valor inicial da fatura


// --- COMPUTED PROPERTIES ---


const tiposLancamento = ["Despesa", "Receita", "Cartão de Crédito"];
const isCreditCard = computed(() => form.value.tipo_lancamento === "Cartão de Crédito");



// Lógica para a Conta vinculada (readonly)


// Lógica para gerar a lista de faturas


const salvarLancamentos = async () => {
  errorStore.unsetError();

  if (!isFormValid.value) {
    return;
  }

  // Se for um lançamento recorrente no modo de edição, mostra o modal primeiro.
  if (isEditMode.value && (form.value.recorrencia === "Parcelado" || form.value.recorrencia === "Fixa")) {
    showEditOptionsModal.value = true;
    return; // Para a execução aqui e espera a escolha do utilizador
  }

  // Se não for recorrente, executa a lógica de salvar diretamente
  await proceedWithSave();
};

const openRecorrenciaModal = ref(false);
const openParcelas = ref(false);

const selecionarRecorrencia = (item: "Não recorrente" | "Fixa" | "Parcelado") => {
  form.value.recorrencia = item;
  openRecorrenciaModal.value = false;

  if (item === "Parcelado") {
    // Abre o modal de configuração de parcelas
    openParcelas.value = true;
  } else {
    // Reseta os dados de parcela se não for "Parcelado"
    form.value.num_parcelas = null;
    form.value.periodicidade = null;
  }
};

const detalheRecorrencia = computed(() => {
  if (
    form.value.recorrencia === "Parcelado" &&
    form.value.num_parcelas &&
    form.value.num_parcelas > 0
  ) {
    const valorInput = parseFloat(
      form.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (isNaN(valorInput) || valorInput <= 0) return "";

    // Opções de formatação para garantir 2 casas decimais
    const opcoesDeFormatacao = {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    };

    if (tipoCalculoParcela.value === "total") {
      const valorParcela = valorInput / form.value.num_parcelas;
      const valorFormatado = valorParcela.toLocaleString("pt-BR", opcoesDeFormatacao);
      return `Em ${form.value.num_parcelas}x de R$ ${valorFormatado}`;
    } else {
      const valorFormatado = valorInput.toLocaleString("pt-BR", opcoesDeFormatacao);
      return `Em ${form.value.num_parcelas}x de R$ ${valorFormatado}`;
    }
  }
  return "";
});

const cancelarConfiguracaoRepeticao = () => {
  // Retorna tipo para "Não recorrente"
  form.value.recorrencia = "Não recorrente";
  form.value.num_parcelas = null;
  form.value.periodicidade = null;

  // Fecha o modal
  openParcelas.value = false;
};

const concluirParcelas = () => {
  // Salva os valores temporários nos valores finais
  parcelaInicial.value = tempParcelaInicial.value;
  form.value.num_parcelas = tempNumParcelas.value;
  form.value.periodicidade = tempPeriodicidade.value || null;

  // Fecha o modal
  openParcelas.value = false;
};

const handleEditScopeSelection = async (scope: any) => {
  editScope.value = scope;
  showEditOptionsModal.value = false;
  await proceedWithSave();
};

const proceedWithSave = async () => {
  loading.value = true;
  const payload = { 
    ...form.value,
    editScope: editScope.value,
    mesAno: props.mesAno,
  };
  try {
    
    if (form.value.recorrencia === "Parcelado"  ) {
      if (props.lancamento?.recorrencia === "Parcelado" || props.lancamento?.recorrencia === "Fixa") {
        payload.tipo_parcela = props.lancamento?.tipo_parcela;
        payload.parcela_atual = props.lancamento?.parcela_atual;
      } else {
        payload.tipo_parcela = tipoCalculoParcela.value;
        payload.parcela_atual = parcelaInicial.value;
      }
    }

    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value
      ? `/lancamentos/${payload.id}`
      : "/lancamentos";
      
    const res = await method(url, payload);

    useUser.setMesAno(res.data.mesAno);
    if (props.transactionType === "Receita") {
      emit("updateData", res.data.revenues);
    } else {
      emit("updateData", res.data.expenses);
    }
    useWallets.setWalletsData(res.data.wallets);

    closeForm();
  } catch (error) {
    console.log(error);
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










// Se estiver editando, preenche o formulário

</script>

<style scoped>
/*
  O CONTAINER PRINCIPAL (NOSSO "INPUT")
*/
.fatura-custom-select {
  position: relative; /* Essencial para posicionar o dropdown */
  display: flex;
  align-items: center;
  justify-content: space-between; /* Empurra os lados para os extremos */
  padding: 8px 4px; /* Espaçamento interno */
  border-bottom: 1px solid rgba(255, 255, 255, 0.7); /* A linha de baixo */
  cursor: pointer;
  width: 100%;
  background-color: transparent;
  margin-top: 16px;
}
/* Lado Esquerdo */
.fatura-prefix {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.7);
}
/* Lado Direito */
.fatura-selection {
  display: flex;
  align-items: center;
  color: white;
  font-weight: 500;
}
/*
  A LISTA SUSPENSA (DROPDOWN)
*/
.fatura-dropdown {
  position: absolute;
  top: 100%; /* Posiciona logo abaixo do campo */
  left: 0;
  right: 0;
  background-color: #2c2c2c; /* Cor de fundo escura para a lista */
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  max-height: 250px; /* Altura máxima com scroll */
  overflow-y: auto;
  z-index: 10;
  width: 250px; /* LARGURA FIXA E MENOR */
  margin: 4px auto 0; /* Centraliza a lista se for mais estreita */
}
/* Itens da lista */
.dropdown-item {
  padding: 10px 16px;
  color: white;
  font-size: 0.9rem;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #0c99ed; /* Cor de destaque ao passar o mouse */
}








/* Adiciona o cursor de clique em toda a área do campo */
.fatura-select {
  cursor: pointer;
}

/*
  O TRUQUE PRINCIPAL:
  Forçamos o container interno a ser um flexbox.
*/
:deep(.v-field__field) {
  display: flex;
  align-items: center;
}

/*
  FAZ A MÁGICA DO ALINHAMENTO:
  Dizemos para a área de input (que contém o valor selecionado)
  crescer e ocupar todo o espaço livre.
*/
/* :deep(.v-field__input) {
  flex-grow: 1;
  padding-inline-start: 20px;
} */

/* Estilo para o prefixo (lado esquerdo) */
.fatura-prefix {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.7);
  white-space: nowrap;
  margin-right: 16px; /* Adiciona um espaço entre o prefixo e o valor */
}

/* Estilo para o valor selecionado (lado direito) */
.fatura-selection {
  display: flex;
  /* Esta linha alinha o texto para o final do container */
  justify-content: flex-end;
  width: 100%;
  color: white;
  font-weight: 500;
  text-align: right;
  white-space: nowrap;
}

/*
  AJUSTE DA LARGURA DA LISTA:
  Isso garante que o menu suspenso seja mais estreito.
*/
:deep(.v-overlay__content.v-menu__content) {
  min-width: 200px !important;
  max-width: 250px !important;
}












.container__modal {
  background-color: #1e1e1e;
  color: white;
  height: 100vh;
  width: 100vw;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1005;
}
.form__body {
  padding: 150px 16px 16px 16px;
  overflow-y: auto;
  height: 100vh;
}
.header__items {
  background-color: #1e1e1e;
  width: 100%;
  z-index: 10;
  padding: 10px 0;
}
.close {
  background: transparent;
  color: white;
  box-shadow: none;
}
.btn {
  background-color: #0c99ed;
  color: #1e1e1e;
  text-transform: none;
  font-weight: bold;
}
.brand-icon {
  height: 24px;
  width: auto;
}
.custom__input__container {
  position: relative;
  padding-top: 20px;
  padding-bottom: 4px; /* Espaço antes da linha de baixo */
}
.custom__input__content {
  display: flex;
  align-items: center;
  color: #fff;
  cursor: pointer;
}
.detalhe__parcela__interno {
  font-size: 14px;
  color: #e0e0e0;
  line-height: 1.2;
  margin-top: 4px;
}
.edit__icon {
  color: #77d08e;
}
.parcela__toggle {
  display: flex;
  border-radius: 10px;
  border: 1px solid #4F4F4F;
  background-color: transparent;
  overflow: hidden;
}

.parcela__toggle .toggle__btn {
  flex: 1;
  text-transform: none;
  font-size: 14px;
  color: #bdbdbd;
  background-color: transparent;
}

.parcela__toggle .v-btn--active {
  background-color: #77d08e;
  color: #121212 !important; /* Cor do texto do botão ativo */
  font-weight: bold;
}
.custom__underline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.7);
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
.item__label {
  flex-grow: 1;
  font-size: 18px;
  font-weight: 400;
}
.item__value {
  margin-right: 20px;
  font-size: 18px;
  font-weight: 500;
}
.number__stepper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  width: 120px;
}
.stepper__btn {
  background-color: transparent;
  border: none;
  width: 30px;
  color: #999;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stepper__input {
  width: 50px;
  background-color: transparent;
  border: none;
  color: white;
  text-align: center;
  font-size: 18px;
  -moz-appearance: textfield;
}
.stepper__input::-webkit-outer-spin-button,
.stepper__input::-webkit-inner-spin-button {
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

.custom__input__content {
  display: flex;
  align-items: center;
  color: #fff;
  cursor: pointer;
}

.detalhe__parcela__interno {
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

.modal-recorrente-card {
  background-color: #2c2c2e;
  color: white;
  border-radius: 16px;
}
.modal-option-btn {
  text-transform: none;
  justify-content: start;
  padding: 12px 16px !important;
  min-height: 48px;
  color: #77d08e;
}
</style>

