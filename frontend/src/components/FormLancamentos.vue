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

          <v-row>
            <v-col
              class="p-0 pt-3"
              cols="6"
            >
              <!-- <div class="d-flex align-center"> -->
              <v-icon
                icon="mdi-calendar"
                size="25"
                class="ms-2"
              />
              <span>Fatura</span>
              <!-- </div> -->
            </v-col>
            <v-col
              class="p-0"
              cols="6"
            >
              <v-select
                v-model="form.fatura"
                :items="invoiceList"
                variant="solo"
                class=""
              />
            </v-col>
          </v-row>
          <v-select
            v-model="form.fatura"
            :items="invoiceList"
            item-title="label"
            item-value="value"
            variant="underlined"
            class="mt-4 fatura-select"
            hide-detail
            aria-label="Fatura"
            :menu-props="{
              contentClass: 'fatura-menu',
              maxHeight: 320
            }"
            menu-icon="mdi-chevron-down"
          >
            <!-- Prefixo fixo: ícone + Fatura -->
            <template #prepend-inner>
              <div class="fatura-prefix">
                <v-icon
                  size="20"
                  class="mr-1"
                >
                  mdi-receipt
                </v-icon>
                <span class="fatura-prefix__label">Fatura</span>
              </div>
            </template>

            
            <!-- Valor selecionado (mês/ano) alinhado à direita -->
            <template #selection="{ item }">
              <div class="fatura-selection">
                <span class="fatura-selection__text">
                  {{ item?.title ?? item?.raw?.label ?? '' }}
                </span>
              </div>
            </template>

            
            <!-- (Opcional) Customização dos itens no menu -->
            <!-- <template #item="{ props, item }">
              <v-list-item v-bind="props">
                <v-list-item-title class="text-body-2">
                  {{ item?.title }}
                </v-list-item-title>
              </v-list-item>
            </template> -->
          </v-select>
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
import { computed, onMounted, ref } from "vue";

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

const menuOpen = ref(false);
const innerValue = computed({
  get: () => props.modelValue,
  set: v => emit("update:modelValue", v),
});

const faturaSelect = ref();


const emit = defineEmits(["close"]);

// --- STORES ---
const walletsStore = useWalletsStore();
const lancamentoStore = useLancamentoStore();
const errorStore = useErrorStore();

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
const form = ref<Partial<Lancamento & { cartao_id: number | null, fatura: string | null }>>({
  tipo_lancamento: props.transactionType || "Despesa",
  descricao: "",
  valor: formatValue(Number(props.lancamento?.valor)) || "0,00",
  conta_id: null,
  cartao_id: null,
  fatura: null,
  data_lancamento: new Date().toISOString().split("T")[0],
  recorrencia: "Não recorrente",
  categoria: "",
  subcategoria: "",
  status_lancamento: props.isCard ? "Efetivada" : "Pendente",
});

const tiposRecorrencia = ref<("Não recorrente" | "Fixa" | "Parcelado")[]>([
  "Não recorrente",
  "Fixa",
  "Parcelado",
]);

const loading = ref(false);
const isFormValid = ref(false);
const isEditMode = computed(() => !!props.lancamento?.id);
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

// --- COMPUTED PROPERTIES ---

const formTitle = computed(() => `${props.lancamento ? "Editar" : "Novo(a)"} ${form.value.tipo_lancamento}`);
const tiposLancamento = ["Despesa", "Receita", "Cartão de Crédito"];
const isCreditCard = computed(() => form.value.tipo_lancamento === "Cartão de Crédito");

// Listas de Contas e Cartões
const creditCardAccounts = computed<Wallet[]>(() => walletsStore.walletsData.cartoes || []);
const availableBankAccounts = computed<Wallet[]>(() => walletsStore.walletsData.contas || []);

// Lógica para o Cartão de Crédito selecionado
const selectedCreditCard = computed(() => {
  if (!form.value.cartao_id) return null;
  return creditCardAccounts.value.find(c => c.id === form.value.cartao_id);
});

// Lógica para a Conta vinculada (readonly)
const linkedAccountName = computed(() => {
  if (!selectedCreditCard.value || !selectedCreditCard.value.conta_pai_id) return "Nenhuma conta vinculada";
  const conta = availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
  return conta?.name || "Conta não encontrada";
});

// Lógica para gerar a lista de faturas
const invoiceList = computed(() => {
  const list = [];
  const now = new Date();
  const options: Intl.DateTimeFormatOptions = { month: "long", year: "numeric" };

  // Adiciona 12 meses para trás
  for (let i = 12; i > 0; i--) {
    const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
    list.push(date.toLocaleDateString("pt-BR", options));
  }

  // Adiciona o mês atual e 12 meses para frente
  for (let i = 0; i <= 12; i++) {
    const date = new Date(now.getFullYear(), now.getMonth() + i, 1);
    list.push(date.toLocaleDateString("pt-BR", options));
  }
  return list;
});

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

// Lógica para Categorias e Subcategorias
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



// --- LIFECYCLE HOOKS ---
onMounted(() => {
  // Inicializa o formulário para Cartão de Crédito se for o caso
  if (props.isCard && creditCardAccounts.value.length > 0) {
    form.value.cartao_id = creditCardAccounts.value[0].id;
  }
  // Define a fatura atual como padrão
  form.value.fatura = new Date().toLocaleDateString("pt-BR", { month: "long", year: "numeric" });
});

// --- MÉTODOS ---
const rules = {
  required: (value: any) => !!value || "Campo obrigatório.",
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

// Se estiver editando, preenche o formulário
if (props.lancamento) {
  form.value = { ...props.lancamento, valor: props.lancamento.valor / 100 };
}
</script>

<style scoped>

/* Prefixo não bloqueia o clique para abrir o select */
.fatura-select :deep(.v-field__prepend-inner) { pointer-events: none; }

.fatura-prefix {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.fatura-prefix__label {
  font-size: 0.9rem;
  color: rgba(var(--v-theme-on-surface), 0.7);
}

/* Campo do select em flex para permitir empurrar o valor p/ direita */
.fatura-select :deep(.v-field__input) {
  display: flex;
  align-items: center;
}

/* Empurra o valor selecionado (mês/ano) para a direita */
.fatura-select :deep(.fatura-selection) {
  margin-left: auto;
}

.fatura-select :deep(.fatura-selection__text) {
  font-weight: 600;
  color: rgba(var(--v-theme-on-surface), 0.95);
}

/* (Opcional) itens do menu mais compactos */
:deep(.fatura-menu .v-list-item) {
  min-height: 36px;
}















-- Estilos internos (podem ser scoped) -->
<style scoped>
.fatura-field {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 0 6px;
  border-bottom: 1px solid rgba(var(--v-theme-outline), .5);
}

/* Esquerda fixa */
.fatura-left {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.fatura-left__label {
  font-size: 0.9rem;
  color: rgba(var(--v-theme-on-surface), 0.7);
}

/* Direita (select) */
.fatura-right {
  margin-left: auto;     /* empurra o select para a direita */
  min-width: 0;
  display: inline-flex;
  align-items: center;
}

/* Deixa o select bem compacto e "flat" para parecer embutido */
.fatura-select-compact :deep(.v-field__outline),
.fatura-select-compact :deep(.v-field__prepend-inner),
.fatura-select-compact :deep(.v-field__append-inner) {
  display: none !important;
}
.fatura-select-compact :deep(.v-field__input) {
  padding: 0 !important;
  min-height: unset !important;
}
.fatura-select-compact :deep(.v-field__overlay) { display: none; }
.fatura-select-compact :deep(.v-field) { --v-input-padding-top: 0; --v-input-padding-bottom: 0; }

.fatura-value {
  font-weight: 600;
  color: rgba(var(--v-theme-on-surface), 0.95);
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

