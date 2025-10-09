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

      <template v-if="!isCard">
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
              v-model="formReleases.data_vencimento"
              color="#77d08e"
              hide-header
              show-adjacent-months
              @update:model-value="menuDataVencimento = false"
            />
          </v-menu>
        </div>

        <v-text-field
          v-model="formReleases.status_lancamento"
          variant="underlined"
          hide-details="auto"
          type="text"
          class="mb-6 imput"
          readonly
          :prepend-inner-icon="
            formReleases.status_lancamento === 'Efetivada'
              ? 'mdi-check-circle-outline'
              : 'mdi-clock-time-three-outline'
          "
          @click="toggleStatus"
        >
          <template #append-inner>
            <div
              :class="
                formReleases.status_lancamento === 'Efetivada'
                  ? 'form__check__efetivada'
                  : 'form__check'
              "
            >
              <div
                :class="
                  formReleases.status_lancamento === 'Efetivada'
                    ? 'switch__check__efetivada'
                    : 'switch__check'
                "
              />
            </div>
          </template>
        </v-text-field>
      </template>
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
          v-model="formReleases.conta_id"
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
            v-model="formReleases.data_lancamento"
            color="#77d08e"
            hide-header
            show-adjacent-months
            @update:model-value="menuDataLancamento = false"
          />
        </v-menu>
      </div>

      <div
        v-if="informacoes && !isCard"
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
            v-model="formReleases.data_efetivacao"
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
import { onClickOutside } from "@vueuse/core";
import type { AxiosError } from "axios";
import { addMonths, format, isToday, isTomorrow, isValid, isYesterday, parseISO, subMonths } from "date-fns";
import { ptBR } from "date-fns/locale";
import { computed, nextTick, onMounted, ref, watch } from "vue";

// Components
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";

// Services and Stores
import http from "@/services/http";
import { useErrorStore, useExpensesStore, useRevenuesStore, useUserStore, useWalletsStore } from "@/store";

// Types and Utils
import type { ApiErrorResponse, Lancamento, Wallet } from "@/types";
import { formatValue } from "@/utils/formatValue";
import { getBankIcon } from "@/utils/iconMapper";

// 1. PROPS & EMITS
const props = defineProps<{
  releases?: Lancamento;
  wallets: Wallet[];
  rota: string;
  mesAno: string;
  transactionType: "Receita" | "Despesa";
  isCard?: boolean;
}>();

const emit = defineEmits(["updateData", "closeForm"]);

// 2. STORES
const walletsStore = useWalletsStore();
const useRevenues = useRevenuesStore();
const useExpenses = useExpensesStore();
const useUser = useUserStore();
const errorStore = useErrorStore();

// 3. REFS & STATE
const formReleases = ref<Partial<Lancamento>>({});
const loading = ref(false);
const validFormLancamentos = ref(false);
const informacoes = ref(false);
const errorsForm = ref<{ [key: string]: string[] }>({});

// UI State
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);
const openRecorrenciaModal = ref(false);
const openParcelas = ref(false);
const showEditOptionsModal = ref(false);
const isFaturaDropdownOpen = ref(false);
const isCardDropdownOpen = ref(false);

// Dropdown Refs
const faturaSelectRef = ref<HTMLElement | null>(null);
const cardSelectRef = ref<HTMLElement | null>(null);
const faturaDropdownContainerRef = ref<HTMLElement | null>(null);
const cardDropdownContainerRef = ref<HTMLElement | null>(null);

// Recurrence State
const editScope = ref<"apenas esta" | "esta e as próximas" | "todas" | "apenas este mês" | "mês atual e os próximos">("apenas esta");
const tipoCalculoParcela = ref<"total" | "parcela">("total");
const parcelaInicial = ref<number | null>(null);
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref<"Mensal" | "Semanal" | "Quinzenal" | "Bimestral">("Mensal");
const tiposRecorrencia = ref<("Não recorrente" | "Fixa" | "Parcelado")[]>([
  "Não recorrente",
  "Fixa",
  "Parcelado",
]);

// 4. COMPUTED PROPERTIES
const isEditMode = computed(() => !!props.releases?.id);

const creditCardAccounts = computed<Wallet[]>(() => 
  props.wallets.filter(w => w.tipo_carteira === 'Cartão de Crédito') || []
);

const availableBankAccounts = computed<Wallet[]>(() => 
  walletsStore.walletsData.contas || []
);

const selectedCreditCard = computed(() => {
  if (!formReleases.value.cartao_id) return null;
  return props.wallets.find(c => c.id === formReleases.value.cartao_id);
});

const linkedAccount = computed(() => {
  if (!selectedCreditCard.value?.conta_pai_id) return null;
  return availableBankAccounts.value.find(acc => acc.id === selectedCreditCard.value.conta_pai_id);
});

const linkedAccountName = computed(() => {
  if (!linkedAccount.value) return "Nenhuma conta vinculada";
  return linkedAccount.value.name || "Conta não encontrada";
});

const invoiceList = computed(() => {
  const list = [];
  const now = new Date();
  const formatDateForList = (date: Date) => {
    const month = date.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace(".", "");
    const year = date.getFullYear();
    return `${month}/${year}`;
  };
  // 12 meses para trás
  for (let i = 12; i > 0; i--) {
    list.push(formatDateForList(subMonths(now, i)));
  }
  // Mês atual e 12 meses para frente
  for (let i = 0; i <= 12; i++) {
    list.push(formatDateForList(addMonths(now, i)));
  }
  return list;
});

const categoriesSource = computed(() =>
  props.transactionType === "Receita"
    ? useRevenues.revenuesData?.categories
    : useExpenses.expensesData?.categories
);

const categoriasNames = computed(() => categoriesSource.value?.map((cat) => cat.name) || []);

const selectedCategoryObject = computed(() =>
  categoriesSource.value?.find(
    (cat) => cat.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") === (formReleases.value.categoria || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  )
);

const subcategoriasDaCategoriaSelecionada = computed(() => {
  if (selectedCategoryObject.value?.subcategories) {
    return selectedCategoryObject.value.subcategories.map((sub) => sub.name);
  }
  return [];
});

const selectedSubcategoryObject = computed(() =>
  selectedCategoryObject.value?.subcategories?.find(
    (sub) => sub.name === formReleases.value.subcategoria
  )
);

const categoriaIcon = computed(() => selectedCategoryObject.value?.icon || "mdi-scatter-plot");
const categoriaColorClass = computed(() => selectedCategoryObject.value?.color || "");
const subcategoriaIcon = computed(() => selectedSubcategoryObject.value?.icon || "mdi-scatter-plot");
const subcategoriaColorClass = computed(() => selectedSubcategoryObject.value?.color || "");

const detalheRecorrencia = computed(() => {
  if (formReleases.value.recorrencia !== "Parcelado" || !formReleases.value.qtd_parcelas || formReleases.value.qtd_parcelas <= 0) {
    return "";
  }
  const valorInput = parseFloat(formReleases.value.valor?.replace(/\./g, "").replace(",", ".") || "0");
  if (isNaN(valorInput) || valorInput <= 0) return "";

  const opcoesDeFormatacao = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
  
  if (tipoCalculoParcela.value === "total") {
    const valorParcela = valorInput / formReleases.value.qtd_parcelas;
    const valorFormatado = valorParcela.toLocaleString("pt-BR", opcoesDeFormatacao);
    return `Em ${formReleases.value.qtd_parcelas}x de R$ ${valorFormatado}`;
  } else {
    const valorFormatado = valorInput.toLocaleString("pt-BR", opcoesDeFormatacao);
    return `Em ${formReleases.value.qtd_parcelas}x de R$ ${valorFormatado}`;
  }
});

const displayDataVencimento = computed(() => formatDateForDisplay(formReleases.value.data_vencimento));
const displayDataLancamento = computed(() => formatDateForDisplay(formReleases.value.data_lancamento));
const displayDataEfetivacao = computed(() => formatDateForDisplay(formReleases.value.data_efetivacao));

// 5. WATCHERS
watch(() => formReleases.value.status_lancamento, (newStatus) => {
  formReleases.value.data_efetivacao = newStatus === "Efetivada" ? format(new Date(), "yyyy-MM-dd") : null;
});

watch(() => formReleases.value.categoria, () => {
  formReleases.value.subcategoria = 'Outros';
});

watch(() => formReleases.value.data_vencimento, (nv) => (formReleases.value.data_vencimento = formatDateOnWatch(nv)));
watch(() => formReleases.value.data_lancamento, (nv) => (formReleases.value.data_lancamento = formatDateOnWatch(nv)));
watch(() => formReleases.value.data_efetivacao, (nv) => (formReleases.value.data_efetivacao = formatDateOnWatch(nv)));

// NOVO: Watch para atualizar a fatura quando o cartão de crédito mudar
watch(() => formReleases.value.cartao_id, (newCardId) => {
  if (newCardId) {
    setDefaultInvoice();
  }
});


// 6. LIFECYCLE HOOKS
onMounted(() => {
  initializeForm();
});

// 7. METHODS

// Form Initialization
const initializeForm = () => {
  const hoje = format(new Date(), "yyyy-MM-dd");

  formReleases.value = {
    id: props.releases?.id || null,
    descricao: props.releases?.descricao || "",
    valor: formatValue(Number(props.releases?.valor)) || "0,00",
    tipo_lancamento: props.transactionType,
    recorrencia: props.releases?.recorrencia || "Não recorrente",
    num_parcela: props.releases?.num_parcela || null,
    qtd_parcelas: props.releases?.qtd_parcelas || null,
    tipo_parcela: props.releases?.tipo_parcela || null,
    periodicidade: props.releases?.periodicidade || null,
    data_vencimento: validateDate(props.releases?.data_vencimento),
    status_lancamento: props.releases?.status_lancamento || "PENDENTE",
    categoria: props.releases?.categoria || "Outros",
    subcategoria: props.releases?.subcategoria || "Outros",
    conta_id: props.isCard ? null : (props.releases?.conta_id || availableBankAccounts.value[0]?.id || null),
    cartao_id: props.isCard ? (props.releases?.cartao_id || creditCardAccounts.value[0]?.id || null) : null,
    fatura: props.releases?.fatura || null,
    data_lancamento: validateDate(props.releases?.data_lancamento),
    data_efetivacao: props.releases?.data_efetivacao ? validateDate(props.releases.data_efetivacao) : null,
  };
  
  // Lógica de inicialização para cartão
  if (props.isCard && !isEditMode.value) {
    setDefaultInvoice();
  }
};

// Date Handling
const validateDate = (date: string | Date | undefined | null): string => {
  if (!date) return format(new Date(), "yyyy-MM-dd");
  
  const parsedDate = typeof date === 'string' ? parseISO(date) : date;
  
  return isValid(parsedDate) ? format(parsedDate, "yyyy-MM-dd") : format(new Date(), "yyyy-MM-dd");
};

const formatDateOnWatch = (newValue: any) => {
  if (newValue instanceof Date) {
    return format(newValue, "yyyy-MM-dd");
  }
  return newValue;
};

const formatDateForDisplay = (dateValue: string | Date | undefined | null): string => {
  if (!dateValue) return "Selecione...";

  const data = typeof dateValue === 'string' ? parseISO(dateValue) : dateValue;
  if (!isValid(data)) return "Data inválida";
  
  if (isToday(data)) return "Hoje";
  if (isYesterday(data)) return "Ontem";
  if (isTomorrow(data)) return "Amanhã";

  const nomeDiaCompleto = format(data, "EEEE", { locale: ptBR });
  const diaAbreviadoCapitalizado = nomeDiaCompleto.charAt(0).toUpperCase() + nomeDiaCompleto.slice(1, 3);
  const dataFormatada = format(data, "dd/MM/yyyy");

  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
};

// Business Logic: Card and Invoice
const setDefaultInvoice = () => {
  const card = selectedCreditCard.value;
  if (!card || !card.dia_fechamento) return;

  const today = new Date();
  const closingDay = card.dia_fechamento;
  const currentDay = today.getDate();

  let invoiceDate = today;
  if (currentDay > closingDay) {
    invoiceDate = addMonths(today, 1);
  }

  const month = invoiceDate.toLocaleDateString("pt-BR", { month: "short" }).toUpperCase().replace(".", "");
  const year = invoiceDate.getFullYear();
  formReleases.value.fatura = `${month}/${year}`;
};

// UI Interaction
onClickOutside(faturaSelectRef, () => { isFaturaDropdownOpen.value = false; });
onClickOutside(cardSelectRef, () => { isCardDropdownOpen.value = false; });

const toggleStatus = () => {
  formReleases.value.status_lancamento =
    formReleases.value.status_lancamento === "Efetivada" ? "Pendente" : "Efetivada";
};

const toggleFaturaDropdown = async () => {
  isFaturaDropdownOpen.value = !isFaturaDropdownOpen.value;
  if (isFaturaDropdownOpen.value) {
    await nextTick();
    const container = faturaDropdownContainerRef.value;
    if (container && formReleases.value.fatura) {
      const selectedItem = container.querySelector(`[data-invoice="${formReleases.value.fatura}"]`) as HTMLElement;
      selectedItem?.scrollIntoView({ block: "start", behavior: "auto" });
    }
  }
};

const toggleCardDropdown = async () => {
  isCardDropdownOpen.value = !isCardDropdownOpen.value;
  if (isCardDropdownOpen.value) {
    await nextTick();
    const container = cardDropdownContainerRef.value;
    if (container && formReleases.value.cartao_id) {
      const selectedItem = container.querySelector(`[data-card-id="${formReleases.value.cartao_id}"]`) as HTMLElement;
      selectedItem?.scrollIntoView({ block: "start", behavior: "auto" });
    }
  }
};

const selectInvoice = (invoice: string) => {
  formReleases.value.fatura = invoice;
  isFaturaDropdownOpen.value = false;
};

const selectCard = (card: Wallet) => {
  formReleases.value.cartao_id = card.id;
  isCardDropdownOpen.value = false;
};

// Form Value Formatting
const formatValueSave = () => {
  let digits = (formReleases.value.valor || "").replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;

  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  
  formReleases.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

// Recurrence Logic
const selecionarRecorrencia = (item: "Não recorrente" | "Fixa" | "Parcelado") => {
  formReleases.value.recorrencia = item;
  openRecorrenciaModal.value = false;

  if (item === "Parcelado") {
    openParcelas.value = true;
  } else {
    formReleases.value.qtd_parcelas = null;
    formReleases.value.periodicidade = null;
  }
};

const incrementParcelaInicial = () => tempParcelaInicial.value++;
const decrementParcelaInicial = () => { if (tempParcelaInicial.value > 1) tempParcelaInicial.value--; };
const incrementQuantidade = () => tempNumParcelas.value++;
const decrementQuantidade = () => { if (tempNumParcelas.value > 2) tempNumParcelas.value--; };

const cancelarConfiguracaoRepeticao = () => {
  formReleases.value.recorrencia = "Não recorrente";
  formReleases.value.qtd_parcelas = null;
  formReleases.value.periodicidade = null;
  openParcelas.value = false;
};

const concluirParcelas = () => {
  parcelaInicial.value = tempParcelaInicial.value;
  formReleases.value.qtd_parcelas = tempNumParcelas.value;
  formReleases.value.periodicidade = tempPeriodicidade.value || null;
  openParcelas.value = false;
};

// Form Submission
const salvarLancamentos = async () => {
  errorStore.unsetError();
  if (!validFormLancamentos.value) return;

  if (isEditMode.value && (formReleases.value.recorrencia === "Parcelado" || formReleases.value.recorrencia === "Fixa")) {
    showEditOptionsModal.value = true;
    return;
  }
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
    // Adicionamos o tipo de transação para o backend saber como agir
    tipo_lancamento: props.isCard ? 'Cartão de Crédito' : props.transactionType,
  };

  if (props.isCard) {
    payload.conta_id = formReleases.value.cartao_id;
  }
  delete payload.cartao_id;

  if (formReleases.value.recorrencia === "Parcelado") {
    if (props.releases?.recorrencia === "Parcelado" || props.releases?.recorrencia === "Fixa") {
      payload.tipo_parcela = props.releases?.tipo_parcela;
      payload.parcela_atual = props.releases?.parcela_atual;
    } else {
      payload.tipo_parcela = tipoCalculoParcela.value;
      payload.parcela_atual = parcelaInicial.value;
    }
  }

  try {
    const method = isEditMode.value ? http.put : http.post;
    const url = isEditMode.value ? `/lancamentos/${payload.id}` : "/lancamentos";
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

const closeForm = () => {
  emit("closeForm");
};

// Validation Rules
const rules = {
  required: (value: any) => !!value || "Campo obrigatório",
  requiredDescricao: (value: string) => !!value || "O campo descrição é obrigatório",
  requiredValor: (value: string) => !!value || "O campo valor é obrigatório",
  requiredValorMaiorQue0: (value: string) => {
    if (!value) return "O campo valor é obrigatório";
    const numericValue = parseFloat(value.replace(/\./g, "").replace(",", "."));
    return (!isNaN(numericValue) && numericValue > 0) || "O valor deve ser maior que zero";
  },
  requiredCatagoria: (value: string) => !!value || "O campo categoria é obrigatório",
};
</script>

<style scoped>
/* SEU CSS CONTINUA O MESMO */
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