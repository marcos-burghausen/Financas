<template>
  <div class="container__modal">
    <v-form 
      v-model="isFormValid" 
      class="px-3 w-100 mt-5 pt-4" 
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
              Lançamento no Cartão de Crédito
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
        <v-text-field
          v-model="form.descricao" 
          label="Descrição" 
          variant="underlined" 
          :rules="[rules.required]"
          prepend-inner-icon="mdi-text-long" 
          class="imput mb-5" 
        />
  
        <v-text-field
          v-model="form.valor"
          variant="underlined"
          hide-details="auto"
          label="Valor" 
          type="tel"
          class="imput mb-5"
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
              class="edit-icon"
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
              :class="form.recorrencia === item ? (props.transactionType === 'Receita' ? 'receita' : props.transactionType === 'Despesa' ? 'despesa' : 'cartao') : ''"
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
                        class="stepper__input"
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
                      class="select__dark"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-space-between align-center p-3">
              <v-btn
                class="btn__cancelar"
                @click="cancelarConfiguracaoRepeticao"
              >
                Cancelar
              </v-btn>
              <v-btn
                class="btn__concluido"
                @click="concluirParcelas"
              >
                Concluído
              </v-btn>
            </div>
          </div>
        </div>

        <template v-if="isCard">
          <div class="custom__select__wrapper">
            <span class="custom__select__label">Cartão de Crédito</span>
            <div
              ref="cardSelectRef"
              class="custom__select"
              @click="toggleCardDropdown"
            >
              <div class="prefix">
                <v-icon
                  :icon="getBankIcon(selectedCreditCard?.name)"
                  size="25"
                  class="me-2"
                />
                <span>{{ selectedCreditCard?.name || 'Selecione' }}</span>
              </div>
              <div class="selection">
                <v-icon
                  :icon="getBankIcon(selectedCreditCard?.bandeira)"
                  size="20"
                  class="ms-2"
                />
                <v-icon
                  icon="mdi-menu-down"
                  size="20"
                  class="ms-2"
                />
              </div>
              <div
                v-if="isCardDropdownOpen"
                ref="cardDropdownContainerRef"
                class="dropdown"
              >
                <div
                  v-for="card in props.creditCards"
                  :key="card.id"
                  class="dropdown__item"
                  :class="{ 'is__selected': card.id === form.cartao_id }"
                  :data-card-id="card.id"
                  @click.stop="selectCard(card)"
                >
                  <div class="dropdown__item__content">
                    <v-icon
                      :icon="getBankIcon(card.name)"
                      size="25"
                      class="me-2"
                    />
                    <span>{{ card.name }}</span>
                  </div>
                  <v-icon
                    :icon="getBankIcon(card.bandeira)"
                    size="20"
                  />
                </div>
              </div>
            </div>
          </div>

          <div
            ref="faturaSelectRef"
            class="custom__select"
            @click="toggleFaturaDropdown"
          >
            <div class="prefix">
              <v-icon
                icon="mdi-calendar"
                size="20"
                class="me-2"
              />
              <span>Fatura</span>
            </div>
            <div class="selection">
              <span>{{ form.fatura || 'Selecione' }}</span>
              <v-icon
                icon="mdi-menu-down"
                size="20"
                class="ms-2"
              />
            </div>
            <div
              v-if="isFaturaDropdownOpen"
              ref="faturaDropdownContainerRef"
              class="dropdown"
            >
              <div
                v-for="item in invoiceList"
                :key="item"
                class="dropdown__item"
                :class="{ 'is__selected': item === form.fatura }"
                :data-invoice="item"
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
          class="imput mb-5"
          :prepend-inner-icon="getBankIcon(linkedAccount?.name)"
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
          class="imput mb-5"
        />

        <v-autocomplete
          v-model="form.categoria"
          :items="categoriasNames"
          :rules="[rules.required]"
          label="Categoria"
          variant="underlined"
          class="imput mb-5"
          >
          <!-- item-title="name"
          item-value="name" -->
          <template #prepend-inner>
            <v-icon
              :icon="categoriaIcon"
              :class="categoriaColorClass"
            />
          </template>
        </v-autocomplete>
        
        <v-autocomplete
          v-model="form.subcategoria"
          class="imput mb-5"
          label="Subcategoria"
          :items="subcategoriasDaCategoriaSelecionada"
          variant="underlined"
          item-title="name"
          >
          <!-- item-value="name" -->
          <template #prepend-inner>
            <v-icon
              :icon="subcategoriaIcon"
              :class="subcategoriaColorClass"
            />
          </template>
        </v-autocomplete>
        
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
// --- 1. IMPORTS ---
import { useExpensesStore, useRevenuesStore, useWalletsStore } from "@/store";
import { useLancamentoStore } from "@/store/lancamentos";
import type { CategoryData, Lancamento, Wallet } from "@/types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon } from "@/utils/iconMapper";
import { onClickOutside } from "@vueuse/core";
import { computed, nextTick, onMounted, ref, watch } from "vue";

// --- 2. PROPS & EMITS ---
const props = defineProps<{
  creditCards: Wallet[];
  transactionType: "Receita" | "Despesa";
  isCard?: boolean;
  lancamento?: Lancamento;
}>();
const emit = defineEmits(["closeForm"]);

// --- 3. STORES ---
const walletsStore = useWalletsStore();
const lancamentoStore = useLancamentoStore();
const expensesStore = useExpensesStore();
const revenuesStore = useRevenuesStore();

// --- 4. STATE ---
const form = ref<Partial<Lancamento & { cartao_id: number | null, fatura: string | null }>>({
  tipo_lancamento: props.transactionType,
  descricao: "",
  valor: "0,00",
  conta_id: props.creditCards.length > 0 ? props.creditCards[0].id : null,
  cartao_id: null,
  fatura: "",
  data_lancamento: new Date().toISOString().split("T")[0],
  recorrencia: "Não recorrente",
  status_lancamento: props.isCard ? "Efetivada" : "Pendente",
  categoria: props.lancamento?.categoria || "Outros",
  subcategoria: props.lancamento?.subcategoria || "Outros",
});
const loading = ref(false);
const isFormValid = ref(false);
const isEditMode = computed(() => !!props.lancamento?.id);

// State para o dropdown de Fatura
const isFaturaDropdownOpen = ref(false);
const faturaSelectRef = ref(null);
const faturaDropdownContainerRef = ref<HTMLElement | null>(null);

// State para o dropdown de Cartão de Crédito
const isCardDropdownOpen = ref(false);
const cardSelectRef = ref(null);
const cardDropdownContainerRef = ref<HTMLElement | null>(null);

const openRecorrenciaModal = ref(false);
const openParcelas = ref(false);
const tipoCalculoParcela = ref<"total" | "parcela">("total");
const tiposRecorrencia = ref<("Não recorrente" | "Fixa" | "Parcelado")[]>([
  "Não recorrente",
  "Fixa",
  "Parcelado",
]);
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

// --- 5. LÓGICA DE INTERAÇÃO ---
onClickOutside(faturaSelectRef, () => { isFaturaDropdownOpen.value = false; });
onClickOutside(cardSelectRef, () => { isCardDropdownOpen.value = false; });

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

// --- 6. COMPUTED PROPERTIES ---
// const creditCardAccounts = computed<Wallet[]>(() => walletsStore.walletsData.cartoes || []);
const availableBankAccounts = computed<Wallet[]>(() => walletsStore.walletsData.contas || []);

const selectedCreditCard = computed(() => {
  if (!form.value.conta_id) return null;
  return props.creditCards.find(c => c.id === form.value.cartao_id);
});

const linkedAccount = computed(() => {
  if (!selectedCreditCard.value || !selectedCreditCard.value.conta_pai_id) return null;
  return availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
});

const linkedAccountName = computed(() => {
    if (!selectedCreditCard.value || !selectedCreditCard.value.conta_pai_id) return "Nenhuma conta vinculada";
    const conta = availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
    return conta?.name || "Conta não encontrada";
  });

  // Busca a lista de categorias correta (Receita ou Despesa)
  const categoriesSource = computed(() =>
    props.transactionType === "Receita"
      ? revenuesStore.revenuesData?.categories
      : expensesStore.expensesData?.categories
  );

  // 2. Cria uma lista apenas com os nomes das categorias para o v-autocomplete
  const categoriasNames = computed(() => {
    if (categoriesSource.value) {
      return categoriesSource.value.map((cat) => cat.name);
    }
    return [];
  });

  // 3. Encontra o objeto completo da categoria que foi selecionada
  const selectedCategoryObject = computed(() => {
    if (!categoriesSource.value || !form.value.categoria) {
      return null;
    }
    return categoriesSource.value.find(
      (cat) => cat.name === form.value.categoria
    );
  });

  // 4. A PARTIR do objeto da categoria, extrai a lista de subcategorias
  const subcategoriasDaCategoriaSelecionada = computed(() => {
    if (selectedCategoryObject.value && selectedCategoryObject.value.subcategories) {
      return selectedCategoryObject.value.subcategories.map((sub) => sub.name);
    }
    return []; // Retorna vazio se não houver categoria ou subcategorias
  });
  console.log(subcategoriasDaCategoriaSelecionada.value);

  // 5. Encontra o objeto completo da subcategoria selecionada
  const selectedSubcategoryObject = computed(() => {
    if (!selectedCategoryObject.value?.subcategories || !form.value.subcategoria) {
      return null;
    }
    return selectedCategoryObject.value.subcategories.find(
      (sub) => sub.name === form.value.subcategoria
    );
  });
  // 6. Usa os objetos encontrados para pegar ícones e cores
  const categoriaIcon = computed(() => selectedCategoryObject.value?.icon || 'mdi-shape-outline');
  const categoriaColorClass = computed(() => selectedCategoryObject.value?.color || '');
  const subcategoriaIcon = computed(() => selectedSubcategoryObject.value?.icon || 'mdi-shape-outline');
  const subcategoriaColorClass = computed(() => selectedSubcategoryObject.value?.color || '');



const availableCategories = computed<CategoryData[]>(() => {
  if (props.transactionType === "Receita") {
    return revenuesStore.revenuesData.categories;
  } else if (props.transactionType === "Despesa") {
    return expensesStore.expensesData.categories;
  }
  return [];
});
console.log("linha: 616", availableCategories.value);


const invoiceList = computed(() => {
  const list = [];
  const now = new Date();
  const formatDate = (date: Date) => {
    const month = date.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace(".", "");
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

watch(() => form.value.categoria, () => {
  form.value.subcategoria = '';
});

// --- MÉTODOS ---
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
  let digits = String(form.value.valor).replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  form.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

const findLinkedAccountFor = (card: Wallet) => {
  if (!card.conta_pai_id) return null;
  return availableBankAccounts.value.find(acc => acc.id === card.conta_pai_id);
};

// Métodos para o seletor de Fatura
const toggleFaturaDropdown = async () => {
  isFaturaDropdownOpen.value = !isFaturaDropdownOpen.value;
  if (isFaturaDropdownOpen.value) {
    await nextTick();
    const container = faturaDropdownContainerRef.value;
    if (container && form.value.fatura) {
      const selectedItem = container.querySelector(`[data-invoice="${form.value.fatura}"]`) as HTMLElement;
      if (selectedItem) {
        selectedItem.scrollIntoView({ block: "start", behavior: "auto" });
      }
    }
  }
};

const selectInvoice = (invoice: string) => {
  form.value.fatura = invoice;
  isFaturaDropdownOpen.value = false;
};

// Métodos para o seletor de Cartão
const toggleCardDropdown = async () => {
  isCardDropdownOpen.value = !isCardDropdownOpen.value;
  if (isCardDropdownOpen.value) {
    await nextTick();
    const container = cardDropdownContainerRef.value;
    if (container && form.value.cartao_id) {
      const selectedItem = container.querySelector(`[data-card-id="${form.value.cartao_id}"]`) as HTMLElement;
      if (selectedItem) {
        selectedItem.scrollIntoView({ block: "start", behavior: "auto" });
      }
    }
  }
};

const selectCard = (card: Wallet) => {
  form.value.cartao_id = card.id;
  isCardDropdownOpen.value = false;
};

const closeForm = () => emit("closeForm");
const submitForm = async () => {
  if (!isFormValid.value) return;
  loading.value = true;
  try {
    const payload = { ...form.value };
    if (props.isCard) {
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

// --- 8. LIFECYCLE HOOKS ---
onMounted(() => {
  if (props.isCard && props.creditCards.length > 0) {
    form.value.cartao_id = props.creditCards[0].id;
  }
  const now = new Date();
  const month = now.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace(".", "");
  const year = now.getFullYear();
  form.value.fatura = `${month}/${year}`;
  
  if (props.lancamento) {
    form.value = { ...props.lancamento, valor: formatValue(Number(props.lancamento.valor)) };
  }
});
</script>

<style scoped>
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
.form__body {
  padding: 80px 16px 16px 16px;
  overflow-y: auto;
  height: 100vh;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
}
.custom__input__container {
  position: relative;
  padding-top: 10px;
  padding-bottom: 4px;
}
.custom__input__content {
  display: flex;
  align-items: center;
  color: #fff;
}
.detalhe__parcela__interno {
  font-size: 14px;
  color: #e0e0e0;
  line-height: 1.2;
  margin-top: 4px;
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
.custom__underline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.7);
}
.modal__tipo {
  background: #2c2c2e;
  color: #fefefe;
  height: 200px;
  border-radius: 20px;
  padding: 15px;
}
.despesa {
  color: #ed0c0c;
}
.receita {
  color: #77d08e;
}
.cartao {
  color: #0c99ed;
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
.divider {
  height: 1px;
  background-color: #333;
  margin: 5px 0;
}
.select__dark {
  color: white;
  width: 120px;
  text-align: right;
}
.btn__cancelar {
  color: #db4646;
  background-color: transparent;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.btn__concluido {
  background-color: #77d08e;
  color: white;
  border-radius: 25px;
  font-size: 16px;
  padding: 0 30px;
  height: 45px;
}
.custom__select__wrapper {
  position: relative;
  margin-top: 16px;
  padding-top: 8px;
}
.custom__select__label {
  position: absolute;
  top: 0;
  left: 4px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}
.custom__select {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 4px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  cursor: pointer;
  width: 100%;
  background-color: transparent;
  margin-bottom: 24px;
}
.selection {
  display: flex;
  align-items: center;
  color: white;
  font-weight: 500;
}
.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #2c2c2c;
  border-radius: 4px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  max-height: 250px;
  overflow-y: auto;
  z-index: 10;
  width: 200px;
  /* margin: 4px auto 0; */
  margin-left: calc(100% - 200px);
}
.dropdown__item {
  display: flex;
  align-items: center;
  justify-content: space-between; /* Para separar os ícones */
  padding: 10px 16px;
  color: white;
  font-size: 0.9rem;
  transition: background-color 0.2s ease;
  border-radius: 4px;
  margin: 2px 4px;
}
.dropdown__item.is__selected {
  background-color: #0c99ed;
  font-weight: bold;
}
.dropdown__item__content {
  display: flex;
  align-items: center;
}
.parcela__toggle .v-btn--active {
  background-color: #77d08e;
  color: #121212 !important;
  font-weight: bold;
}
.stepper__input::-webkit-outer-spin-button,
.stepper__input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

</style>