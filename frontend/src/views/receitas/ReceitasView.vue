<template>
  <div class="receitas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3">
        <div>
          <h1 class="text-h4 font-weight-bold d-flex align-center gap-2 mb-2">
            <v-icon icon="mdi-cash-plus" size="32" color="success" />
            Minhas Receitas
          </h1>
          <p class="text-subtitle-2 text-medium-emphasis mb-0">
            Gerencie suas receitas e ganhos
          </p>
        </div>
        <v-btn
          color="success"
          size="large"
          prepend-icon="mdi-plus"
          @click="openAddDialog"
          class="flex-shrink-0"
        >
          Nova Receita
        </v-btn>
      </div>
    </div>

    <!-- Summary Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card success-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="success" icon="mdi-cash-plus" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Total do Mês
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-success">
              {{ formatCurrency(summary.totalMes || 0) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatPercentage(summary.variacaoMes) }}
              <v-icon :icon="summary.variacaoMes >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'" :color="summary.variacaoMes >= 0 ? 'success' : 'error'" size="x-small" />
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card info-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="info" icon="mdi-calendar-check" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Recebidas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-info">
              {{ receitasRecebidas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaRecebidas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card warning-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="warning" icon="mdi-clock-outline" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Pendentes
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-warning">
              {{ receitasPendentes }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaPendentes) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card error-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="error" icon="mdi-calendar-remove" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Atrasadas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-error">
              {{ receitasAtrasadas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaAtrasadas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters and Controls -->
    <v-card class="mb-6" elevation="1">
      <v-card-text class="pa-4">
        <v-row class="align-center" dense>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="searchText"
              label="Buscar"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="selectedStatus"
              label="Status"
              :items="statusOptions"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="selectedCategoria"
              label="Categoria"
              :items="categorias"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
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

    <!-- Receitas Table -->
    <v-card elevation="1">
      <v-data-table
        :headers="headers"
        :items="filteredReceitas"
        :loading="loading"
        :items-per-page="itemsPerPage"
        class="receitas-table"
      >
        <!-- Data columns -->
        <template #item.descricao="{ item }">
          <div class="d-flex align-center gap-2">
            <v-avatar size="32" color="success" variant="tonal" icon="mdi-receipt" />
            <div>
              <div class="font-weight-500">{{ item.descricao }}</div>
              <div class="text-caption text-medium-emphasis">
                {{ formatDate(item.data_vencimento) }}
              </div>
            </div>
          </div>
        </template>

        <template #item.valor="{ item }">
          <div class="text-right font-weight-bold text-success">
            {{ formatCurrency(item.valor) }}
          </div>
        </template>

        <template #item.categoria="{ item }">
          <v-chip size="small" variant="outlined">
            {{ item.categoria }}
          </v-chip>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            :text-color="getStatusTextColor(item.status)"
            size="small"
            label
          >
            {{ getStatusLabel(item.status) }}
          </v-chip>
        </template>

        <template #item.acoes="{ item }">
          <div class="d-flex gap-1 justify-end">
            <v-btn
              icon="mdi-pencil"
              size="x-small"
              variant="text"
              color="primary"
              @click="editReceita(item)"
            />
            <v-btn
              icon="mdi-delete"
              size="x-small"
              variant="text"
              color="error"
              @click="deleteReceita(item.id)"
            />
          </div>
        </template>

        <template #no-data>
          <div class="text-center py-6">
            <v-icon size="48" color="medium-emphasis" class="mb-2">
              mdi-folder-open-outline
            </v-icon>
            <p class="text-medium-emphasis">Nenhuma receita encontrada</p>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Dialog Add/Edit -->
    <v-dialog v-model="dialog" max-width="700px">
      <v-card>
        <v-card-title class="d-flex align-center gap-2 pa-6 pb-3">
          <v-icon :icon="editingId ? 'mdi-pencil' : 'mdi-plus'" color="success" />
          {{ editingId ? 'Editar Receita' : 'Nova Receita' }}
        </v-card-title>

        <!-- Dialog Content -->
        <v-card-text class="pa-6">
          <v-form ref="formRef" @submit.prevent="saveReceita">
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
                <v-icon icon="mdi-refresh" class="me-2" />
                <div class="d-flex flex-column">
                  <span>{{ formData.recorrencia }}</span>
                  <span v-if="detalheRecorrencia" class="detalhe__parcela__interno">
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
                <v-btn class="toggle__btn" value="total" rounded="lg">
                  Valor total
                </v-btn>
                <v-btn class="toggle__btn" value="parcela" rounded="lg">
                  Valor parcela
                </v-btn>
              </v-btn-toggle>

              <div class="custom__underline" />
            </div>

            <!-- Modal Recorrência -->
            <v-menu v-model="openRecorrenciaModal" :close-on-content-click="false">
              <v-card width="300" class="mx-auto">
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
            </v-menu>

            <!-- Modal Parcelas -->
            <v-dialog v-model="openParcelas" max-width="400">
              <v-card>
                <v-card-title class="pa-4">Configurar Parcelas</v-card-title>
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
                      />
                      <v-btn
                        icon="mdi-plus"
                        size="x-small"
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
                  <v-btn variant="text" @click="openParcelas = false">
                    Cancelar
                  </v-btn>
                  <v-btn color="success" @click="concluirParcelas">
                    Concluído
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <!-- Row 4: Categoria e Subcategoria -->
            <v-row>
              <v-col cols="12" md="6">
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
              <v-col cols="12" md="6">
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
              <v-col cols="12" md="6">
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
                />
              </v-col>
              <v-col cols="12" md="6">
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
            <v-menu :close-on-content-click="false" transition="scale-transition">
              <template #activator="{ props }">
                <div class="custom__display__input" v-bind="props">
                  <div class="d-flex align-center text-grey">
                    <v-icon icon="mdi-calendar" class="me-3" />
                    <span>Data de Vencimento *</span>
                  </div>
                  <v-spacer class="m-0 p-0" />
                  <span class="font-weight-medium">{{ displayDataVencimento }}</span>
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
              style="color: rgb(var(--v-theme-success))"
              block
              class="my-4"
              @click="informacoes = !informacoes"
            >
              Mais informações
            </v-btn>

            <!-- Row 8: Data de Lançamento (Advanced) -->
            <v-menu
              v-if="informacoes"
              :close-on-content-click="false"
              transition="scale-transition"
            >
              <template #activator="{ props }">
                <div class="custom__display__input" v-bind="props">
                  <div class="d-flex align-center text-grey">
                    <v-icon icon="mdi-calendar" class="me-3" />
                    <span>Data de Lançamento</span>
                  </div>
                  <v-spacer />
                  <span class="font-weight-medium">{{ displayDataLancamento }}</span>
                </div>
              </template>

              <v-date-picker
                v-model="formData.data_lancamento"
                color="success"
                hide-header
                show-adjacent-months
              />
            </v-menu>

            <!-- Row 9: Data de Efetivação (Advanced) -->
            <v-menu
              v-if="informacoes"
              :close-on-content-click="false"
              transition="scale-transition"
              class="mt-4"
            >
              <template #activator="{ props }">
                <div class="custom__display__input" v-bind="props">
                  <div class="d-flex align-center text-grey">
                    <v-icon icon="mdi-calendar" class="me-3" />
                    <span>Data de Efetivação</span>
                  </div>
                  <v-spacer />
                  <span class="font-weight-medium">{{ displayDataEfetivacao }}</span>
                </div>
              </template>

              <v-date-picker
                v-model="formData.data_efetivacao"
                color="success"
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
              <v-btn variant="outlined" @click="dialog = false">
                Cancelar
              </v-btn>
              <v-btn
                color="success"
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
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { format, parseISO, isValid, isToday, isYesterday, isTomorrow } from 'date-fns';
import { ptBR } from 'date-fns/locale';

// State
const dialog = ref(false);
const formRef = ref();
const loading = ref(false);
const searchText = ref('');
const selectedStatus = ref('');
const selectedCategoria = ref('');
const editingId = ref<number | null>(null);
const itemsPerPage = ref(10);

// Recurrence State
const openRecorrenciaModal = ref(false);
const openParcelas = ref(false);
const informacoes = ref(false);
const tiposRecorrencia = ref(['Não recorrente', 'Fixa', 'Parcelado']);
const tipoCalculoParcela = ref('total');
const tempParcelaInicial = ref(1);
const tempNumParcelas = ref(2);
const tempPeriodicidade = ref('Mensal');

// Mock data
const receitas = ref([
  {
    id: 1,
    descricao: 'Salário',
    valor: 5000,
    categoria: 'Salário',
    conta: 'Conta Principal',
    data_vencimento: '2025-10-01',
    status: 'recebida',
    observacao: 'Salário mensal',
    recorrencia: 'Fixa',
    status_lancamento: 'EFETIVADA',
    subcategoria: 'Salário',
    conta_id: 1,
    data_lancamento: '2025-10-01',
    data_efetivacao: '2025-10-01',
    observacoes: 'Salário mensal',
  },
  {
    id: 2,
    descricao: 'Freelancer',
    valor: 1200,
    categoria: 'Freelancer',
    conta: 'Conta Principal',
    data_vencimento: '2025-10-05',
    status: 'recebida',
    observacao: 'Projeto web',
    recorrencia: 'Não recorrente',
    status_lancamento: 'EFETIVADA',
    subcategoria: 'Projeto',
    conta_id: 1,
    data_lancamento: '2025-10-05',
    data_efetivacao: '2025-10-05',
    observacoes: 'Projeto web',
  },
  {
    id: 3,
    descricao: 'Bonus',
    valor: 800,
    categoria: 'Bonus',
    conta: 'Conta Principal',
    data_vencimento: '2025-10-20',
    status: 'pendente',
    observacao: 'Bonus do mês',
    recorrencia: 'Não recorrente',
    status_lancamento: 'PENDENTE',
    subcategoria: 'Bônus mensal',
    conta_id: 1,
    data_lancamento: '2025-10-20',
    data_efetivacao: null,
    observacoes: 'Bonus do mês',
  },
]);

const categorias = ref(['Salário', 'Freelancer', 'Bonus', 'Investimento', 'Outros']);
const categoriasNames = ref(['Salário', 'Freelancer', 'Bonus', 'Investimento', 'Outros']);
const subcategorias = ref({
  'Salário': ['Salário', 'Décimo terceiro'],
  'Freelancer': ['Projeto', 'Consultoria'],
  'Bonus': ['Bônus mensal', 'Bônus anual'],
  'Investimento': ['Ações', 'Renda fixa'],
  'Outros': ['Outros'],
});

const contas = ref([
  { id: 1, name: 'Conta Principal' },
  { id: 2, name: 'Conta Investimento' },
  { id: 3, name: 'Poupança' },
]);

const statusOptions = ref([
  'recebida',
  'pendente',
  'cancelada',
]);

// Form data
const formData = ref({
  descricao: '',
  categoria: '',
  conta: '',
  valor: '0,00',
  data_vencimento: new Date().toISOString().split('T')[0],
  status: 'pendente',
  observacao: '',
  recorrencia: 'Não recorrente',
  status_lancamento: 'PENDENTE',
  subcategoria: '',
  conta_id: null,
  data_lancamento: new Date().toISOString().split('T')[0],
  data_efetivacao: null,
  observacoes: '',
});

// Computed properties
const subcategoriasDaCategoriaSelecionada = computed(() => {
  return subcategorias.value[formData.value.categoria] || [];
});

const detalheRecorrencia = computed(() => {
  if (formData.value.recorrencia === 'Parcelado') {
    return `${tempNumParcelas.value} parcelas, começando na ${tempParcelaInicial.value}ª - ${tempPeriodicidade.value}`;
  }
  return '';
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

// Summary computed
const summary = computed(() => ({
  totalMes: receitas.value.reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, '').replace(',', '.')), 0),
  variacaoMes: 5.2,
}));

const receitasRecebidas = computed(() => receitas.value.filter(r => r.status === 'recebida').length);
const somaRecebidas = computed(() => receitas.value.filter(r => r.status === 'recebida').reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, '').replace(',', '.')), 0));

const receitasPendentes = computed(() => receitas.value.filter(r => r.status === 'pendente').length);
const somaPendentes = computed(() => receitas.value.filter(r => r.status === 'pendente').reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, '').replace(',', '.')), 0));

const receitasAtrasadas = computed(() => receitas.value.filter(r => r.status === 'cancelada').length);
const somaAtrasadas = computed(() => receitas.value.filter(r => r.status === 'cancelada').reduce((sum, r) => sum + parseFloat((r.valor || 0).toString().replace(/\./g, '').replace(',', '.')), 0));

// Filtered receitas
const filteredReceitas = computed(() => {
  return receitas.value.filter(r => {
    const matchText = !searchText.value || r.descricao.toLowerCase().includes(searchText.value.toLowerCase());
    const matchStatus = !selectedStatus.value || r.status === selectedStatus.value;
    const matchCategoria = !selectedCategoria.value || r.categoria === selectedCategoria.value;
    return matchText && matchStatus && matchCategoria;
  });
});

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
  minLength3: (v: string) => (v && v.length >= 3) || 'Mínimo 3 caracteres',
  valorPositivo: (v: string) => {
    if (!v) return 'Valor obrigatório';
    const numValue = parseFloat(v.replace(/\./g, '').replace(',', '.'));
    return numValue > 0 || 'Valor deve ser maior que zero';
  },
};

// Headers
const headers = [
  { title: 'Descrição', align: 'start', key: 'descricao', width: '35%' },
  { title: 'Categoria', align: 'start', key: 'categoria', width: '15%' },
  { title: 'Valor', align: 'end', key: 'valor', width: '15%' },
  { title: 'Status', align: 'center', key: 'status', width: '15%' },
  { title: 'Ações', align: 'end', key: 'acoes', width: '10%', sortable: false },
];

// Methods
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
};

const formatPercentage = (value: number) => {
  return `${value >= 0 ? '+' : ''}${value.toFixed(1)}%`;
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR');
};

const formatValueDisplay = () => {
  let digits = (formData.value.valor || '').replace(/\D/g, '');
  digits = digits.replace(/^0+/, '') || '0';
  while (digits.length < 3) digits = '0' + digits;

  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  formData.value.valor = `${formattedIntegerPart},${decimalPart}`;
};

const formatDateForDisplay = (dateValue: string | Date | undefined | null): string => {
  if (!dateValue) return 'Selecione...';

  const data = typeof dateValue === 'string' ? parseISO(dateValue) : dateValue;
  if (!isValid(data)) return 'Data inválida';

  if (isToday(data)) return 'Hoje';
  if (isYesterday(data)) return 'Ontem';
  if (isTomorrow(data)) return 'Amanhã';

  const nomeDiaCompleto = format(data, 'EEEE', { locale: ptBR });
  const diaAbreviadoCapitalizado = nomeDiaCompleto.charAt(0).toUpperCase() + nomeDiaCompleto.slice(1, 3);
  const dataFormatada = format(data, 'dd/MM/yyyy');

  return `${diaAbreviadoCapitalizado}., ${dataFormatada}`;
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    recebida: 'success',
    pendente: 'warning',
    cancelada: 'error',
  };
  return colors[status] || 'default';
};

const getStatusTextColor = (status: string) => {
  return 'white';
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    recebida: 'Recebida',
    pendente: 'Pendente',
    cancelada: 'Cancelada',
  };
  return labels[status] || status;
};

const toggleStatus = () => {
  formData.value.status_lancamento = formData.value.status_lancamento === 'EFETIVADA' ? 'PENDENTE' : 'EFETIVADA';
};

const selecionarRecorrencia = (item: string) => {
  formData.value.recorrencia = item;
  openRecorrenciaModal.value = false;

  if (item === 'Parcelado') {
    openParcelas.value = true;
  }
};

const concluirParcelas = () => {
  openParcelas.value = false;
};

const openAddDialog = () => {
  editingId.value = null;
  formData.value = {
    descricao: '',
    categoria: '',
    conta: '',
    valor: '0,00',
    data_vencimento: new Date().toISOString().split('T')[0],
    status: 'pendente',
    observacao: '',
    recorrencia: 'Não recorrente',
    status_lancamento: 'PENDENTE',
    subcategoria: '',
    conta_id: 1,
    data_lancamento: new Date().toISOString().split('T')[0],
    data_efetivacao: null,
    observacoes: '',
  };
  dialog.value = true;
};

const editReceita = (receita: any) => {
  editingId.value = receita.id;
  formData.value = { ...receita };
  dialog.value = true;
};

const deleteReceita = (id: number) => {
  if (confirm('Tem certeza que deseja deletar esta receita?')) {
    receitas.value = receitas.value.filter(r => r.id !== id);
  }
};

const saveReceita = () => {
  if (editingId.value) {
    // Update
    const index = receitas.value.findIndex(r => r.id === editingId.value);
    if (index !== -1) {
      receitas.value[index] = { ...formData.value, id: editingId.value };
    }
  } else {
    // Create
    const newId = Math.max(...receitas.value.map(r => r.id), 0) + 1;
    receitas.value.push({ ...formData.value, id: newId });
  }
  dialog.value = false;
};

const resetFilters = () => {
  searchText.value = '';
  selectedStatus.value = '';
  selectedCategoria.value = '';
};
</script>

<style scoped lang="scss">
.receitas-view {
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

    &.success-card {
      border-left-color: rgb(var(--v-theme-success));
    }

    &.info-card {
      border-left-color: rgb(var(--v-theme-info));
    }

    &.warning-card {
      border-left-color: rgb(var(--v-theme-warning));
    }

    &.error-card {
      border-left-color: rgb(var(--v-theme-error));
    }

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  }

  .custom__input__container {
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    padding-bottom: 0.5rem;
  }

  .custom__input__content {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background 0.2s;

    &:hover {
      background: rgba(255, 255, 255, 0.05);
    }
  }

  .detalhe__parcela__interno {
    font-size: 12px;
    opacity: 0.7;
  }

  .parcela__toggle {
    width: 100%;
    margin-top: 1rem;
  }

  .toggle__btn {
    flex: 1;
    text-transform: none;
  }

  .custom__underline {
    width: 100%;
    height: 1px;
    background: rgba(255, 255, 255, 0.12);
    margin-top: 0.5rem;
  }

  .custom__display__input {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      opacity: 0.8;
    }
  }

  .switch__check {
    width: 40px;
    height: 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.3s;

    &--inner {
      width: 20px;
      height: 20px;
      background: rgba(255, 255, 255, 0.4);
      border-radius: 50%;
      position: absolute;
      left: 2px;
      transition: all 0.3s;
    }
  }

  .switch__check__efetivada {
    width: 40px;
    height: 24px;
    background: rgb(var(--v-theme-success));
    border-radius: 12px;
    display: flex;
    align-items: center;
    position: relative;
    transition: all 0.3s;

    &--inner {
      width: 20px;
      height: 20px;
      background: white;
      border-radius: 50%;
      position: absolute;
      right: 2px;
      transition: all 0.3s;
    }
  }

  .text-grey {
    opacity: 0.7;
  }
}
</style>
