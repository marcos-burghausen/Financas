<template>
  <div class="despesas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3">
        <div>
          <h1 class="text-h4 font-weight-bold d-flex align-center gap-2 mb-2">
            <v-icon
              icon="mdi-cash-remove"
              size="32"
              color="error"
            />
            Minhas Despesas
          </h1>
          <p class="text-subtitle-2 text-medium-emphasis mb-0">
            Gerencie suas despesas e gastos
          </p>
        </div>
        <v-btn
          color="error"
          size="large"
          prepend-icon="mdi-plus"
          class="flex-shrink-0"
          @click="openAddDialog"
        >
          Nova Despesa
        </v-btn>
      </div>
    </div>

    <!-- 📅 Month Navigation -->
    <v-card
      class="mb-6"
      elevation="1"
    >
      <v-card-text class="pa-4">
        <div class="d-flex align-center justify-center gap-4">
          <v-btn
            icon="mdi-chevron-left"
            color="primary"
            variant="outlined"
            size="small"
            title="Mês anterior"
            @click="goToPreviousMonth"
          />
          <div
            class="text-center"
            style="min-width: 250px"
          >
            <v-btn
              variant="text"
              :text="getMonthName(currentMonth).toUpperCase()"
              :class="{ 'text-primary font-weight-bold': currentMonth === new Date().toISOString().slice(0, 7) }"
              title="Ir para o mês atual"
              @click="goToCurrentMonth"
            />
          </div>
          <v-btn
            icon="mdi-chevron-right"
            color="primary"
            variant="outlined"
            size="small"
            title="Próximo mês"
            @click="goToNextMonth"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- Summary Cards -->
    <v-row class="mb-6">
      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card error-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="error"
                icon="mdi-cash-remove"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Total do Mês
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-error">
              {{ formatCurrency(summary.totalMes || 0) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatPercentage(summary.variacaoMes) }}
              <v-icon
                :icon="summary.variacaoMes >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'"
                :color="summary.variacaoMes >= 0 ? 'error' : 'success'"
                size="x-small"
              />
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card warning-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="warning"
                icon="mdi-calendar-check"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Pagas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-warning">
              {{ despesasPagas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaPagas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card info-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="info"
                icon="mdi-clock-outline"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Pendentes
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-info">
              {{ despesasPendentes }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaPendentes) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card danger-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="error"
                icon="mdi-calendar-remove"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Atrasadas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-error">
              {{ despesasAtrasadas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaAtrasadas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters and Controls -->
    <v-card
      class="mb-6"
      elevation="1"
    >
      <v-card-text class="pa-4">
        <v-row
          class="align-center"
          dense
        >
          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-text-field
              v-model="searchText"
              label="Buscar"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-select
              v-model="selectedStatus"
              label="Status"
              :items="statusOptions"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-select
              v-model="selectedCategoria"
              label="Categoria"
              :items="categoriasNames"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-btn
              color="primary"
              variant="outlined"
              block
              @click="resetFilters"
            >
              Limpar Filtros
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Despesas Table -->
    <v-card elevation="1">
      <v-data-table
        :headers="headers"
        :items="filteredDespesas"
        :loading="loading"
        :items-per-page="itemsPerPage"
        class="despesas-table"
      >
        <!-- Data columns -->
        <template #item.descricao="{ item }">
          <div class="d-flex align-center gap-2">
            <v-avatar
              size="32"
              color="error"
              variant="tonal"
              icon="mdi-receipt"
            />
            <div>
              <div class="font-weight-500">
                {{ item.descricao }}
              </div>
              <div class="text-caption text-medium-emphasis">
                {{ formatDate(item.data_vencimento) }}
              </div>
            </div>
          </div>
        </template>

        <template #item.valor="{ item }">
          <div class="text-right font-weight-bold text-error">
            {{ formatCurrency(item.valor) }}
          </div>
        </template>

        <template #item.categoria="{ item }">
          <v-chip
            size="small"
            variant="outlined"
          >
            {{ item.categoria }}
          </v-chip>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(getStatusReal(item))"
            :text-color="getStatusTextColor(getStatusReal(item))"
            size="small"
            label
          >
            {{ getStatusLabel(getStatusReal(item)) }}
          </v-chip>
        </template>

        <template #item.acoes="{ item }">
          <div class="d-flex gap-2 justify-end">
            <v-btn
              v-if="getStatusReal(item) === 'pendente' || getStatusReal(item) === 'atrasada'"
              icon="mdi-check-circle"
              variant="text"
              size="small"
              color="success"
              title="Marcar como paga"
              @click="efetivarDespesa(item)"
            />
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              color="primary"
              @click="editDespesa(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              size="small"
              color="error"
              @click="deleteDespesa(item.id)"
            />
          </div>
        </template>

        <!-- No data template -->
        <template #no-data>
          <div class="text-center py-8">
            <v-icon
              size="48"
              color="medium-emphasis"
              class="mb-4 d-block"
            >
              mdi-folder-open-outline
            </v-icon>
            <p class="text-subtitle-1 text-medium-emphasis">
              Nenhuma despesa encontrada
            </p>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog
      v-model="dialog"
      max-width="700px"
    >
      <v-card>
        <v-card-title class="d-flex align-center gap-2 pa-6 pb-3">
          <v-icon
            :icon="editingId ? 'mdi-pencil' : 'mdi-plus'"
            color="error"
          />
          {{ editingId ? 'Editar Despesa' : 'Nova Despesa' }}
        </v-card-title>

        <!-- Dialog Content -->
        <v-card-text class="pa-6">
          <v-form
            ref="formRef"
            @submit.prevent="saveDespesa"
          >
            <!-- Row 1: Descrição -->
            <v-text-field
              v-model="formData.descricao"
              label="Descrição *"
              prepend-inner-icon="mdi-text-long"
              variant="underlined"
              hide-details="auto"
              required
              class="mb-4"
              :rules="[rules.required, rules.minLength3]"
            />

            <!-- Row 2: Valor -->
            <v-text-field
              v-model="formData.valor"
              label="Valor *"
              prepend-inner-icon="mdi-currency-brl"
              variant="underlined"
              hide-details="auto"
              type="tel"
              class="mb-4"
              :rules="[rules.required, rules.valorPositivo]"
              @input="formatValueDisplay"
            />

            <!-- Row 3: Recorrência -->
            <div class="custom__input__container mb-4">
              <div
                class="custom__input__content"
                @click="openRecorrenciaModal = true"
              >
                <v-icon
                  icon="mdi-refresh"
                  class="me-2"
                />
                <div class="d-flex flex-column">
                  <span>{{ formData.recorrencia }}</span>
                  <span
                    v-if="detalheRecorrencia"
                    class="detalhe__parcela__interno"
                  >
                    {{ detalheRecorrencia }}
                  </span>
                </div>
                <v-spacer />
                <v-icon
                  v-if="formData.recorrencia === 'Parcelado'"
                  icon="mdi-pencil"
                  size="x-small"
                  class="edit__icon"
                  @click.stop="openParcelas = true"
                />
              </div>

              <v-btn-toggle
                v-if="formData.recorrencia === 'Parcelado'"
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

            <!-- Modal Recorrência -->
            <v-dialog
              v-model="openRecorrenciaModal"
              :close-on-content-click="false"
            >
              <v-card
                width="300"
                class="mx-auto"
              >
                <v-card-text class="pa-4">
                  <div class="d-flex flex-column gap-2">
                    <v-btn
                      v-for="item in tiposRecorrencia"
                      :key="item"
                      :class="formData.recorrencia === item ? 'success' : ''"
                      variant="text"
                      block
                      :prepend-icon="
                        formData.recorrencia === item
                          ? 'mdi-radiobox-marked'
                          : 'mdi-checkbox-blank-circle-outline'
                      "
                      @click="selecionarRecorrencia(item)"
                    >
                      {{ item }}
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-dialog>

            <!-- Modal Parcelas -->
            <v-dialog
              v-model="openParcelas"
              max-width="400"
            >
              <v-card>
                <v-card-title class="pa-4">
                  Configurar Parcelas
                </v-card-title>
                <v-card-text class="pa-6">
                  <div class="d-flex align-center justify-space-between mb-4">
                    <span>Parcela Inicial:</span>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        icon="mdi-minus"
                        size="x-small"
                        :disabled="tempParcelaInicial <= 1"
                        @click="tempParcelaInicial--"
                      />
                      <v-text-field
                        v-model.number="tempParcelaInicial"
                        type="number"
                        density="compact"
                        style="width: 60px"
                        min="1"
                        :max="tempNumParcelas"
                      />
                      <v-btn
                        icon="mdi-plus"
                        size="x-small"
                        :disabled="tempParcelaInicial >= tempNumParcelas"
                        @click="tempParcelaInicial++"
                      />
                    </div>
                  </div>

                  <v-divider class="my-4" />

                  <div class="d-flex align-center justify-space-between mb-4">
                    <span>Quantidade:</span>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        icon="mdi-minus"
                        size="x-small"
                        :disabled="tempNumParcelas <= 2"
                        @click="tempNumParcelas--"
                      />
                      <v-text-field
                        v-model.number="tempNumParcelas"
                        type="number"
                        density="compact"
                        style="width: 60px"
                        min="2"
                      />
                      <v-btn
                        icon="mdi-plus"
                        size="x-small"
                        @click="tempNumParcelas++"
                      />
                    </div>
                  </div>

                  <v-divider class="my-4" />

                  <v-select
                    v-model="tempPeriodicidade"
                    label="Periodicidade"
                    :items="['Mensal', 'Semanal', 'Quinzenal', 'Bimestral']"
                    variant="outlined"
                    density="compact"
                  />
                </v-card-text>
                <v-card-actions class="pa-4">
                  <v-spacer />
                  <v-btn
                    variant="text"
                    @click="openParcelas = false"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    color="success"
                    @click="concluirParcelas"
                  >
                    Concluído
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <!-- Row 4: Categoria e Subcategoria -->
            <v-row>
              <v-col
                cols="12"
                md="6"
              >
                <v-autocomplete
                  v-model="formData.categoria"
                  :items="categoriasNames"
                  label="Categoria *"
                  prepend-inner-icon="mdi-tag"
                  variant="underlined"
                  hide-details="auto"
                  class="mb-4"
                  :rules="[rules.required]"
                />
              </v-col>
              <v-col
                cols="12"
                md="6"
              >
                <v-autocomplete
                  v-model="formData.subcategoria"
                  :items="subcategoriasDaCategoriaSelecionada"
                  label="Subcategoria"
                  prepend-inner-icon="mdi-folder-tag"
                  variant="underlined"
                  hide-details="auto"
                  class="mb-4"
                />
              </v-col>
            </v-row>

            <!-- Row 5: Conta e Status -->
            <v-row>
              <v-col
                cols="12"
                md="6"
              >
                <v-select
                  v-model="formData.conta_id"
                  label="Conta *"
                  prepend-inner-icon="mdi-bank"
                  variant="underlined"
                  hide-details="auto"
                  :items="contas"
                  item-title="name"
                  item-value="id"
                  class="mb-4"
                  :rules="[rules.required]"
                >
                  <template #selection="{ item }">
                    <div class="d-flex align-center gap-2">
                      <v-icon
                        :icon="getBankIcon(item.raw.icon || '')"
                        size="small"
                      />
                      <span>{{ item.raw.name }}</span>
                    </div>
                  </template>
                  <template #item="{ item, props }">
                    <v-list-item
                      v-bind="props"
                      :title="item.raw.name"
                    >
                      <template #prepend>
                        <v-icon
                          :icon="getBankIcon(item.raw.icon || '')"
                          class="me-2"
                        />
                      </template>
                    </v-list-item>
                  </template>
                </v-select>
              </v-col>
              <v-col
                cols="12"
                md="6"
              >
                <v-text-field
                  :model-value="formData.status_lancamento"
                  label="Status"
                  variant="underlined"
                  hide-details="auto"
                  type="text"
                  readonly
                  class="mb-4"
                  :prepend-inner-icon="
                    formData.status_lancamento === 'EFETIVADA'
                      ? 'mdi-check-circle-outline'
                      : 'mdi-clock-time-three-outline'
                  "
                  @click="toggleStatus"
                >
                  <template #append-inner>
                    <div :class="formData.status_lancamento === 'EFETIVADA' ? 'switch__check__efetivada' : 'switch__check'">
                      <div :class="formData.status_lancamento === 'EFETIVADA' ? 'switch__check__efetivada--inner' : 'switch__check--inner'" />
                    </div>
                  </template>
                </v-text-field>
              </v-col>
            </v-row>

            <!-- Row 6: Data de Vencimento -->
            <v-menu
              v-model="menuDataVencimento"
              :close-on-content-click="false"
              transition="scale-transition"
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
                    <span>Data de Vencimento *</span>
                  </div>
                  <v-spacer class="m-0 p-0" />
                  <span class="font-weight-medium">
                    {{ displayDataVencimento }}
                    <v-chip
                      v-if="dataVencimentoRelativa"
                      size="x-small"
                      class="ms-2"
                      color="success"
                      variant="outlined"
                    >
                      {{ dataVencimentoRelativa }}
                    </v-chip>
                  </span>
                </div>
              </template>

              <v-date-picker
                v-model="formData.data_vencimento"
                color="success"
                hide-header
                show-adjacent-months
              />
            </v-menu>

            <!-- Row 7: Mais Informações Toggle -->
            <v-btn
              :append-icon="informacoes ? 'mdi-chevron-up' : 'mdi-chevron-down'"
              variant="plain"
              size="x-small"
              style="color: rgb(var(--v-theme-error))"
              block
              class="my-4"
              @click="informacoes = !informacoes"
            >
              Mais informações
            </v-btn>

            <!-- Row 8: Data de Lançamento (Advanced) -->
            <v-menu
              v-if="informacoes"
              v-model="menuDataLancamento"
              :close-on-content-click="false"
              transition="scale-transition"
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
                    <span>Data de Lançamento</span>
                  </div>
                  <v-spacer />
                  <span class="font-weight-medium">{{ displayDataLancamento }}</span>
                </div>
              </template>

              <v-date-picker
                v-model="formData.data_lancamento"
                color="error"
                hide-header
                show-adjacent-months
              />
            </v-menu>

            <!-- Row 9: Data de Efetivação (Advanced) -->
            <v-menu
              v-if="informacoes"
              v-model="menuDataEfetivacao"
              :close-on-content-click="false"
              transition="scale-transition"
              class="mt-4"
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
                    <span>Data de Efetivação</span>
                  </div>
                  <v-spacer />
                  <span class="font-weight-medium">{{ displayDataEfetivacao }}</span>
                </div>
              </template>

              <v-date-picker
                v-model="formData.data_efetivacao"
                color="error"
                hide-header
                show-adjacent-months
              />
            </v-menu>

            <!-- Row 10: Observações -->
            <v-textarea
              v-if="informacoes"
              v-model="formData.observacoes"
              label="Observações (opcional)"
              placeholder="Adicione notas ou detalhes sobre este lançamento..."
              prepend-inner-icon="mdi-note-text-outline"
              variant="underlined"
              rows="3"
              auto-grow
              counter
              maxlength="1000"
              class="mt-4"
            />

            <!-- Buttons -->
            <div class="d-flex gap-2 justify-end mt-6">
              <v-btn
                variant="outlined"
                @click="dialog = false"
              >
                Cancelar
              </v-btn>
              <v-btn
                color="error"
                type="submit"
                :loading="loading"
                :disabled="loading"
              >
                {{ editingId ? 'Atualizar' : 'Adicionar' }}
              </v-btn>
            </div>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Loading Overlay - Navegação de Meses -->
    <v-overlay
      v-model="loadingMonth"
      class="align-center justify-center"
      persistent
      contained
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="64"
          width="5"
          color="error"
          class="mb-4"
        />
        <div class="text-subtitle-1 text-white mb-1">
          Carregando despesas...
        </div>
        <div class="text-caption text-white-50">
          {{ getMonthName(currentMonth) }}
        </div>
      </div>
    </v-overlay>

    <!-- Loading Overlay - Carregamento do Formulário -->
    <v-overlay
      v-model="loadingForm"
      class="align-center justify-center"
      persistent
      :z-index="9999"
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="64"
          width="5"
          color="error"
          class="mb-4"
        />
        <div class="text-subtitle-1 text-white mb-1">
          Carregando formulário...
        </div>
        <div class="text-caption text-white-50">
          Preparando dados
        </div>
      </div>
    </v-overlay>
  </div>
</template>

<script setup lang="ts">
import { useLancamentos } from "@/composables/useLancamentos";
import despesasService from "@/services/despesas.service";
import { useExpensesStore } from "@/store/expenses";
import { useToastStore } from "@/store/toast";
import { useUserStore } from "@/store/user";
import { useWalletsStore } from "@/store/wallets";
import { getBankIcon } from "@/utils/iconMapper";
import { format, isToday, isTomorrow, isValid, isYesterday, parseISO } from "date-fns";
import { ptBR } from "date-fns/locale";
import { computed, onMounted, ref, watch } from "vue";

const toastStore = useToastStore();
const userStore = useUserStore();
const expensesStore = useExpensesStore();
const walletsStore = useWalletsStore();

// State
const dialog = ref(false);
const formRef = ref();
const loading = ref(false);
const loadingMonth = ref(false);
const loadingForm = ref(false);
const searchText = ref("");
const selectedStatus = ref("");
const selectedCategoria = ref("");
const editingId = ref<number | null>(null);
const itemsPerPage = ref(10);

// ✅ Refs para controlar date pickers
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);

// Recurrence State (igual a ReceitasView)
const openRecorrenciaModal = ref(false);
const openParcelas = ref(false);
const informacoes = ref(false);
const tiposRecorrencia = ref(["Não recorrente", "Fixa", "Parcelado"]);
const tipoCalculoParcela = ref("total");
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref("Mensal");

// Mock data
const despesas = ref([]);
const variacao = ref(0);

const categorias = ref(["Salário", "Freelancer", "Bonus", "Investimento", "Outros"]);
const categoriasNames = computed(() => {
  // Usa dados do store se disponível, senão usa hardcoded como fallback
  if (expensesStore.expensesData?.categories && expensesStore.expensesData.categories.length > 0) {
    return expensesStore.expensesData.categories.map((cat: any) => cat.name);
  }
  return ["Salário", "Freelancer", "Bonus", "Investimento", "Outros"];
});
const subcategorias = ref({
  "Salário": ["Salário", "Décimo terceiro"],
  "Freelancer": ["Projeto", "Consultoria"],
  "Bonus": ["Bônus mensal", "Bônus anual"],
  "Investimento": ["Ações", "Renda fixa"],
  "Outros": ["Outros"],
});

const contas = ref([]);

const statusOptions = ref([
  "recebida",
  "pendente",
  "cancelada",
]);

// Form data
const formData = ref({
  descricao: "",
  categoria: "",
  conta: "",
  valor: "0,00",
  data_vencimento: new Date().toISOString().split("T")[0],
  status: "pendente",
  observacao: "",
  recorrencia: "Não recorrente",
  status_lancamento: "PENDENTE",
  subcategoria: "",
  conta_id: null,
  data_lancamento: new Date().toISOString().split("T")[0],
  data_efetivacao: null,
  observacoes: "",
});

// Computed properties
const subcategoriasDaCategoriaSelecionada = computed(() => {
  // Tenta usar dados do store primeiro
  if (expensesStore.expensesData?.categories && expensesStore.expensesData.categories.length > 0) {
    const categoryFound = expensesStore.expensesData.categories.find((cat: any) => cat.name === formData.value.categoria);
    if (categoryFound && categoryFound.subcategories) {
      return categoryFound.subcategories.map((sub: any) => sub.name);
    }
  }
  // Fallback para dados hardcoded
  return subcategorias.value[formData.value.categoria] || [];
});

const detalheRecorrencia = computed(() => {
  if (formData.value.recorrencia === "Parcelado" && formData.value.valor && tempNumParcelas.value > 0) {
    const valorInput = parseFloat(formData.value.valor.replace(/\./g, "").replace(",", "."));
    if (!isNaN(valorInput) && valorInput > 0) {
      let valorParcela: number;
      
      // Se toggle está em 'total', divide o valor pelo número de parcelas
      // Se toggle está em 'parcela', o valor já é o valor de uma parcela
      if (tipoCalculoParcela.value === "total") {
        valorParcela = valorInput / tempNumParcelas.value;
      } else {
        valorParcela = valorInput;
      }
      
      const valorFormatado = valorParcela.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      return `Em ${tempNumParcelas.value}x de R$ ${valorFormatado}`;
    }
  }
  return "";
});

const displayDataVencimento = computed(() => {
  return formatDateForDisplay(formData.value.data_vencimento);
});

const displayDataLancamento = computed(() => {
  return formatDateForDisplay(formData.value.data_lancamento);
});

const displayDataEfetivacao = computed(() => {
  return formatDateForDisplay(formData.value.data_efetivacao);
});

// 📅 Calcular data relativa (Ontem, Hoje, Amanhã)
const dataVencimentoRelativa = computed(() => {
  if (!formData.value.data_vencimento) return "";
  
  try {
    const dataVencimento = new Date(formData.value.data_vencimento);
    const hoje = new Date();
    
    // Normalizar para comparação apenas de datas (sem horas)
    dataVencimento.setHours(0, 0, 0, 0);
    hoje.setHours(0, 0, 0, 0);
    
    const diffTime = dataVencimento.getTime() - hoje.getTime();
    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return "Hoje";
    if (diffDays === -1) return "Ontem";
    if (diffDays === 1) return "Amanhã";
    if (diffDays < 0) return `${Math.abs(diffDays)} dias atrás`;
    if (diffDays > 1) return `Em ${diffDays} dias`;
    
    return "";
  } catch (error) {
    return "";
  }
});

// Summary computed
const summary = computed(() => ({
  totalMes: despesas.value.reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, "").replace(",", ".")), 0),
  variacaoMes: variacao.value,
}));

// ✅ Usar getStatusReal para calcular o status baseado em datas
const despesasPagas = computed(() => despesas.value.filter(r => getStatusReal(r) === "recebida").length);
const somaPagas = computed(() => despesas.value.filter(r => getStatusReal(r) === "recebida").reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, "").replace(",", ".")), 0));

const despesasPendentes = computed(() => despesas.value.filter(r => getStatusReal(r) === "pendente").length);
const somaPendentes = computed(() => despesas.value.filter(r => getStatusReal(r) === "pendente").reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, "").replace(",", ".")), 0));

const despesasAtrasadas = computed(() => despesas.value.filter(r => getStatusReal(r) === "atrasada").length);
const somaAtrasadas = computed(() => despesas.value.filter(r => getStatusReal(r) === "atrasada").reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, "").replace(",", ".")), 0));

// Filtered despesas
const filteredDespesas = computed(() => {
  return despesas.value.filter(r => {
    const matchText = !searchText.value || r.descricao.toLowerCase().includes(searchText.value.toLowerCase());
    const matchStatus = !selectedStatus.value || getStatusReal(r) === selectedStatus.value;
    const matchCategoria = !selectedCategoria.value || r.categoria === selectedCategoria.value;
    return matchText && matchStatus && matchCategoria;
  });
});

// Validation rules
const rules = {
  required: (v: any) => !!v || "Campo obrigatório",
  minLength3: (v: string) => (v && v.length >= 3) || "Mínimo 3 caracteres",
  valorPositivo: (v: string) => {
    if (!v) return "Valor obrigatório";
    const numValue = parseFloat(v.replace(/\./g, "").replace(",", "."));
    return numValue > 0 || "Valor deve ser maior que zero";
  },
};

// Headers
const headers = [
  { title: "Descrição", align: "start", key: "descricao", width: "35%" },
  { title: "Categoria", align: "start", key: "categoria", width: "15%" },
  { title: "Valor", align: "end", key: "valor", width: "15%" },
  { title: "Status", align: "center", key: "status", width: "15%" },
  { title: "Ações", align: "end", key: "acoes", width: "10%", sortable: false },
];

// Methods
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value / 100);
};

const formatPercentage = (value: number | undefined) => {
  if (value === undefined || value === null) return "+0.0%";
  return `${value >= 0 ? "+" : ""}${value.toFixed(1)}%`;
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString("pt-BR");
};

const formatValueDisplay = () => {
  let digits = (formData.value.valor || "").replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;

  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  formData.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

const formatDateForDisplay = (dateValue: string | Date | undefined | null): string => {
  if (!dateValue) return "Selecione...";

  const data = typeof dateValue === "string" ? parseISO(dateValue) : dateValue;
  if (!isValid(data)) return "Data inválida";

  if (isToday(data)) return "Hoje";
  if (isYesterday(data)) return "Ontem";
  if (isTomorrow(data)) return "Amanhã";

  const nomeDiaCompleto = format(data, "EEEE", { locale: ptBR });
  const diaAbreviadoCapitalizado = nomeDiaCompleto.charAt(0).toUpperCase() + nomeDiaCompleto.slice(1, 3);
  const dataFormatada = format(data, "dd/MM/yyyy");

  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
};

// ✅ Formatar data para enviar ao backend (YYYY-MM-DD)
const formatDateForBackend = (dateValue: string | Date | undefined | null): string => {
  if (!dateValue) return "";
  
  try {
    // Se for string ISO com timezone, extrair apenas a data
    if (typeof dateValue === "string" && dateValue.includes("T")) {
      return dateValue.split("T")[0]; // "2025-10-09T03:00:00.000Z" → "2025-10-09"
    }
    
    // Se for string em formato YYYY-MM-DD, retornar como está
    if (typeof dateValue === "string" && dateValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
      return dateValue;
    }
    
    // Se for Date object, formatar
    if (dateValue instanceof Date) {
      const year = dateValue.getFullYear();
      const month = String(dateValue.getMonth() + 1).padStart(2, "0");
      const day = String(dateValue.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    }
    
    return "";
  } catch (error) {
    console.error("Erro ao formatar data:", dateValue, error);
    return "";
  }
};
// 📅 Month Navigation Functions
const getMonthName = (mesAnoString: string): string => {
  const [ano, mes] = mesAnoString.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  return format(date, "MMMM yyyy", { locale: ptBR });
};

// LOCAL month state - independent per page
const currentMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM

const goToPreviousMonth = () => {
  const [ano, mes] = currentMonth.value.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() - 1);
  currentMonth.value = date.toISOString().slice(0, 7);
  loadingMonth.value = true;
  loadDespesas();
};

const goToNextMonth = () => {
  const [ano, mes] = currentMonth.value.split("-");
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() + 1);
  currentMonth.value = date.toISOString().slice(0, 7);
  loadingMonth.value = true;
  loadDespesas();
};

// ✅ Função para calcular o status real baseado na data de vencimento
const getStatusReal = (receita: any): string => {
  // Se status for EFETIVADA (recebida), retorna recebida
  if (receita.status_lancamento === "EFETIVADA") {
    return "recebida";
  }
  
  // Se status for PENDENTE, verifica se está atrasada
  if (receita.status_lancamento === "PENDENTE") {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    
    let dataVencimento = new Date(receita.data_vencimento);
    dataVencimento.setHours(0, 0, 0, 0);
    
    // Se a data de vencimento é anterior a hoje, está atrasada
    if (dataVencimento < hoje) {
      return "atrasada";
    }
    
    return "pendente";
  }
  
  // Fallback
  return receita.status || "pendente";
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    recebida: "success",
    pendente: "warning",
    atrasada: "error",
    cancelada: "error",
  };
  return colors[status] || "default";
};

const getStatusTextColor = (status: string) => {
  return "white";
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    recebida: "Paga",
    pendente: "Pendente",
    atrasada: "Atrasada",
    cancelada: "Cancelada",
  };
  return labels[status] || status;
};

const toggleStatus = () => {
  formData.value.status_lancamento = formData.value.status_lancamento === "EFETIVADA" ? "PENDENTE" : "EFETIVADA";
};

// Carrega dados do formulário da API
// Carrega dados do formulário da API
const loadFormData = async () => {
  try {
    // Load expenses categories and wallets data
    const { updateData: updateExpensesData } = useLancamentos("despesa");
    await updateExpensesData();
    
    // O useLancamentos já atualiza o walletsStore com os dados mais recentes da API
    // Não precisa fazer walletsStore.loadFromSession() pois os dados já foram atualizados
  } catch (error) {
    console.error("Erro ao carregar dados do formulário:", error);
  }
};

const selecionarRecorrencia = (item: string) => {
  formData.value.recorrencia = item;
  openRecorrenciaModal.value = false;

  if (item === "Parcelado") {
    openParcelas.value = true;
  }
};

const concluirParcelas = () => {
  openParcelas.value = false;
};

const openAddDialog = async () => {
  try {
    loadingForm.value = true;
    editingId.value = null;
    // Carrega dados dos stores se ainda não estiverem carregados
    await loadFormData();
    formData.value = {
      descricao: "",
      categoria: "Outros",
      conta: "",
      valor: "0,00",
      data_vencimento: new Date().toISOString().split("T")[0],
      status: "pendente",
      observacao: "",
      recorrencia: "Não recorrente",
      status_lancamento: "PENDENTE",
      subcategoria: "Outros",
      conta_id: contas.value[0]?.id || 1,
      data_lancamento: new Date().toISOString().split("T")[0],
      data_efetivacao: null,
      observacoes: "",
    };
    dialog.value = true;
  } catch (error) {
    console.error("Erro ao abrir formulário:", error);
    toastStore.error("Erro ao carregar dados do formulário");
  } finally {
    loadingForm.value = false;
  }
};

const editDespesa = (despesa: any) => {
  editingId.value = despesa.id;
  // ✅ Converter valor de centavos para string formatada "10,00"
  const valorFormatado = typeof despesa.valor === "number" 
    ? (despesa.valor / 100).toFixed(2).replace(".", ",")
    : despesa.valor;
  
  formData.value = { 
    ...despesa,
    valor: valorFormatado // ✅ Exibir valor formatado no formulário
  };
  dialog.value = true;
};

const deleteDespesa = async (id: number) => {
  if (confirm("Tem certeza que deseja deletar esta despesa?")) {
    try {
      loading.value = true;
      await despesasService.delete(id);
      toastStore.success("Despesa deletada com sucesso!");
      await loadDespesas();
    } catch (error: any) {
      console.error("Erro ao deletar despesa:", error);
      toastStore.error(error.message || "Erro ao deletar despesa");
    } finally {
      loading.value = false;
    }
  }
};

const efetivarDespesa = async (despesa: any) => {
  try {
    loading.value = true;
    // ✅ Converter valor para centavos (inteiro) para enviar ao backend
    let valorCentavos = despesa.valor;

    if (typeof despesa.valor === "string") {
      // Se for string "10,00", converte para 1000
      valorCentavos = Math.round(parseFloat(despesa.valor.replace(",", ".")) * 100);
    } else if (typeof despesa.valor === "number") {
      // Se já for número, assume que é centavos, mantém como está
      valorCentavos = despesa.valor;
    }
    
    const payload = {
      ...despesa,
      valor: valorCentavos, // ✅ Enviar valor em centavos (número inteiro)
      status_lancamento: "EFETIVADA",
      data_vencimento: formatDateForBackend(despesa.data_vencimento),
      data_lancamento: formatDateForBackend(despesa.data_lancamento),
      data_efetivacao: formatDateForBackend(despesa.data_efetivacao),
      tipo_lancamento: "Despesa"
    };

    await despesasService.update(despesa.id, payload);
    toastStore.success("Despesa efetivada com sucesso!");
    await loadDespesas();
  } catch (error: any) {
    console.error("Erro ao efetivar despesa:", error);
    toastStore.error(error.message || "Erro ao efetivar despesa");
  } finally {
    loading.value = false;
  }
};

const saveDespesa = async () => {
  loading.value = true;
  try {
    // Validar formulário
    if (!formRef.value?.validate()) {
      throw new Error("Preencha todos os campos obrigatórios");
    }

    // Mapear recorrência para formato da API (MAIÚSCULAS)
    const recorrenciaMap: { [key: string]: string } = {
      "Não recorrente": "NAO_RECORRENTE",
      "Fixa": "FIXA",
      "Parcelado": "PARCELADO",
    };

    // Obter mesAno no formato YYYY-MM
    const mesAno = userStore.getMesAno?.() || new Date().toISOString().slice(0, 7);

    // ✅ Construir payload com TODOS os campos esperados pelo backend
    const payload: any = {
      // Campos obrigatórios
      descricao: formData.value.descricao,
      valor: formData.value.valor,  // STRING formatada "10,00", backend faz conversão
      tipo_lancamento: "Despesa",   // ✅ "Despesa" (backend transforma para DESPESA)
      recorrencia: recorrenciaMap[formData.value.recorrencia] || "NAO_RECORRENTE",
      status_lancamento: formData.value.status_lancamento || "PENDENTE",
      categoria: formData.value.categoria,
      subcategoria: formData.value.subcategoria,
      conta_id: formData.value.conta_id,
      data_vencimento: formatDateForBackend(formData.value.data_vencimento),  // ✅ Formatar para YYYY-MM-DD
      data_lancamento: formatDateForBackend(formData.value.data_lancamento),  // ✅ Formatar para YYYY-MM-DD
      mesAno: mesAno,
      
      // Campos da interface Lancamento (preenchidos com valores padrão)
      id: editingId.value && formData.value.recorrencia === "Não recorrente" ? editingId.value : null,
      invoice_id: null,
      is_estorno: false,
      original_lancamento_id: null,
      data_efetivacao: formatDateForBackend(formData.value.data_efetivacao),  // ✅ Formatar se existir
      observacoes: formData.value.observacoes || null,
      fatura: null,
      cartao_id: null,
      user_id: null,
    };

    // Se for parcelado, adicionar dados de parcelas (MAIÚSCULAS)
    if (formData.value.recorrencia === "Parcelado") {
      payload.qtd_parcelas = tempNumParcelas.value;
      payload.num_parcela = tempParcelaInicial.value;
      payload.tipo_parcela = tipoCalculoParcela.value?.toLowerCase() || "total"; // total ou parcela
      payload.periodicidade = tempPeriodicidade.value?.toUpperCase() || "MENSAL";
    } else {
      payload.qtd_parcelas = null;
      payload.num_parcela = null;
      payload.tipo_parcela = null;
      payload.periodicidade = null;
    }

    console.log("Payload enviado:", payload);

    if (editingId.value && formData.value.recorrencia === "Não recorrente") {
      // ATUALIZAR apenas se for Não recorrente
      await despesasService.update(editingId.value, payload);
      toastStore.success("Despesa atualizada com sucesso!");
    } else {
      // CRIAR novo lançamento
      await despesasService.create(payload);
      if (editingId.value) {
        // Se estava editando FIXA ou PARCELADO, apagar o antigo
        await despesasService.delete(editingId.value);
        toastStore.success("Despesa atualizada com sucesso!");
      } else {
        toastStore.success("Despesa criada com sucesso!");
      }
    }

    // Fechar modal e recarregar dados
    dialog.value = false;
    await loadDespesas();
  } catch (error: any) {
    console.error("Erro ao salvar despesa:", error);
    toastStore.error(error.message || "Erro ao salvar despesa");
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  searchText.value = "";
  selectedStatus.value = "";
  selectedCategoria.value = "";
};

// Carregar despesas da API
const loadDespesas = async () => {
  try {
    loading.value = true;
    const mesAno = currentMonth.value;
    const data = await despesasService.list(mesAno);
    console.log(data);
    
    if (data && data.length > 0) {
      despesas.value = data.map((r: any) => ({
        id: r.id,
        descricao: r.descricao,
        valor: r.valor || 0,
        categoria: r.categoria,
        subcategoria: r.subcategoria,
        conta: r.conta?.name || "Conta",
        conta_id: r.conta_id,
        data_vencimento: r.data_vencimento,
        status: r.status_lancamento === "EFETIVADA" ? "recebida" : "pendente",
        observacao: r.observacao || "",
        recorrencia: r.recorrencia || "Não recorrente",
        status_lancamento: r.status_lancamento || "PENDENTE",
        data_lancamento: r.data_lancamento,
        data_efetivacao: r.data_efetivacao,
        observacoes: r.observacoes || "",
      }));
      variacao.value = data.variacao;
    } else {
      despesas.value = [];
    }
  } catch (error: any) {
    console.warn("Erro ao carregar despesas:", error?.message);
    toastStore.warning("Erro ao carregar despesas");
  } finally {
    loading.value = false;
    loadingMonth.value = false;
  }
};

// ✅ Watchers para fechar date pickers automaticamente após seleção
watch(() => formData.value.data_vencimento, (newVal) => {
  if (newVal) {
    menuDataVencimento.value = false; // Fechar date picker
  }
});

watch(() => formData.value.data_lancamento, (newVal) => {
  if (newVal) {
    menuDataLancamento.value = false; // Fechar date picker
  }
});

watch(() => formData.value.data_efetivacao, (newVal) => {
  if (newVal) {
    menuDataEfetivacao.value = false; // Fechar date picker
  }
});

// Watch for local month changes
onMounted(() => {
  // Reset to current month on mount to ensure fresh data
  currentMonth.value = new Date().toISOString().slice(0, 7);
  // Ativar loading ao carregar página
  loadingMonth.value = true;
  // Load data after resetting the month
  loadDespesas();
});

watch(() => currentMonth.value, () => {
  loadDespesas();
}, { immediate: false }); // Set to false to avoid double load on mount
</script>

<style scoped lang="scss">
.despesas-view {
  .view-header {
    @media (max-width: 600px) {
      .d-flex {
        flex-direction: column;
        align-items: flex-start;
        
        .v-btn {
          width: 100%;
        }
      }
    }
  }

  .summary-card {
    transition: all 0.3s ease;
    border-left: 4px solid;

    &.error-card {
      border-left-color: rgb(var(--v-theme-error));
    }

    &.warning-card {
      border-left-color: rgb(var(--v-theme-warning));
    }

    &.info-card {
      border-left-color: rgb(var(--v-theme-info));
    }

    &.danger-card {
      border-left-color: rgb(var(--v-theme-error));
    }

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  }

  .dialog-header {
    padding: 1.5rem;
    border-radius: 4px 4px 0 0;
  }

  .despesas-table {
    :deep(.v-data-table__wrapper) {
      border-radius: 4px;
    }
  }
}
</style>
