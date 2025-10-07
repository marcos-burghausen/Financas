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
        <div class="d-flex align-items-center">
          <v-btn
            :disabled="loading"
            class="close fs-5 ms-2"
            prepend-icon="mdi-close"
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
          :disabled="
            loading || !validFormLancamentos || formReleases.valor === '0,00'
          "
          :loading="loading"
          class="btn m-0 me-3 p-0 px-2"
          :class="(props.transactionType === 'Receita' ? 'btn__receita' : props.transactionType === 'Despesa' && !props.isCard ? 'btn__despesa' : 'btn__cartao')"
          type="submit"
          rounded="xl"
        >
          Salvar
        </v-btn>
      </div>

      <div class="form__body">
        <v-text-field
          v-model="formReleases.descricao"
          label="Descrição"
          variant="underlined"
          hide-details="auto"
          required
          class="mb-3 imput"
          :rules="[rules.requiredDescricao]"
          prepend-inner-icon="mdi-text-long"
        />

        <v-text-field
          v-model="formReleases.valor"
          variant="underlined"
          hide-details="auto"
          type="tel"
          class="mb-3 imput"
          :rules="[rules.requiredValor, rules.requiredValorMaiorQue0]"
          prepend-inner-icon="mdi-currency-usd"
          @input="formatValueSave"
        />

      <div
        v-if="props.releases?.recorrencia === 'Não recorrente' || !isEditMode"
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
            <span>{{ formReleases.recorrencia }}</span>
            <span
              v-if="detalheRecorrencia"
              class="detalhe__parcela__interno"
            >
              {{ detalheRecorrencia }}
            </span>
          </div>
          <v-spacer />
          <v-icon
            v-if="formReleases.recorrencia === 'Parcelado'"
            icon="mdi-pencil"
            size="x-small"
            class="edit__icon"
            @click.stop="openParcelas = true"
          />
        </div>

        <v-btn-toggle
          v-if="formReleases.recorrencia === 'Parcelado'"
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
            :class="formReleases.recorrencia === item ? (props.transactionType === 'Receita' ? 'receita' : props.transactionType === 'Despesa' && !props.isCard ? 'despesa' : 'cartao') : ''"
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
                  v-for="card in props.wallets"
                  :key="card.id"
                  class="dropdown__item"
                  :class="{ 'is__selected': card.id === formReleases.cartao_id }"
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
              <span>{{ formReleases.fatura || 'Selecione' }}</span>
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
                :class="{ 'is__selected': item === formReleases.fatura }"
                :data-invoice="item"
                @click.stop="selectInvoice(item)"
              >
                {{ item }}
              </div>
            </div>
          </div>
        </template>

      <div class="mb-2 pt-1">
        <v-menu
          v-model="menuDataVencimento"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div
              class="custom__display__input"
              v-bind="props"
            >
              <div class="d-flex align-center text-grey">
                <v-icon
                  icon="mdi-calendar"
                  class="me-3"
                />
                <span>Data de vencimento</span>
              </div>
              <v-spacer class="m-0 p-0" />
              <span class="font-weight-medium">{{ displayDataVencimento }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataVencimento"
            color="#77d08e"
            hide-header
            show-adjacent-months
            @update:model-value="menuDataVencimento = false"
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
          <v-icon
            :icon="categoriaIcon"
            :class="categoriaColorClass"
          />
        </template>
      </v-autocomplete>

      <v-autocomplete
        v-model="formReleases.subcategoria"
        :items="subcategoriasDaCategoriaSelecionada"
        label="Subcategoria"
        variant="underlined"
        class="mb-6 imput"
      >
        <template #prepend-inner>
          <v-icon
            :icon="subcategoriaIcon"
            :class="subcategoriaColorClass"
          />
        </template>
      </v-autocomplete>

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
        class="mb-6"
      >
        <v-menu
          v-model="menuDataLancamento"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div
              class="custom__display__input"
              v-bind="props"
            >
              <div class="d-flex align-center text-grey">
                <v-icon
                  icon="mdi-calendar"
                  class="me-3"
                />
                <span>Data de lançamento</span>
              </div>
              <v-spacer />
              <span class="font-weight-medium">{{ displayDataLancamento }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataLancamento"
            color="#77d08e"
            hide-header
            show-adjacent-months
            @update:model-value="menuDataLancamento = false"
          />
        </v-menu>
      </div>
      
      <div
        v-if="informacoes"
        class="mb-1"
      >
        <v-menu
          v-model="menuDataEfetivacao"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
        >
          <template #activator="{ props }">
            <div
              class="custom__display__input"
              v-bind="props"
            >
              <div class="d-flex align-center text-grey">
                <v-icon
                  icon="mdi-calendar"
                  class="me-3"
                />
                <span>Data de efetivação</span>
              </div>
              <v-spacer />
              <span class="font-weight-medium">{{ displayDataEfetivacao }}</span>
            </div>
          </template>

          <v-date-picker
            v-model="formReleases.dataEfetivacao"
            color="#77d08e"
            hide-header
            show-adjacent-months
            @update:model-value="menuDataEfetivacao = false"
          />
        </v-menu>
      </div>
      </div>
    </v-form>
    <v-dialog
      v-model="showEditOptionsModal"
      persistent
      max-width="320"
    >
      <v-card class="modal__recorrente__card">
        <v-card-title class="headline">
          {{ formReleases.recorrencia === 'Parcelado' ? 'Receita Recorrente' : 'Alterar o valor' }}
        </v-card-title>

        <v-card-text v-if="formReleases.recorrencia === 'Fixa'">
          {{ formReleases.recorrencia === 'Fixa' ? 'Essa é uma transação fixa. Você pode escolher como deseja considerar a alteração do valor.' : '' }}
        </v-card-text>

        <v-card-actions class="d-flex flex-column align-stretch">
          <!-- Opções para Lançamento Parcelado -->
          <template v-if="formReleases.recorrencia === 'Parcelado'">
            <v-btn
              block
              class="modal__option__btn"
              @click="handleEditScopeSelection('apenas esta')"
            >
              Atualizar apenas esta
            </v-btn>
            <v-btn
              block
              class="modal__option__btn"
              @click="handleEditScopeSelection('esta e as próximas')"
            >
              Atualizar esta e as próximas
            </v-btn>
            <v-btn
              block
              class="modal__option__btn"
              @click="handleEditScopeSelection('todas')"
            >
              Atualizar todas
            </v-btn>
          </template>

          <!-- Opções para Lançamento Fixo -->
          <template v-if="formReleases.recorrencia === 'Fixa'">
            <v-btn
              block
              class="modal__option__btn"
              @click="handleEditScopeSelection('apenas este mês')"
            >
              Apenas este mês
            </v-btn>
            <v-btn
              block
              class="modal__option__btn"
              @click="handleEditScopeSelection('mês atual e os próximos')"
            >
              Mês atual e os próximos
            </v-btn>
          </template>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
  <ErrorsForm />
  <ErrorMessage />
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import http from "@/services/http";
import {
  useErrorStore,
  useExpensesStore,
  useRevenuesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";
import type { ApiErrorResponse, Lancamento, Wallet } from "@/types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon } from "@/utils/iconMapper";
import { onClickOutside } from "@vueuse/core";
import type { AxiosError } from "axios";
import { format, format as formatDate, isTomorrow, isValid, isYesterday, parseISO } from "date-fns";
import { ptBR } from "date-fns/locale";
import { computed, nextTick, ref, watch } from "vue";

const walletsStore = useWalletsStore();
const useRevenues = useRevenuesStore();
const useExpenses = useExpensesStore();
const useUser = useUserStore();
const errorStore = useErrorStore();
const showEditOptionsModal = ref(false);
const editScope = ref<"apenas esta" | "esta e as próximas" | "todas" | "apenas este mês" | "mês atual e os próximos">("apenas esta");

const emit = defineEmits(["updateData", "closeForm"]);
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);

// State para o dropdown de Fatura
const isFaturaDropdownOpen = ref(false);
const faturaSelectRef = ref(null);
const faturaDropdownContainerRef = ref<HTMLElement | null>(null);

// State para o dropdown de Cartão de Crédito
const isCardDropdownOpen = ref(false);
const cardSelectRef = ref(null);
const cardDropdownContainerRef = ref<HTMLElement | null>(null);

const props = defineProps<{
  releases?: Lancamento;
  wallets: Wallet[];
  rota: string;
  mesAno: string;
  transactionType: "Receita" | "Despesa";
  isCard?: boolean;
}>();

const validateDate = (date: string | Date | undefined): string => {
  if (!date) {
    console.log(`Date inválida: ${date}`);
    return formatDate(new Date(), "yyyy-MM-dd");
  }
  let parsedDate: Date;
  if (typeof date === "string") {
    const parts = date.split("-").map(Number);
    parsedDate = new Date(date + "T00:00:00");
  } else {
    parsedDate = date;
  }
  if (!isValid(parsedDate)) {
    return formatDate(new Date(), "yyyy-MM-dd");
  }
  return formatDate(parsedDate, "yyyy-MM-dd");
};

const formReleases = ref<Partial<Lancamento>>({
  id: props.releases?.id || null,
  descricao: props.releases?.descricao || "",
  valor: formatValue(Number(props.releases?.valor)) || "0,00",
  tipo_lancamento: props.transactionType,
  recorrencia: props.releases?.recorrencia || "Não recorrente",
  parcela_atual: props.releases?.parcela_atual || null,
  num_parcelas: props.releases?.num_parcelas || null,
  tipo_parcela: props.releases?.tipo_parcela || null,
  periodicidade: props.releases?.periodicidade || null,
  data_vencimento: validateDate(props.releases?.data_vencimento),
  status_lancamento: props.releases?.status_lancamento || "Pendente",
  categoria: props.releases?.categoria || "Outros",
  subcategoria: props.releases?.subcategoria || "Outros",
  conta_id: props.wallets.length > 0 ? props.wallets[0].id : null,
  data_lancamento: validateDate(props.releases?.data_lancamento),
  data_efetivacao: props.releases?.data_efetivacao || null,
  cartao_id: null,
});

let valorParcela = ref<string | null>(null);
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
// --- 5. LÓGICA DE INTERAÇÃO ---
onClickOutside(faturaSelectRef, () => { isFaturaDropdownOpen.value = false; });
onClickOutside(cardSelectRef, () => { isCardDropdownOpen.value = false; });
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
  formReleases.value.status_lancamento =
    formReleases.value.status_lancamento === "Efetivada" ? "Pendente" : "Efetivada";
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

// Métodos para o seletor de Fatura
const toggleFaturaDropdown = async () => {
  isFaturaDropdownOpen.value = !isFaturaDropdownOpen.value;
  if (isFaturaDropdownOpen.value) {
    await nextTick();
    const container = faturaDropdownContainerRef.value;
    if (container && formReleases.value.fatura) {
      const selectedItem = container.querySelector(`[data-invoice="${formReleases.value.fatura}"]`) as HTMLElement;
      if (selectedItem) {
        selectedItem.scrollIntoView({ block: "start", behavior: "auto" });
      }
    }
  }
};

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

const selectInvoice = (invoice: string) => {
  formReleases.value.fatura = invoice;
  isFaturaDropdownOpen.value = false;
};

// Métodos para o seletor de Cartão
const toggleCardDropdown = async () => {
  isCardDropdownOpen.value = !isCardDropdownOpen.value;
  if (isCardDropdownOpen.value) {
    await nextTick();
    const container = cardDropdownContainerRef.value;
    if (container && formReleases.value.cartao_id) {
      const selectedItem = container.querySelector(`[data-card-id="${formReleases.value.cartao_id}"]`) as HTMLElement;
      if (selectedItem) {
        selectedItem.scrollIntoView({ block: "start", behavior: "auto" });
      }
    }
  }
};

const selectCard = (card: Wallet) => {
  formReleases.value.cartao_id = card.id;
  isCardDropdownOpen.value = false;
};

const categoriasNames = computed(() => {
  if (props.transactionType === "Receita") {
    return useRevenues.revenuesData?.categories.map((cat) => cat.name) || [];
  } else {
    return useExpenses.expensesData?.categories.map((cat) => cat.name) || [];
  }
});


// const contasNames = ref(useWallets.walletsData.contasNames);
const isEditMode = computed(() => !!props.releases?.id);
console.log(props.releases?.recorrencia);

const isToday = (dateValue: string | Date | undefined | null): boolean => {
  if (!dateValue) return false;

  // Pega a data de hoje, já formatada corretamente.
  const todayStr = formatDate(new Date(), "yyyy-MM-dd");

  let selectedDateStr: string;

  if (typeof dateValue === "string") {
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
  if (!formReleases.value.data_vencimento) return "Selecione...";

  if (typeof formReleases.value.data_vencimento !== "string") return "";
  const data = parseISO(formReleases.value.data_vencimento);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return "Hoje";
  if (isYesterday(data)) return "Ontem";
  if (isTomorrow(data)) return "Amanhã";
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, "EEEE", { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, "dd/MM/yyyy");

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const displayDataLancamento = computed(() => {
  // Se não houver data, não mostre nada.
  if (!formReleases.value.data_lancamento) return "Selecione...";

  if (typeof formReleases.value.data_lancamento !== "string") return "";
  const data = parseISO(formReleases.value.data_lancamento);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return "Hoje";
  if (isYesterday(data)) return "Ontem";
  if (isTomorrow(data)) return "Amanhã";
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, "EEEE", { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, "dd/MM/yyyy");

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const displayDataEfetivacao = computed(() => {
  // Se não houver data, não mostre nada.
  if (!formReleases.value.data_efetivacao) return null;

  if (typeof formReleases.value.data_efetivacao !== "string") return "";
  const data = parseISO(formReleases.value.data_efetivacao);

  // Compara com a data atual e retorna o texto correspondente
  if (isToday(data)) return "Hoje";
  if (isYesterday(data)) return "Ontem";
  if (isTomorrow(data)) return "Amanhã";
  
  // Pega o nome do dia da semana (ex: "segunda-feira")
  const nomeDiaCompleto = format(data, "EEEE", { locale: ptBR });
  
  // Pega as 3 primeiras letras e capitaliza a primeira
  const nomeDiaAbreviado = nomeDiaCompleto.substring(0, 3);
  const diaAbreviadoCapitalizado = nomeDiaAbreviado.charAt(0).toUpperCase() + nomeDiaAbreviado.slice(1);

  // Formata o resto da data
  const dataFormatada = format(data, "dd/MM/yyyy");

  // Retorna no formato "Seg., 25/07/2025"
  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
});

const isTodayVencimento = computed(() =>
  isToday(formReleases.value.data_vencimento)
);

const isTodayLancamento = computed(() =>
  isToday(formReleases.value.data_lancamento)
);
const isTodayEfetivacao = computed(() =>
  isToday(formReleases.value.data_efetivacao)
);

const tipoCalculoParcela = ref<"total" | "parcela">("total");



const detalheRecorrencia = computed(() => {
  if (
    formReleases.value.recorrencia === "Parcelado" &&
    formReleases.value.num_parcelas &&
    formReleases.value.num_parcelas > 0
  ) {
    const valorInput = parseFloat(
      formReleases.value.valor.replace(/\./g, "").replace(",", ".")
    );
    if (isNaN(valorInput) || valorInput <= 0) return "";

    // Opções de formatação para garantir 2 casas decimais
    const opcoesDeFormatacao = {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    };

    if (tipoCalculoParcela.value === "total") {
      const valorParcela = valorInput / formReleases.value.num_parcelas;
      const valorFormatado = valorParcela.toLocaleString("pt-BR", opcoesDeFormatacao);
      return `Em ${formReleases.value.num_parcelas}x de R$ ${valorFormatado}`;
    } else {
      const valorFormatado = valorInput.toLocaleString("pt-BR", opcoesDeFormatacao);
      return `Em ${formReleases.value.num_parcelas}x de R$ ${valorFormatado}`;
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
const selectedCategoryObject = computed(() => {
  return categoriesSource.value.find(
    (cat) => cat.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") === (formReleases.value.categoria || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  );
});

// Encontra o objeto da subcategoria selecionada
const selectedSubcategoryObject = computed(() =>
  selectedCategoryObject.value?.subcategories?.find(
    (sub) => sub.name === formReleases.value.subcategoria
  )
);


// Retorna o ícone e a cor para a CATEGORIA
const categoriaIcon = computed(() => selectedCategoryObject.value?.icon || "mdi-scatter-plot");

const categoriaColorClass = computed(() => selectedCategoryObject.value?.color || "");

// Retorna o ícone e a cor para a SUBCATEGORIA
const subcategoriaIcon = computed(() => selectedSubcategoryObject.value?.icon || "mdi-scatter-plot");
const subcategoriaColorClass = computed(() => selectedSubcategoryObject.value?.color || "");

watch(
  () => formReleases.value.status_lancamento,
  (newStatus) => {
    if (newStatus === "Efetivada") {
      formReleases.value.data_efetivacao = formatDate(new Date(), "yyyy-MM-dd");
    } else {
      formReleases.value.data_efetivacao = null;
    }
  }
);

const subcategoriasDaCategoriaSelecionada = computed(() => {
  // Decide se estamos a trabalhar com receitas ou despesas
  // const categoriesSource = props.transactionType === "Receita"
  //   ? useRevenues.revenuesData?.categories
  //   : useExpenses.expensesData?.categories;

  if (!categoriesSource.value) {
    return [];
  }

  const selectedCategory = categoriesSource.value.find(
    (cat) => cat.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") === (formReleases.value.categoria || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  );

  if (selectedCategory && selectedCategory.subcategories) {
    return subcategoriesNames.value = selectedCategory.subcategories.map((sub) => sub.name);
  }

  return [];
});


const formatDateOnWatch = (newValue: any) => {
  if (newValue instanceof Date) {
    return formatDate(newValue, "yyyy-MM-dd");
  }
  return newValue;
};

watch(
  () => formReleases.value.data_vencimento,
  (nv) => (formReleases.value.data_vencimento = formatDateOnWatch(nv))
);
watch(
  () => formReleases.value.data_lancamento,
  (nv) => (formReleases.value.data_lancamento = formatDateOnWatch(nv))
);
watch(
  () => formReleases.value.data_efetivacao,
  (nv) => (formReleases.value.data_efetivacao = formatDateOnWatch(nv))
);

// Adiciona o watch para limpar a subcategoria quando a categoria mudar
watch(() => formReleases.value.categoria, () => {
  formReleases.value.subcategoria = 'Outros';
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

const inicializarValoresTemporarios = () => {
  tempParcelaInicial.value = 1;
  tempNumParcelas.value = 2;
  tempPeriodicidade.value = "Mensal";
};

const cancelarConfiguracaoRepeticao = () => {
  // Retorna tipo para "Não recorrente"
  formReleases.value.recorrencia = "Não recorrente";
  formReleases.value.num_parcelas = null;
  formReleases.value.periodicidade = null;

  // Fecha o modal
  openParcelas.value = false;
};

const concluirParcelas = () => {
  // Salva os valores temporários nos valores finais
  parcelaInicial.value = tempParcelaInicial.value;
  formReleases.value.num_parcelas = tempNumParcelas.value;
  formReleases.value.periodicidade = tempPeriodicidade.value || null;

  // Fecha o modal
  openParcelas.value = false;
};

// --- 6. COMPUTED PROPERTIES ---
// const creditCardAccounts = computed<Wallet[]>(() => walletsStore.walletsData.cartoes || []);
const availableBankAccounts = computed<Wallet[]>(() => walletsStore.walletsData.contas || []);

const selectedCreditCard = computed(() => {
  if (!formReleases.value.conta_id) return null;
  return props.wallets.find(c => c.id === formReleases.value.cartao_id);
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
    formReleases.value.num_parcelas = null;
    formReleases.value.periodicidade = null;
  }
};


const salvarLancamentos = async () => {
  errorStore.unsetError();

  if (!validFormLancamentos.value) {
    return;
  }

  // Se for um lançamento recorrente no modo de edição, mostra o modal primeiro.
  if (isEditMode.value && (formReleases.value.recorrencia === "Parcelado" || formReleases.value.recorrencia === "Fixa")) {
    showEditOptionsModal.value = true;
    return; // Para a execução aqui e espera a escolha do utilizador
  }

  // Se não for recorrente, executa a lógica de salvar diretamente
  await proceedWithSave();
};


const handleEditScopeSelection = async (scope: any) => {
  editScope.value = scope;
  showEditOptionsModal.value = false;
  await proceedWithSave();
};

const proceedWithSave = async () => {
  loading.value = true;
  const payload = { 
    ...formReleases.value,
    editScope: editScope.value,
    mesAno: props.mesAno,
  };
  try {
    
    if (formReleases.value.recorrencia === "Parcelado"  ) {
      if (props.releases?.recorrencia === "Parcelado" || props.releases?.recorrencia === "Fixa") {
        payload.tipo_parcela = props.releases?.tipo_parcela;
        payload.parcela_atual = props.releases?.parcela_atual;
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
    walletsStore.setWalletsData(res.data.wallets);

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

const clearInputs = () => {
  formReleases.value = {
    id: null,
    descricao: "",
    valor: "0,00",
    tipo_lancamento: props.transactionType,
    recorrencia: "Não recorrente",
    parcela_atual: null,
    num_parcelas: null,
    tipo_parcela: null,
    periodicidade: null,
    data_vencimento: new Date().toISOString().split("T")[0],
    status_lancamento: "Pendente",
    categoria: "Outros",
    subcategoria: "Outros",
    // conta: contasNames.value[0] || "",
    data_lancamento: new Date().toISOString().split("T")[0],
    data_efetivacao: null,
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
    if (formReleases.value.status_lancamento === "Efetivada") {
      return !!value || "O campo data efetivação é obrigatório";
    }
    return true;
  },
};
</script>

<style scoped>
.container__modal {
  background: rgb(15, 15, 15);
  color: #a5a5a5;
  height: 100%;
  min-height: 100%;
  width: 100%;
  max-width: 600px;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.header__items {
  /* background-color: #1e1e1e; */
  width: 100%;
  color: #a5a5a5;
  /* height: 70px; */
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
.btn__despesa {
  background: #ed0c0c;
}
.btn__receita {
  background: #77d08e;
}
.btn__cartao {
  background: #0c99ed;
}
.btn {
  color: #1e1e1e;
  cursor: pointer;
  font-weight: bold;
  align-self: center;
  border: none;
  margin-top: 1rem;
  font-size: 20px;
  /* background-color: #77d08e; */
  transition: background-color 0.5s;
}
/* .form__body {
  padding: 80px 16px 16px 16px;
  overflow-y: auto;
  height: 100vh;
} */
.imput {
  height: 40px;
  color: #a5a5a5;
  width: 100%;
}
.custom__input__container {
  position: relative;
  padding-top: 20px;
  padding-bottom: 4px;
}
.custom__input__content {
  display: flex;
  align-items: center;
  color: #a5a5a5;
}
.detalhe__parcela__interno {
  font-size: 14px;
  color: #a5a5a5;
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
  color: #a5a5a5;
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
.custom__display__input {
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.7);
  padding: 8px 0;
  cursor: pointer;
  color: #fff;
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
.modal__recorrente__card {
  background-color: #2c2c2e;
  color: white;
  border-radius: 16px;
}
.modal__option__btn {
  text-transform: none;
  justify-content: start;
  padding: 12px 16px !important;
  min-height: 48px;
  color: #77d08e;
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

.custom-input-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 4px;
}













</style>
