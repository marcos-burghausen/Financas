<template>
  <v-layout>
    <v-navigation-drawer v-model="drawer" temporary color="#212529" width="280">
      <v-list>
        <v-list-item
          v-for="(item, index) in filteredItensSideBar"
          :key="index"
          :to="{ name: item.route }"
          :class="{ 'bg-primary': isActiveRoute(item.route) }"
        >
          <template #prepend>
            <v-icon :icon="item.icon" />
          </template>
          <v-list-item-title>{{ item.name }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container fluid class="pa-6">
        <v-row class="mb-4">
          <v-col cols="12">
            <div class="d-flex align-center justify-space-between mb-2">
              <div class="d-flex align-center">
                <v-btn icon variant="text" @click="drawer = !drawer" class="mr-2 d-lg-none">
                  <v-icon icon="mdi-menu" size="28" />
                </v-btn>
                <div>
                  <h1 class="text-h4 mb-1 d-flex align-center">
                    <v-icon icon="mdi-credit-card-outline" size="36" class="mr-3" color="primary" />
                    Cartões de Crédito
                  </h1>
                  <p class="text-subtitle-1 text-grey mb-0">
                    Gerencie seus cartões e faturas
                  </p>
                </div>
              </div>
              <v-btn color="primary" @click="openAddCardDialog" prepend-icon="mdi-plus-circle">
                Novo Cartão
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <v-row v-if="loading">
          <v-col cols="12" class="text-center py-12">
            <v-progress-circular indeterminate color="primary" size="64" />
            <p class="text-grey mt-4">Carregando cartões...</p>
          </v-col>
        </v-row>

        <v-row v-else-if="!creditCards || creditCards.length === 0">
          <v-col cols="12">
            <v-card class="text-center py-12" elevation="2">
              <v-icon icon="mdi-credit-card-off-outline" size="64" color="grey-lighten-1" />
              <h2 class="text-h6 text-grey mt-4">Nenhum cartão de crédito encontrado</h2>
              <p class="text-grey-darken-1">Adicione seu primeiro cartão para começar a gerenciar.</p>
              <v-btn color="primary" @click="openAddCardDialog" class="mt-4">
                Adicionar Cartão
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col
            v-for="card in creditCards"
            :key="card.id"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card elevation="4" class="h-100 d-flex flex-column">
              <v-card-title class="d-flex align-center justify-space-between">
                <div>
                  <v-icon :icon="card.icon || 'mdi-credit-card'" class="mr-2" />
                  {{ card.nome }}
                </div>
                <v-menu>
                  <template v-slot:activator="{ props }">
                    <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props" />
                  </template>
                  <v-list>
                    <v-list-item @click="openEditCardDialog(card)">
                      <template #prepend>
                        <v-icon icon="mdi-pencil" />
                      </template>
                      <v-list-item-title>Editar</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openDeleteCardDialog(card)">
                      <template #prepend>
                        <v-icon icon="mdi-delete" />
                      </template>
                      <v-list-item-title>Excluir</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </v-card-title>
              <v-card-text>
                <div class="d-flex justify-space-between mb-2">
                  <span class="font-weight-medium">Limite:</span>
                  <span>{{ formatCurrency(card.limite) }}</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="font-weight-medium">Vencimento:</span>
                  <span>Dia {{ card.dia_vencimento }}</span>
                </div>
                <div class="d-flex justify-space-between">
                  <span class="font-weight-medium">Fechamento:</span>
                  <span>Dia {{ card.dia_fechamento }}</span>
                </div>
              </v-card-text>
              <v-spacer />
              <v-card-actions class="px-4 pb-4">
                <v-btn
                  block
                  color="primary"
                  variant="tonal"
                  @click="viewInvoices(card)"
                  prepend-icon="mdi-text-box-outline"
                >
                  Ver Faturas
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <v-row v-if="selectedCard" class="mt-6">
          <v-col cols="12">
            <v-card elevation="4">
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon icon="mdi-text-box-outline" size="28" color="white" class="mr-3" />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">
                        Faturas de {{ selectedCard.nome }}
                      </h3>
                    </div>
                  </div>
                  <v-btn icon="mdi-close" variant="text" color="white" @click="selectedCard = null" />
                </div>
              </div>

              <v-card-text v-if="loadingInvoices" class="text-center py-12">
                <v-progress-circular indeterminate color="primary" size="48" />
                <p class="text-grey mt-4">Carregando faturas...</p>
              </v-card-text>
              
              <v-table v-else-if="invoices.length > 0">
                <thead>
                  <tr>
                    <th>Mês/Ano</th>
                    <th>Valor Total</th>
                    <th>Data de Vencimento</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="invoice in invoices" :key="invoice.id">
                    <td>{{ invoice.mes }}/{{ invoice.ano }}</td>
                    <td>{{ formatCurrency(invoice.valor_total) }}</td>
                    <td>{{ formatDate(invoice.data_vencimento) }}</td>
                    <td>
                      <v-chip :color="invoice.pago ? 'success' : 'warning'" size="small">
                        {{ invoice.pago ? 'Paga' : 'Aberta' }}
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
              </v-table>
              
              <v-card-text v-else class="text-center py-12">
                <v-icon icon="mdi-file-document-outline" size="48" color="grey-lighten-1" />
                <p class="text-grey mt-2">Nenhuma fatura encontrada</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>

    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ isEditing ? 'Editar' : 'Adicionar' }} Cartão de Crédito</span>
        </v-card-title>
        <v-card-text>
          <FormContaCartao
            ref="formContaCartaoRef"
            :initialData="editableCard"
            :isCard="true"
            @salvar="saveCard"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="blue darken-1" text @click="closeDialog">Cancelar</v-btn>
          <v-btn color="blue darken-1" @click="submitForm">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    
    <v-dialog v-model="deleteDialog" persistent max-width="400px">
        <v-card>
            <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
            <v-card-text>
                Tem certeza que deseja excluir o cartão <strong>{{ cardToDelete?.nome }}</strong>? Esta ação não pode ser desfeita.
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn text @click="deleteDialog = false">Cancelar</v-btn>
                <v-btn color="error" @click="confirmDelete">Excluir</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000" location="top right">
      {{ snackbar.message }}
      <template #actions>
        <v-btn variant="text" @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-layout>
</template>

<script setup lang="ts">
import FormContaCartao from '@/components/FormContaCartao.vue';
import { useRolesStore } from '@/store/roles';
import type { CreditCard, CreditCardInvoice, NewCreditCard } from '@/types/accounts.types';
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

// Stores
const rolesStore = useRolesStore();
const route = useRoute();

// State
const dialog = ref(false);
const deleteDialog = ref(false);
const isEditing = ref(false);
const editableCard = ref<CreditCard | null>(null);
const cardToDelete = ref<CreditCard | null>(null);
const selectedCard = ref<CreditCard | null>(null);
const drawer = ref(false);
const loading = ref(true);
const loadingInvoices = ref(false);

const formContaCartaoRef = ref<InstanceType<typeof FormContaCartao> | null>(null);

const snackbar = ref({
  show: false,
  message: '',
  color: 'success',
});

// Mock Data
const creditCards = ref<CreditCard[]>([]);
const invoices = ref<CreditCardInvoice[]>([]);

// Menu Items (for navigation drawer)
const itensSideBar = ref([
    { name: "Admin", icon: "mdi-shield-crown", route: "admin", adminOnly: true, traderOnly: false },
    { name: "Trader", icon: "mdi-chart-line", route: "trader", adminOnly: false, traderOnly: true },
    { name: "Dashboard", icon: "mdi-view-dashboard", route: "dashboard", adminOnly: false, traderOnly: false },
    { name: "Contas", icon: "mdi-bank", route: "contas", adminOnly: false, traderOnly: false },
    { name: "Receitas", icon: "mdi-cash-plus", route: "receitas", adminOnly: false, traderOnly: false },
    { name: "Despesas", icon: "mdi-cash-minus", route: "despesas", adminOnly: false, traderOnly: false },
    { name: "Categorias", icon: "mdi-tag-multiple", route: "categorias", adminOnly: false, traderOnly: false },
    { name: "Cartões de Crédito", icon: "mdi-credit-card-outline", route: "cartoes", adminOnly: false, traderOnly: false },
    { name: "Notificações", icon: "mdi-bell", route: "notificacoes", adminOnly: false, traderOnly: false },
    { name: "Perfil", icon: "mdi-account", route: "perfil", adminOnly: false, traderOnly: false },
]);

const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) return false;
    const isTrader = rolesStore.myRoles.includes('TRADER') || rolesStore.myRoles.includes('USER_TRADER') || rolesStore.myRoles.includes('FULL');
    if (item.traderOnly && !isTrader) return false;
    return true;
  });
});

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName;
};

// Methods
const openAddCardDialog = () => {
  isEditing.value = false;
  editableCard.value = null;
  dialog.value = true;
};

const openEditCardDialog = (card: CreditCard) => {
  isEditing.value = true;
  editableCard.value = { ...card };
  dialog.value = true;
};

const openDeleteCardDialog = (card: CreditCard) => {
    cardToDelete.value = card;
    deleteDialog.value = true;
}

const closeDialog = () => {
  dialog.value = false;
  editableCard.value = null;
};

const submitForm = async () => {
  if (formContaCartaoRef.value) {
    await formContaCartaoRef.value.submit();
  }
};

const saveCard = async (cardData: NewCreditCard | CreditCard) => {
    showSnackbar(isEditing.value ? 'Cartão editado com sucesso!' : 'Cartão salvo com sucesso!', 'success');
    closeDialog();
};

const confirmDelete = async () => {
    showSnackbar('Cartão excluído com sucesso!', 'success');
    deleteDialog.value = false;
}

const viewInvoices = async (card: CreditCard) => {
  selectedCard.value = card;
  loadingInvoices.value = true;
  setTimeout(() => { // Simula a busca das faturas
    invoices.value = [
      { id: 1, conta_id: card.id, mes: 10, ano: 2025, valor_total: 1580.50, data_vencimento: '2025-10-25', pago: true },
      { id: 2, conta_id: card.id, mes: 9, ano: 2025, valor_total: 1230.00, data_vencimento: '2025-09-25', pago: true },
      { id: 3, conta_id: card.id, mes: 8, ano: 2025, valor_total: 2100.75, data_vencimento: '2025-08-25', pago: true },
    ];
    loadingInvoices.value = false;
  }, 1000);
};

const showSnackbar = (message: string, color: string) => {
  snackbar.value = { show: true, message, color };
};

// Formatting
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
};

// MOCK DATA LOADER
const fetchMockData = () => {
    loading.value = true;
    setTimeout(() => {
        creditCards.value = [
            { id: 1, nome: 'Nubank', limite: 5000, dia_vencimento: 10, dia_fechamento: 3, icon: 'mdi-credit-card-chip' },
            { id: 2, nome: 'Inter', limite: 12000, dia_vencimento: 15, dia_fechamento: 8, icon: 'mdi-credit-card-outline' },
            { id: 3, nome: 'Santander SX', limite: 7500, dia_vencimento: 25, dia_fechamento: 18, icon: 'mdi-credit-card-check' },
        ];
        loading.value = false;
    }, 1500); // Simula atraso da rede
}


// Lifecycle
onMounted(() => {
  fetchMockData();
  if (rolesStore.myRoles.length === 0) {
    // Para o menu funcionar, precisamos de um mock das roles também
    rolesStore.$patch({ myRoles: ['USER', 'FULL'] }); 
  }
});
</script>

<style scoped>
.dashboard-view {
  background-color: #f5f5f5;
  min-height: 100vh;
}

.card-gradient {
  background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
  border-radius: 8px 8px 0 0;
}

.card-gradient-primary {
  --gradient-start: #2196F3;
  --gradient-end: #1976D2;
}
</style>