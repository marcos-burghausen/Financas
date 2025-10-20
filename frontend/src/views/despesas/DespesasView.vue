<template>
  <div class="despesas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3">
        <div>
          <h1 class="text-h4 font-weight-bold d-flex align-center gap-2 mb-2">
            <v-icon icon="mdi-cash-remove" size="32" color="error" />
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
          @click="openAddDialog"
          class="flex-shrink-0"
        >
          Nova Despesa
        </v-btn>
      </div>
    </div>

    <!-- 📅 Month Navigation -->
    <v-card class="mb-6" elevation="1">
      <v-card-text class="pa-4">
        <div class="d-flex align-center justify-center gap-4">
          <v-btn
            icon="mdi-chevron-left"
            color="primary"
            variant="outlined"
            size="small"
            @click="goToPreviousMonth"
            title="Mês anterior"
          />
          <div class="text-center" style="min-width: 250px">
            <v-btn
              variant="text"
              :text="getMonthName(selectedMonth).toUpperCase()"
              @click="goToCurrentMonth"
              :class="{ 'text-primary font-weight-bold': selectedMonth === new Date().toISOString().slice(0, 7) }"
              title="Ir para o mês atual"
            />
          </div>
          <v-btn
            icon="mdi-chevron-right"
            color="primary"
            variant="outlined"
            size="small"
            @click="goToNextMonth"
            title="Próximo mês"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- Summary Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card error-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="error" icon="mdi-cash-remove" />
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
              <v-icon :icon="summary.variacaoMes >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'" :color="summary.variacaoMes >= 0 ? 'error' : 'success'" size="x-small" />
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card warning-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="warning" icon="mdi-calendar-check" />
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

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card info-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="info" icon="mdi-clock-outline" />
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

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card danger-card" elevation="2">
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
            <v-avatar size="32" color="error" variant="tonal" icon="mdi-receipt" />
            <div>
              <div class="font-weight-500">{{ item.descricao }}</div>
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
          <v-chip size="small" variant="outlined">
            {{ item.categoria }}
          </v-chip>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(getStatusReal(item))"
            :text-color="getStatusTextColor(getStatusReal(item))"
            size="small"
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
              @click="efetivarDespesa(item)"
              title="Marcar como paga"
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
            <v-icon icon="mdi-inbox" size="48" color="medium-emphasis" class="mb-4 d-block" />
            <p class="text-subtitle-1 text-medium-emphasis">Nenhuma despesa encontrada</p>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
        <!-- Dialog Header -->
        <div class="dialog-header bg-error">
          <div class="d-flex justify-space-between align-center">
            <h2 class="text-h6 text-white font-weight-bold">
              {{ editingId ? 'Editar Despesa' : 'Nova Despesa' }}
            </h2>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="dialog = false"
            />
          </div>
        </div>

        <!-- Dialog Content -->
        <v-card-text class="pa-6">
          <v-form @submit.prevent="saveDespesa">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="formData.descricao"
                  label="Descrição *"
                  prepend-inner-icon="mdi-text"
                  variant="outlined"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="formData.categoria"
                  label="Categoria *"
                  prepend-inner-icon="mdi-tag"
                  variant="outlined"
                  :items="categorias"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="formData.conta"
                  label="Conta *"
                  prepend-inner-icon="mdi-bank"
                  variant="outlined"
                  :items="contas"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="formData.valor"
                  label="Valor *"
                  prepend-inner-icon="mdi-currency-brl"
                  variant="outlined"
                  type="number"
                  step="0.01"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="formData.data_vencimento"
                  label="Data de Vencimento *"
                  prepend-inner-icon="mdi-calendar"
                  variant="outlined"
                  type="date"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12">
                <v-select
                  v-model="formData.status"
                  label="Status *"
                  prepend-inner-icon="mdi-checkbox-marked-circle"
                  variant="outlined"
                  :items="statusOptions"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="formData.observacao"
                  label="Observação"
                  prepend-inner-icon="mdi-note"
                  variant="outlined"
                  rows="3"
                />
              </v-col>
            </v-row>

            <div class="d-flex gap-2 justify-end mt-6">
              <v-btn variant="outlined" @click="dialog = false">
                Cancelar
              </v-btn>
              <v-btn color="error" type="submit">
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
import despesasService from '@/services/despesas.service';
import { useToastStore } from '@/store/toast';
import { useUserStore } from '@/store/user';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { computed, onMounted, ref, watch } from 'vue';

const toastStore = useToastStore();
const userStore = useUserStore();

// State
const dialog = ref(false);
const loading = ref(false);
const searchText = ref('');
const selectedStatus = ref('');
const selectedCategoria = ref('');
const editingId = ref<number | null>(null);
const itemsPerPage = ref(10);

// ✅ Refs para controlar date pickers
const menuDataVencimento = ref(false);
const menuDataLancamento = ref(false);
const menuDataEfetivacao = ref(false);

// 📅 Month Navigation State
const selectedMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM format

// Mock data
const despesas = ref([
  { id: 1, descricao: 'Aluguel', valor: 1500, categoria: 'Moradia', conta: 'Conta Principal', data_vencimento: '2025-10-01', status: 'paga', observacao: 'Aluguel mensal' },
  { id: 2, descricao: 'Supermercado', valor: 450, categoria: 'Alimentação', conta: 'Conta Principal', data_vencimento: '2025-10-05', status: 'paga', observacao: 'Compras semanais' },
  { id: 3, descricao: 'Internet', valor: 120, categoria: 'Utilidades', conta: 'Conta Principal', data_vencimento: '2025-10-10', status: 'pendente', observacao: 'Internet banda larga' },
  { id: 4, descricao: 'Uber', valor: 85, categoria: 'Transporte', conta: 'Conta Principal', data_vencimento: '2025-10-15', status: 'pendente', observacao: 'Deslocamento' },
]);

const categorias = ref(['Moradia', 'Alimentação', 'Transporte', 'Utilidades', 'Saúde', 'Educação', 'Lazer', 'Outros']);
const contas = ref(['Conta Principal', 'Conta Investimento', 'Poupança']);
const statusOptions = ref([
  { title: 'Paga', value: 'paga' },
  { title: 'Pendente', value: 'pendente' },
  { title: 'Cancelada', value: 'cancelada' },
]);

// Form data
const formData = ref({
  descricao: '',
  categoria: '',
  conta: '',
  valor: 0,
  data_vencimento: '',
  status: 'pendente',
  observacao: '',
});

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
};

// Headers da tabela
const headers = [
  { title: 'Descrição', align: 'start', key: 'descricao', width: '35%' },
  { title: 'Categoria', align: 'start', key: 'categoria', width: '15%' },
  { title: 'Valor', align: 'end', key: 'valor', width: '15%' },
  { title: 'Status', align: 'center', key: 'status', width: '15%' },
  { title: 'Ações', align: 'end', key: 'acoes', width: '10%', sortable: false },
];

// Summary computed
const summary = computed(() => ({
  totalMes: despesas.value.reduce((sum, d) => sum + d.valor, 0),
  variacaoMes: 8.5,
}));

const despesasPagas = computed(() => despesas.value.filter(d => getStatusReal(d) === 'paga').length);
const somaPagas = computed(() => despesas.value.filter(d => getStatusReal(d) === 'paga').reduce((sum, d) => sum + d.valor, 0));

const despesasPendentes = computed(() => despesas.value.filter(d => getStatusReal(d) === 'pendente').length);
const somaPendentes = computed(() => despesas.value.filter(d => getStatusReal(d) === 'pendente').reduce((sum, d) => sum + d.valor, 0));

const despesasAtrasadas = computed(() => despesas.value.filter(d => getStatusReal(d) === 'atrasada').length);
const somaAtrasadas = computed(() => despesas.value.filter(d => getStatusReal(d) === 'atrasada').reduce((sum, d) => sum + d.valor, 0));

// Filtered despesas
const filteredDespesas = computed(() => {
  return despesas.value.filter(d => {
    const matchText = !searchText.value || d.descricao.toLowerCase().includes(searchText.value.toLowerCase());
    const matchStatus = !selectedStatus.value || getStatusReal(d) === selectedStatus.value;
    const matchCategoria = !selectedCategoria.value || d.categoria === selectedCategoria.value;
    return matchText && matchStatus && matchCategoria;
  });
});

// Methods
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value / 100);
};

const formatPercentage = (value: number) => {
  return `${value >= 0 ? '+' : ''}${value.toFixed(1)}%`;
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR');
};

// 📅 Month Navigation Functions
const getMonthName = (mesAnoString: string): string => {
  const [ano, mes] = mesAnoString.split('-');
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  return format(date, 'MMMM yyyy', { locale: ptBR });
};

const goToPreviousMonth = () => {
  const [ano, mes] = selectedMonth.value.split('-');
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() - 1);
  const newMonth = date.toISOString().slice(0, 7);
  selectedMonth.value = newMonth;
  userStore.setMesAno(newMonth);
  loadDespesas();
};

const goToNextMonth = () => {
  const [ano, mes] = selectedMonth.value.split('-');
  const date = new Date(parseInt(ano), parseInt(mes) - 1, 1);
  date.setMonth(date.getMonth() + 1);
  const newMonth = date.toISOString().slice(0, 7);
  selectedMonth.value = newMonth;
  userStore.setMesAno(newMonth);
  loadDespesas();
};

const goToCurrentMonth = () => {
  const currentMonth = new Date().toISOString().slice(0, 7);
  selectedMonth.value = currentMonth;
  userStore.setMesAno(currentMonth);
  loadDespesas();
};

// ✅ Função para calcular o status real baseado na data de vencimento
const getStatusReal = (despesa: any): string => {
  // Se status for EFETIVADA (paga), retorna paga
  if (despesa.status_lancamento === 'EFETIVADA') {
    return 'paga';
  }
  
  // Se status for PENDENTE, verifica se está atrasada
  if (despesa.status_lancamento === 'PENDENTE') {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    
    let dataVencimento = new Date(despesa.data_vencimento);
    dataVencimento.setHours(0, 0, 0, 0);
    
    // Se a data de vencimento é anterior a hoje, está atrasada
    if (dataVencimento < hoje) {
      return 'atrasada';
    }
    
    return 'pendente';
  }
  
  // Fallback
  return despesa.status || 'pendente';
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    paga: 'success',
    pendente: 'warning',
    atrasada: 'error',
    cancelada: 'error',
  };
  return colors[status] || 'default';
};

const getStatusTextColor = (status: string) => {
  return 'white';
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    paga: 'Paga',
    pendente: 'Pendente',
    atrasada: 'Atrasada',
    cancelada: 'Cancelada',
  };
  return labels[status] || status;
};

const openAddDialog = () => {
  editingId.value = null;
  formData.value = {
    descricao: '',
    categoria: '',
    conta: '',
    valor: 0,
    data_vencimento: '',
    status: 'pendente',
    observacao: '',
  };
  dialog.value = true;
};

const editDespesa = (despesa: any) => {
  editingId.value = despesa.id;
  // ✅ Converter valor de centavos para string formatada "10,00"
  const valorFormatado = typeof despesa.valor === 'number' 
    ? (despesa.valor / 100).toFixed(2).replace('.', ',')
    : despesa.valor;
  
  formData.value = { 
    ...despesa,
    valor: valorFormatado // ✅ Exibir valor formatado no formulário
  };
  dialog.value = true;
};

const deleteDespesa = async (id: number) => {
  if (confirm('Tem certeza que deseja deletar esta despesa?')) {
    try {
      loading.value = true;
      await despesasService.delete(id);
      toastStore.success('Despesa deletada com sucesso!');
      await loadDespesas();
    } catch (error: any) {
      console.error('Erro ao deletar despesa:', error);
      toastStore.error(error.message || 'Erro ao deletar despesa');
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
    
    if (typeof despesa.valor === 'string') {
      // Se for string "10,00", converte para 1000
      valorCentavos = Math.round(parseFloat(despesa.valor.replace(',', '.')) * 100);
    } else if (typeof despesa.valor === 'number') {
      // Se já for número, assume que é centavos, mantém como está
      valorCentavos = despesa.valor;
    }
    
    const payload = {
      ...despesa,
      valor: valorCentavos, // ✅ Enviar valor em centavos (número inteiro)
      status_lancamento: 'EFETIVADA',
      data_vencimento: formatDateForBackend(despesa.data_vencimento),
      data_lancamento: formatDateForBackend(despesa.data_lancamento),
      data_efetivacao: formatDateForBackend(despesa.data_efetivacao),
      tipo_lancamento: 'Despesa'
    };
    
    await despesasService.update(despesa.id, payload);
    toastStore.success('Despesa paga com sucesso!');
    await loadDespesas();
  } catch (error: any) {
    console.error('Erro ao efetivar despesa:', error);
    toastStore.error(error.message || 'Erro ao efetivar despesa');
  } finally {
    loading.value = false;
  }
};

const saveDespesa = async () => {
  loading.value = true;
  try {
    // Validar formulário
    if (!formRef.value?.validate()) {
      throw new Error('Preencha todos os campos obrigatórios');
    }

    // Mapear recorrência para formato da API (MAIÚSCULAS)
    const recorrenciaMap: { [key: string]: string } = {
      'Não recorrente': 'NAO_RECORRENTE',
      'Fixa': 'FIXA',
      'Parcelado': 'PARCELADO',
    };

    // Obter mesAno no formato YYYY-MM
    const mesAno = userStore.getMesAno?.() || new Date().toISOString().slice(0, 7);

    // ✅ Construir payload com TODOS os campos esperados pelo backend
    const payload: any = {
      // Campos obrigatórios
      descricao: formData.value.descricao,
      valor: formData.value.valor,  // STRING formatada "10,00", backend faz conversão
      tipo_lancamento: 'Despesa',   // ✅ "Despesa" (backend transforma para DESPESA)
      recorrencia: recorrenciaMap[formData.value.recorrencia] || 'NAO_RECORRENTE',
      status_lancamento: formData.value.status_lancamento || 'PENDENTE',
      categoria: formData.value.categoria,
      subcategoria: formData.value.subcategoria,
      conta_id: formData.value.conta_id,
      data_vencimento: formatDateForBackend(formData.value.data_vencimento),  // ✅ Formatar para YYYY-MM-DD
      data_lancamento: formatDateForBackend(formData.value.data_lancamento),  // ✅ Formatar para YYYY-MM-DD
      mesAno: mesAno,
      
      // Campos da interface Lancamento (preenchidos com valores padrão)
      id: editingId.value && formData.value.recorrencia === 'Não recorrente' ? editingId.value : null,
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
    if (formData.value.recorrencia === 'Parcelado') {
      payload.qtd_parcelas = tempNumParcelas.value;
      payload.num_parcela = tempParcelaInicial.value;
      payload.tipo_parcela = tipoCalculoParcela.value?.toLowerCase() || 'total'; // total ou parcela
      payload.periodicidade = tempPeriodicidade.value?.toUpperCase() || 'MENSAL';
    } else {
      payload.qtd_parcelas = null;
      payload.num_parcela = null;
      payload.tipo_parcela = null;
      payload.periodicidade = null;
    }

    console.log('Payload enviado:', payload);

    if (editingId.value && formData.value.recorrencia === 'Não recorrente') {
      // ATUALIZAR apenas se for Não recorrente
      await despesasService.update(editingId.value, payload);
      toastStore.success('Despesa atualizada com sucesso!');
    } else {
      // CRIAR novo lançamento
      await despesasService.create(payload);
      if (editingId.value) {
        // Se estava editando FIXA ou PARCELADO, apagar o antigo
        await despesasService.delete(editingId.value);
        toastStore.success('Despesa atualizada com sucesso!');
      } else {
        toastStore.success('Despesa criada com sucesso!');
      }
    }

    // Fechar modal e recarregar dados
    dialog.value = false;
    await loadDespesas();
  } catch (error: any) {
    console.error('Erro ao salvar despesa:', error);
    toastStore.error(error.message || 'Erro ao salvar despesa');
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  searchText.value = '';
  selectedStatus.value = '';
  selectedCategoria.value = '';
};

// Carregar despesas da API
const loadDespesas = async () => {
  try {
    loading.value = true;
    const mesAno = userStore.getMesAno?.();
    const data = await despesasService.list(mesAno);
    if (data && data.length > 0) {
      despesas.value = data.map((d: any) => ({
        id: d.id,
        descricao: d.descricao,
        valor: d.valor || 0,  // Valor em centavos da API
        categoria: d.categoria,  // ✅ Sem fallback
        subcategoria: d.subcategoria,  // ✅ Sem fallback
        conta: d.conta?.name || 'Conta',
        conta_id: d.conta_id,
        data_vencimento: d.data_vencimento,
        status: d.status_lancamento === 'EFETIVADA' ? 'paga' : 'pendente',
        observacao: d.observacao || '',
        recorrencia: d.recorrencia || 'Não recorrente',
        status_lancamento: d.status_lancamento || 'PENDENTE',
        data_lancamento: d.data_lancamento,
        data_efetivacao: d.data_efetivacao,
        observacoes: d.observacoes || '',
      }));
    } else {
      despesas.value = [];
    }
  } catch (error: any) {
    console.warn('Erro ao carregar despesas:', error?.message);
    toastStore.warning('Erro ao carregar despesas');
  } finally {
    loading.value = false;
  }
};

// ✅ Formatar data para enviar ao backend (YYYY-MM-DD)
const formatDateForBackend = (dateValue: string | Date | undefined | null): string => {
  if (!dateValue) return '';
  
  try {
    // Se for string ISO com timezone, extrair apenas a data
    if (typeof dateValue === 'string' && dateValue.includes('T')) {
      return dateValue.split('T')[0]; // "2025-10-09T03:00:00.000Z" → "2025-10-09"
    }
    
    // Se for string em formato YYYY-MM-DD, retornar como está
    if (typeof dateValue === 'string' && dateValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
      return dateValue;
    }
    
    // Se for Date object, formatar
    if (dateValue instanceof Date) {
      const year = dateValue.getFullYear();
      const month = String(dateValue.getMonth() + 1).padStart(2, '0');
      const day = String(dateValue.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }
    
    return '';
  } catch (error) {
    console.error('Erro ao formatar data:', dateValue, error);
    return '';
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

onMounted(() => {
  loadDespesas();
});
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
