<template>
  <div class="veiculo-view">
    <!-- Header Section -->
    <div class="view-header mb-4 mb-md-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3 mb-2">
        <div class="d-flex align-center gap-2 gap-md-3 flex-grow-1">
          <v-icon icon="mdi-car" :size="$vuetify.display.xs ? 28 : 36" color="primary" />
          <div>
            <h1 :class="$vuetify.display.xs ? 'text-h6' : 'text-h5'" class="font-weight-bold">Meus Veículos</h1>
            <p class="text-caption text-medium-emphasis mb-0 d-none d-sm-block">Gerencie seus veículos e manutenções</p>
          </div>
        </div>
        <v-btn
          color="primary"
          :size="$vuetify.display.xs ? 'default' : 'large'"
          :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-plus'"
          :icon="$vuetify.display.xs ? 'mdi-plus' : undefined"
          @click="openAddVeiculoDialog"
          class="flex-shrink-0"
        >
          <span class="d-none d-sm-inline">Novo Veículo</span>
        </v-btn>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-4 mb-md-6 kpi-row">
      <!-- Card: Total de Veículos -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Total de Veículos</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold">{{ veiculos.length }}</h3>
            </div>
            <v-icon icon="mdi-car-multiple" :size="$vuetify.display.xs ? 24 : 40" color="info" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Gasto Total em Manutenção -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Gasto Total (Manutenção)</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ formatCurrency(summary.gastoTotal) }}</h3>
            </div>
            <v-icon icon="mdi-cash-multiple" :size="$vuetify.display.xs ? 24 : 40" color="warning" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Manutenção Próxima -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1" :class="{ 'alert-card': summary.proximaManutencao }">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Próxima Manutenção</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ summary.proximaManutencao || 'Não há' }}</h3>
            </div>
            <v-icon icon="mdi-wrench" :size="$vuetify.display.xs ? 24 : 40" color="error" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Veículos Ativos -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Veículos Ativos</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold">{{ summary.veiculosAtivos }}</h3>
            </div>
            <v-icon icon="mdi-check-circle-outline" :size="$vuetify.display.xs ? 24 : 40" color="success" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Abas de Navegação -->
    <v-tabs v-model="tabAtiva" class="mb-4 mb-md-6">
      <v-tab value="veiculos">
        <v-icon icon="mdi-car" class="mr-2" />
        Veículos
      </v-tab>
      <v-tab value="manutencoes">
        <v-icon icon="mdi-wrench" class="mr-2" />
        Manutenções
      </v-tab>
    </v-tabs>

    <!-- Tab: Veículos -->
    <v-card v-if="tabAtiva === 'veiculos'" class="mb-4 mb-md-6" elevation="1">
      <!-- Filtros -->
      <v-card class="filters-card pa-3 pa-md-4" elevation="0" variant="outlined">
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-stretch align-sm-center">
          <v-text-field
            v-model="searchVeiculo"
            label="Buscar veículos..."
            prepend-inner-icon="mdi-magnify"
            clearable
            density="compact"
            class="flex-grow-1 flex-shrink-1"
            style="min-width: 0"
            hide-details
          />
          <v-select
            v-model="statusFilterVeiculo"
            :items="['ativo', 'inativo', 'manutenção']"
            label="Status"
            clearable
            density="compact"
            class="filter-select"
            hide-details
          />
          <v-btn
            variant="outlined"
            @click="clearFiltersVeiculo"
            :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-close-circle-outline'"
            :icon="$vuetify.display.xs ? 'mdi-close-circle-outline' : undefined"
            :size="$vuetify.display.xs ? 'small' : 'default'"
            class="flex-shrink-0"
          >
            <span class="d-none d-sm-inline">Limpar</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Grid de Veículos -->
      <v-row class="pa-3 pa-md-4">
        <v-col
          v-for="veiculo in filteredVeiculos"
          :key="veiculo.id"
          cols="12"
          sm="6"
          lg="4"
        >
          <v-card 
            class="veiculo-card h-100 transition-all hover-elevation cursor-pointer" 
            elevation="1"
            @click="openHistoricoDialog(veiculo)"
            role="button"
            tabindex="0"
            @keydown.enter="openHistoricoDialog(veiculo)"
          >
            <!-- Header do Card -->
            <div class="card-header pa-4 d-flex align-center justify-space-between" :style="{ backgroundColor: veiculo.color }">
              <div class="flex-grow-1">
                <h3 class="text-h6 font-weight-bold text-white mb-0">{{ veiculo.marca }} {{ veiculo.modelo }}</h3>
                <p class="text-caption text-white-50 mb-0">{{ veiculo.placa }}</p>
              </div>
              <v-chip
                :color="getStatusColor(veiculo.status)"
                label
                size="small"
                text-color="white"
                class="ml-2"
              >
                {{ getStatusLabel(veiculo.status) }}
              </v-chip>
            </div>

            <!-- Conteúdo do Card -->
            <v-card-text class="pa-4">
              <!-- Informações Básicas -->
              <div class="mb-4">
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Ano:</span>
                  <span class="text-body-2 font-weight-bold">{{ veiculo.ano }}</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Quilometragem:</span>
                  <span class="text-body-2 font-weight-bold">{{ formatNumber(veiculo.quilometragem) }} km</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Combustível:</span>
                  <span class="text-body-2 font-weight-bold">{{ veiculo.combustivel }}</span>
                </div>
              </div>

              <v-divider class="mb-4" />

              <!-- Última Manutenção -->
              <div class="mb-4">
                <p class="text-caption text-medium-emphasis mb-2">Última Manutenção</p>
                <div class="d-flex align-center gap-2">
                  <v-icon icon="mdi-calendar" size="20" color="primary" />
                  <span class="text-body-2">{{ formatDate(veiculo.ultimaManutencao) }}</span>
                </div>
              </div>

              <!-- Próxima Manutenção -->
              <div class="mb-4">
                <p class="text-caption text-medium-emphasis mb-2">Próxima Manutenção</p>
                <v-progress-linear
                  :value="calculateProxManutencaoProgress(veiculo)"
                  :color="calculateProxManutencaoProgress(veiculo) > 80 ? 'error' : 'success'"
                  class="mb-2"
                />
                <div class="d-flex justify-space-between align-center">
                  <span class="text-caption">{{ veiculo.proximaManutencao }} km</span>
                  <span class="text-caption text-medium-emphasis">{{ calculateProxManutencaoProgress(veiculo) }}%</span>
                </div>
              </div>

              <v-divider class="mb-4" />

              <!-- Ações -->
              <div class="d-flex gap-2">
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  prepend-icon="mdi-wrench"
                  @click.stop="openAddManutencaoDialog(veiculo)"
                  class="flex-grow-1"
                >
                  <span class="text-truncate">Manutenção</span>
                </v-btn>
                <v-btn
                  color="info"
                  variant="outlined"
                  icon="mdi-pencil"
                  size="small"
                  @click.stop="editVeiculo(veiculo)"
                />
                <v-btn
                  color="error"
                  variant="outlined"
                  icon="mdi-delete"
                  size="small"
                  @click.stop="deleteVeiculo(veiculo.id)"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Card Vazio -->
        <v-col v-if="filteredVeiculos.length === 0" cols="12">
          <v-card class="pa-8 text-center" elevation="0" variant="outlined">
            <v-icon icon="mdi-car-off" size="64" color="text-disabled" class="mb-4" />
            <p class="text-body-1 text-medium-emphasis">Nenhum veículo encontrado</p>
            <v-btn
              color="primary"
              @click="openAddVeiculoDialog"
              class="mt-4"
              prepend-icon="mdi-plus"
            >
              Adicionar Veículo
            </v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-card>

    <!-- Tab: Manutenções -->
    <v-card v-if="tabAtiva === 'manutencoes'" class="mb-4 mb-md-6" elevation="1">
      <!-- Filtros -->
      <v-card class="filters-card pa-3 pa-md-4" elevation="0" variant="outlined">
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-stretch align-sm-center">
          <v-text-field
            v-model="searchManutencao"
            label="Buscar manutenções..."
            prepend-inner-icon="mdi-magnify"
            clearable
            density="compact"
            class="flex-grow-1 flex-shrink-1"
            style="min-width: 0"
            hide-details
          />
          <v-select
            v-model="tipoManutencaoFilter"
            :items="tiposManutencao"
            label="Tipo"
            clearable
            density="compact"
            class="filter-select"
            hide-details
          />
          <v-btn
            variant="outlined"
            @click="clearFiltersManutencao"
            :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-close-circle-outline'"
            :icon="$vuetify.display.xs ? 'mdi-close-circle-outline' : undefined"
            :size="$vuetify.display.xs ? 'small' : 'default'"
            class="flex-shrink-0"
          >
            <span class="d-none d-sm-inline">Limpar</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Tabela de Manutenções -->
      <div class="table-wrapper">
        <v-data-table
          :items="filteredManutencoes"
          :headers="headersManutencao"
          :loading="loadingManutencao"
          class="manutencoes-table"
          density="comfortable"
          :mobile-breakpoint="$vuetify.display.xs ? 9999 : 0"
        >
          <template #item.veiculo="{ item }">
            <div class="d-flex align-center gap-2">
              <v-icon icon="mdi-car" color="primary" />
              <div>
                <div class="font-weight-bold">{{ getVeiculo(item.veiculoId).marca }} {{ getVeiculo(item.veiculoId).modelo }}</div>
                <div class="text-caption text-medium-emphasis">{{ getVeiculo(item.veiculoId).placa }}</div>
              </div>
            </div>
          </template>

          <template #item.tipo="{ item }">
            <v-chip
              :color="getTipoManutencaoColor(item.tipo)"
              label
              size="small"
            >
              {{ item.tipo }}
            </v-chip>
          </template>

          <template #item.data="{ item }">
            <div class="text-body-2">{{ formatDate(item.data) }}</div>
          </template>

          <template #item.quilometragem="{ item }">
            <div class="text-body-2 font-weight-bold">{{ formatNumber(item.quilometragem) }} km</div>
          </template>

          <template #item.valor="{ item }">
            <div class="text-right font-weight-bold text-error">{{ formatCurrency(item.valor) }}</div>
          </template>

          <template #item.descricao="{ item }">
            <div class="text-caption">{{ item.descricao || '-' }}</div>
          </template>

          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-btn
                icon="mdi-pencil"
                variant="text"
                size="small"
                @click="editManutencao(item)"
              />
              <v-btn
                icon="mdi-delete"
                variant="text"
                size="small"
                color="error"
                @click="deleteManutencao(item.id)"
              />
            </div>
          </template>
        </v-data-table>
      </div>
    </v-card>

    <!-- Diálogos -->
    <!-- Dialog: Adicionar/Editar Veículo -->
    <v-dialog v-model="dialogVeiculoOpen" max-width="600px" persistent>
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ editingVeiculo ? 'Editar Veículo' : 'Novo Veículo' }}
        </v-card-title>
        <v-card-text class="pa-6">
          <v-form ref="formVeiculo" @submit.prevent="saveVeiculo">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.marca"
                  label="Marca"
                  placeholder="Ex: Toyota"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.modelo"
                  label="Modelo"
                  placeholder="Ex: Corolla"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.placa"
                  label="Placa"
                  placeholder="Ex: ABC1234"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.ano"
                  label="Ano"
                  placeholder="Ex: 2023"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.quilometragem"
                  label="Quilometragem (km)"
                  placeholder="Ex: 10000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formVeiculoData.combustivel"
                  :items="['Gasolina', 'Diesel', 'Etanol', 'Híbrido', 'Elétrico']"
                  label="Combustível"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.proximaManutencao"
                  label="Próxima Manutenção (km)"
                  placeholder="Ex: 40000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formVeiculoData.status"
                  :items="['ativo', 'inativo', 'manutenção']"
                  label="Status"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-color-picker
                  v-model="formVeiculoData.color"
                  hide-inputs
                  show-swatches
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-6">
          <v-btn @click="closeDialogVeiculo">Cancelar</v-btn>
          <v-spacer />
          <v-btn color="primary" @click="saveVeiculo">
            {{ editingVeiculo ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog: Adicionar/Editar Manutenção (Ordem de Serviço) -->
    <v-dialog v-model="dialogManutencaoOpen" max-width="900px" persistent scrollable>
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ editingManutencao ? 'Editar Ordem de Serviço' : 'Nova Ordem de Serviço' }}
        </v-card-title>
        <v-card-text class="pa-6">
          <v-form ref="formManutencao" @submit.prevent="saveManutencao">
            <!-- Seleção de Veículo -->
            <v-select
              v-model="formManutencaoData.veiculoId"
              :items="veiculos"
              item-title="marca"
              item-value="id"
              label="Veículo"
              outlined
              dense
              required
            >
              <template #item="{ props, item }">
                <v-list-item v-bind="props" :title="`${item.raw.marca} ${item.raw.modelo}`" :subtitle="item.raw.placa" />
              </template>
            </v-select>

            <!-- Tipo, Data e Quilometragem -->
            <v-row class="mt-2">
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formManutencaoData.tipo"
                  :items="tiposManutencao"
                  label="Tipo de Manutenção"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.data"
                  label="Data"
                  type="date"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model.number="formManutencaoData.quilometragem"
                  label="Quilometragem (km)"
                  placeholder="Ex: 20000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
            </v-row>

            <!-- Dados da Oficina -->
            <v-divider class="my-4" />
            <h3 class="text-subtitle-1 font-weight-bold mb-3">Dados da Oficina</h3>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.oficina.nome"
                  label="Nome da Oficina"
                  placeholder="Ex: Auto Center Brasil"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.oficina.telefone"
                  label="Telefone"
                  placeholder="(11) 3456-7890"
                  outlined
                  dense
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.oficina.email"
                  label="Email"
                  placeholder="contato@oficina.com"
                  outlined
                  dense
                  type="email"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.oficina.endereco"
                  label="Endereço"
                  placeholder="Rua, número - Cidade"
                  outlined
                  dense
                />
              </v-col>
            </v-row>

            <!-- Itens da Manutenção -->
            <v-divider class="my-4" />
            <div class="d-flex justify-space-between align-center mb-3">
              <h3 class="text-subtitle-1 font-weight-bold">Itens da Manutenção</h3>
              <v-btn
                size="small"
                color="primary"
                prepend-icon="mdi-plus"
                @click="addManutencaoItem"
              >
                Adicionar Item
              </v-btn>
            </div>

            <div class="items-container">
              <div
                v-for="(item, index) in formManutencaoData.itens"
                :key="index"
                class="item-card mb-4 pa-4 border rounded"
              >
                <div class="d-flex justify-space-between align-center mb-3">
                  <span class="text-caption font-weight-bold">Item {{ index + 1 }}</span>
                  <v-btn
                    v-if="formManutencaoData.itens.length > 1"
                    icon="mdi-delete"
                    size="small"
                    variant="text"
                    color="error"
                    @click="removeManutencaoItem(index)"
                  />
                </div>

                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="item.nome"
                      label="Nome do Item"
                      placeholder="Ex: Óleo 5W30"
                      outlined
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="item.descricao"
                      label="Descrição"
                      placeholder="Ex: Óleo de motor sintético"
                      outlined
                      dense
                    />
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" sm="4">
                    <v-text-field
                      v-model.number="item.quantidade"
                      label="Quantidade"
                      type="number"
                      step="0.01"
                      outlined
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field
                      v-model.number="item.valor"
                      label="Valor Unitário (R$)"
                      type="number"
                      step="0.01"
                      outlined
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="12" sm="4">
                    <div class="text-right">
                      <p class="text-caption text-medium-emphasis mb-1">Subtotal</p>
                      <p class="text-body-2 font-weight-bold">{{ formatCurrency(item.quantidade * item.valor) }}</p>
                    </div>
                  </v-col>
                </v-row>
              </div>
            </div>

            <!-- Total -->
            <v-card class="bg-primary-light mt-4 pa-4">
              <div class="d-flex justify-space-between align-center">
                <span class="text-subtitle-1 font-weight-bold">Valor Total da OS:</span>
                <span class="text-h6 font-weight-bold text-primary">{{ formatCurrency(getTotalManutencao()) }}</span>
              </div>
            </v-card>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-6">
          <v-btn @click="closeDialogManutencao">Cancelar</v-btn>
          <v-spacer />
          <v-btn color="primary" @click="saveManutencao">
            {{ editingManutencao ? 'Atualizar' : 'Registrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog: Histórico do Veículo -->
    <v-dialog v-model="dialogHistoricoOpen" max-width="800px">
      <v-card v-if="veiculoSelecionado">
        <!-- Header -->
        <div class="card-header pa-6 d-flex align-center justify-space-between" :style="{ backgroundColor: veiculoSelecionado.color }">
          <div class="flex-grow-1">
            <h2 class="text-h5 font-weight-bold text-white mb-1">{{ veiculoSelecionado.marca }} {{ veiculoSelecionado.modelo }}</h2>
            <p class="text-body-2 text-white-50 mb-0">{{ veiculoSelecionado.placa }} • {{ veiculoSelecionado.ano }}</p>
          </div>
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="closeHistoricoDialog"
            class="text-white"
          />
        </div>

        <!-- Informações do Veículo -->
        <v-card-text class="pa-6">
          <div class="mb-6">
            <h3 class="text-subtitle-1 font-weight-bold mb-4">Informações do Veículo</h3>
            <v-row>
              <v-col cols="12" sm="6">
                <div class="info-item mb-3">
                  <p class="text-caption text-medium-emphasis mb-1">Quilometragem Atual</p>
                  <p class="text-body-1 font-weight-bold">{{ formatNumber(veiculoSelecionado.quilometragem) }} km</p>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="info-item mb-3">
                  <p class="text-caption text-medium-emphasis mb-1">Combustível</p>
                  <p class="text-body-1 font-weight-bold">{{ veiculoSelecionado.combustivel }}</p>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="info-item mb-3">
                  <p class="text-caption text-medium-emphasis mb-1">Última Manutenção</p>
                  <p class="text-body-1 font-weight-bold">{{ formatDate(veiculoSelecionado.ultimaManutencao) }}</p>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="info-item mb-3">
                  <p class="text-caption text-medium-emphasis mb-1">Próxima Manutenção</p>
                  <p class="text-body-1 font-weight-bold">{{ veiculoSelecionado.proximaManutencao }} km</p>
                </div>
              </v-col>
            </v-row>
          </div>

          <v-divider class="mb-6" />

          <!-- Histórico de Manutenções -->
          <div>
            <div class="d-flex align-center justify-space-between mb-4">
              <h3 class="text-subtitle-1 font-weight-bold">Histórico de Manutenções</h3>
              <v-chip
                :color="getManutencaoCount(veiculoSelecionado.id) > 0 ? 'primary' : 'secondary'"
                label
                size="small"
              >
                {{ getManutencaoCount(veiculoSelecionado.id) }} registros
              </v-chip>
            </div>

            <div v-if="getManutencaoVeiculo(veiculoSelecionado.id).length > 0" class="manutencao-list">
              <div
                v-for="manutencao in getManutencaoVeiculo(veiculoSelecionado.id)"
                :key="manutencao.id"
                class="manutencao-item pa-4 mb-3 cursor-pointer"
                elevation="0"
                @click="openOSDetailsDialog(manutencao)"
                role="button"
                tabindex="0"
                @keydown.enter="openOSDetailsDialog(manutencao)"
              >
                <div class="d-flex align-center justify-space-between mb-2">
                  <div class="d-flex align-center gap-2">
                    <v-icon 
                      :icon="getTipoIcon(manutencao.tipo)" 
                      :color="getTipoManutencaoColor(manutencao.tipo)"
                    />
                    <div>
                      <p class="text-body-2 font-weight-bold mb-0">{{ manutencao.tipo }}</p>
                      <p class="text-caption text-medium-emphasis mb-0">{{ formatDate(manutencao.data) }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-body-2 font-weight-bold text-error mb-0">{{ formatCurrency(calcularTotalOS(manutencao)) }}</p>
                    <p class="text-caption text-medium-emphasis mb-0">{{ formatNumber(manutencao.quilometragem) }} km</p>
                  </div>
                </div>
                <div class="d-flex align-center gap-2 mt-2">
                  <v-chip size="small" variant="outlined" v-if="manutencao.oficina">{{ manutencao.oficina.nome }}</v-chip>
                  <v-chip size="small" color="primary" text-color="white">{{ manutencao.itens ? manutencao.itens.length : 0 }} itens</v-chip>
                  <v-icon icon="mdi-chevron-right" size="20" class="ml-auto" />
                </div>
              </div>
            </div>

            <div v-else class="text-center pa-6">
              <v-icon icon="mdi-history" size="48" color="text-disabled" class="mb-2" />
              <p class="text-body-2 text-medium-emphasis">Nenhuma manutenção registrada</p>
            </div>
          </div>
        </v-card-text>

        <!-- Ações -->
        <v-card-actions class="pa-6">
          <v-spacer />
          <v-btn
            color="primary"
            prepend-icon="mdi-wrench"
            @click="openAddManutencaoForHistorico"
          >
            Registrar Manutenção
          </v-btn>
          <v-btn
            variant="outlined"
            @click="closeHistoricoDialog"
          >
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog: Detalhes da Ordem de Serviço -->
    <v-dialog v-model="dialogOSDetailsOpen" max-width="800px" scrollable>
      <v-card v-if="osDetalhes">
        <!-- Header -->
        <div class="bg-primary text-white pa-6">
          <div class="d-flex align-center justify-space-between mb-3">
            <h2 class="text-h5 font-weight-bold">Ordem de Serviço #{{ osDetalhes.id }}</h2>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="closeOSDetailsDialog"
              class="text-white"
            />
          </div>
          <p class="text-body-2 text-white-70 mb-0">{{ osDetalhes.tipo }} • {{ formatDate(osDetalhes.data) }}</p>
        </div>

        <v-card-text class="pa-6">
          <!-- Informações do Veículo -->
          <div class="mb-6">
            <h3 class="text-subtitle-1 font-weight-bold mb-3">Informações do Veículo</h3>
            <v-row>
              <v-col cols="12" sm="6">
                <p class="text-caption text-medium-emphasis mb-1">Veículo</p>
                <p class="text-body-2 font-weight-bold">{{ getVeiculo(osDetalhes.veiculoId).marca }} {{ getVeiculo(osDetalhes.veiculoId).modelo }}</p>
              </v-col>
              <v-col cols="12" sm="6">
                <p class="text-caption text-medium-emphasis mb-1">Placa</p>
                <p class="text-body-2 font-weight-bold">{{ getVeiculo(osDetalhes.veiculoId).placa }}</p>
              </v-col>
              <v-col cols="12" sm="6">
                <p class="text-caption text-medium-emphasis mb-1">Quilometragem</p>
                <p class="text-body-2 font-weight-bold">{{ formatNumber(osDetalhes.quilometragem) }} km</p>
              </v-col>
              <v-col cols="12" sm="6">
                <p class="text-caption text-medium-emphasis mb-1">Data do Serviço</p>
                <p class="text-body-2 font-weight-bold">{{ formatDate(osDetalhes.data) }}</p>
              </v-col>
            </v-row>
          </div>

          <v-divider class="mb-6" />

          <!-- Dados da Oficina -->
          <div class="mb-6">
            <h3 class="text-subtitle-1 font-weight-bold mb-3">Oficina</h3>
            <v-card elevation="0" variant="outlined" class="pa-4">
              <v-row>
                <v-col cols="12" sm="6">
                  <p class="text-caption text-medium-emphasis mb-1">Nome</p>
                  <p class="text-body-2 font-weight-bold">{{ osDetalhes.oficina.nome }}</p>
                </v-col>
                <v-col cols="12" sm="6">
                  <p class="text-caption text-medium-emphasis mb-1">Telefone</p>
                  <p class="text-body-2 font-weight-bold">
                    <v-icon icon="mdi-phone" size="16" class="mr-1" />
                    {{ osDetalhes.oficina.telefone || 'Não informado' }}
                  </p>
                </v-col>
                <v-col cols="12" sm="6">
                  <p class="text-caption text-medium-emphasis mb-1">Email</p>
                  <p class="text-body-2 font-weight-bold">
                    <v-icon icon="mdi-email" size="16" class="mr-1" />
                    {{ osDetalhes.oficina.email || 'Não informado' }}
                  </p>
                </v-col>
                <v-col cols="12" sm="6">
                  <p class="text-caption text-medium-emphasis mb-1">Endereço</p>
                  <p class="text-body-2 font-weight-bold">
                    <v-icon icon="mdi-map-marker" size="16" class="mr-1" />
                    {{ osDetalhes.oficina.endereco || 'Não informado' }}
                  </p>
                </v-col>
              </v-row>
            </v-card>
          </div>

          <v-divider class="mb-6" />

          <!-- Itens da Manutenção -->
          <div class="mb-6">
            <h3 class="text-subtitle-1 font-weight-bold mb-3">Itens</h3>
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead>
                  <tr style="border-bottom: 2px solid #e0e0e0;">
                    <th style="text-align: left; padding: 12px; font-weight: bold; font-size: 0.875rem;">Item</th>
                    <th style="text-align: center; padding: 12px; font-weight: bold; font-size: 0.875rem;">Qtd</th>
                    <th style="text-align: right; padding: 12px; font-weight: bold; font-size: 0.875rem;">Valor Unit.</th>
                    <th style="text-align: right; padding: 12px; font-weight: bold; font-size: 0.875rem;">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in osDetalhes.itens" :key="index" style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; vertical-align: top;">
                      <p class="text-body-2 font-weight-bold mb-1">{{ item.nome }}</p>
                      <p class="text-caption text-medium-emphasis mb-0">{{ item.descricao }}</p>
                    </td>
                    <td style="text-align: center; padding: 12px;">{{ item.quantidade }}</td>
                    <td style="text-align: right; padding: 12px;">{{ formatCurrency(item.valor) }}</td>
                    <td style="text-align: right; padding: 12px; font-weight: bold;">{{ formatCurrency(item.quantidade * item.valor) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Total -->
          <v-card class="bg-primary text-white pa-4">
            <div class="d-flex justify-space-between align-center">
              <span class="text-subtitle-1 font-weight-bold">Valor Total:</span>
              <span class="text-h6 font-weight-bold">{{ formatCurrency(calcularTotalOS(osDetalhes)) }}</span>
            </div>
          </v-card>
        </v-card-text>

        <v-card-actions class="pa-6">
          <v-btn color="warning" prepend-icon="mdi-pencil" @click="editarOSDetalhes">Editar</v-btn>
          <v-spacer />
          <v-btn variant="outlined" @click="closeOSDetailsDialog">Fechar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar de Notificações -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { manutencaoService, veiculoService } from '@/services/veiculoService'
import { computed, onMounted, ref } from 'vue'

// Estados de Notificação
const snackbar = ref(false)
const snackbarMessage = ref('')
const snackbarColor = ref('success')

function showToast(message: string, color: string = 'success') {
  snackbarMessage.value = message
  snackbarColor.value = color
  snackbar.value = true
}
const tabAtiva = ref('veiculos')
const loading = ref(false)
const loadingManutencao = ref(false)
const loadingData = ref(false)
const searchVeiculo = ref('')
const searchManutencao = ref('')
const statusFilterVeiculo = ref('')
const tipoManutencaoFilter = ref('')
const dialogVeiculoOpen = ref(false)
const dialogManutencaoOpen = ref(false)
const dialogHistoricoOpen = ref(false)
const dialogOSDetailsOpen = ref(false)
const editingVeiculo = ref(null)
const editingManutencao = ref(null)
const veiculoSelecionado = ref(null)
const osDetalhes = ref(null)

// Tipos
interface Veiculo {
  id?: number
  marca: string
  modelo: string
  placa: string
  ano: number
  quilometragem: number
  combustivel: string
  proximaManutencao: number
  status: string
  color?: string
  ultimaManutencao?: string
  manutencoes?: Manutencao[]
}

interface ManutencaoItem {
  id?: number | string
  nome: string
  descricao?: string
  quantidade: number
  valor: number
}

interface Manutencao {
  id?: number
  veiculo_id?: number
  veiculoId?: number
  tipo: string
  data: string
  quilometragem: number
  valor_total?: number
  oficina_nome?: string
  oficina_telefone?: string
  oficina_email?: string
  oficina_endereco?: string
  oficina?: {
    nome: string
    telefone?: string
    email?: string
    endereco?: string
  }
  observacoes?: string
  itens?: ManutencaoItem[]
}

// Dados - carregados da API
const veiculos = ref<Veiculo[]>([])
const manutencoes = ref<Manutencao[]>([])

// Inicializar dados ao montar o componente
onMounted(async () => {
  await loadData()
})

// Funções para carregar dados da API
async function loadData() {
  loadingData.value = true
  try {
    await Promise.all([loadVeiculos(), loadManutencoes()])
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
    showToast('Erro ao carregar dados. Por favor, tente novamente.', 'error', 'error')
  } finally {
    loadingData.value = false
  }
}

async function loadVeiculos() {
  try {
    const response = await veiculoService.getVeiculos()
    const data = Array.isArray(response.data) ? response.data : response.data?.data || []
    veiculos.value = data.map((v: any) => ({
      ...v,
      color: getRandomColor(),
    }))
  } catch (error) {
    console.error('Erro ao carregar veículos:', error)
    throw error
  }
}

async function loadManutencoes() {
  try {
    const response = await manutencaoService.getManutencoes()
    const data = Array.isArray(response.data) ? response.data : response.data?.data || []
    manutencoes.value = data
  } catch (error) {
    console.error('Erro ao carregar manutenções:', error)
    throw error
  }
}

function getRandomColor() {
  const colors = ['#163dc0', '#e63946', '#f77f00', '#2a9d8f', '#e76f51', '#d62828']
  return colors[Math.floor(Math.random() * colors.length)]
}
const tiposManutencao = [
  'Troca de Óleo',
  'Revisão',
  'Pneu',
  'Freios',
  'Filtros',
  'Bateria',
  'Velas',
  'Corrente',
  'Suspensão',
  'Ar Condicionado',
  'Limpeza de Injetor',
  'Alinhamento',
]

// Form Data
const formVeiculoData = ref({
  marca: '',
  modelo: '',
  placa: '',
  ano: new Date().getFullYear(),
  quilometragem: 0,
  combustivel: 'Gasolina',
  proximaManutencao: 40000,
  status: 'ativo',
  color: '#163dc0',
})

const formManutencaoData = ref({
  veiculoId: null,
  tipo: '',
  data: new Date().toISOString().split('T')[0],
  quilometragem: 0,
  oficina: {
    nome: '',
    telefone: '',
    email: '',
    endereco: ''
  },
  itens: [
    { id: 1, nome: '', descricao: '', quantidade: 1, valor: 0 }
  ]
})

// Headers da Tabela
const headersManutencao = [
  { title: 'Veículo', key: 'veiculo', sortable: true },
  { title: 'Tipo', key: 'tipo', sortable: true },
  { title: 'Data', key: 'data', sortable: true },
  { title: 'Quilometragem', key: 'quilometragem', sortable: true },
  { title: 'Valor', key: 'valor', sortable: true },
  { title: 'Descrição', key: 'descricao', sortable: false },
  { title: 'Ações', key: 'actions', sortable: false },
]

// Computed Properties
const filteredVeiculos = computed(() => {
  return veiculos.value.filter((v: Veiculo) => {
    const matchSearch = searchVeiculo.value === '' || 
      `${v.marca} ${v.modelo} ${v.placa}`.toLowerCase().includes(searchVeiculo.value.toLowerCase())
    const matchStatus = statusFilterVeiculo.value === '' || v.status === statusFilterVeiculo.value
    return matchSearch && matchStatus
  })
})

const filteredManutencoes = computed(() => {
  return manutencoes.value.filter((m: Manutencao) => {
    const veiculo = getVeiculo(m.veiculo_id || m.veiculoId || 0)
    const matchSearch = searchManutencao.value === '' || 
      `${veiculo.marca} ${veiculo.modelo} ${m.tipo}`.toLowerCase().includes(searchManutencao.value.toLowerCase())
    const matchTipo = tipoManutencaoFilter.value === '' || m.tipo === tipoManutencaoFilter.value
    return matchSearch && matchTipo
  })
})

const summary = computed(() => {
  const gastoTotal = manutencoes.value.reduce((sum: number, m: Manutencao) => sum + (m.valor_total || 0), 0)
  const veiculosAtivos = veiculos.value.filter((v: Veiculo) => v.status === 'ativo').length
  
  let proximaManutencao: string | null = null
  veiculos.value.forEach((v: Veiculo) => {
    const progress = calculateProxManutencaoProgress(v)
    if (progress > 80 && !proximaManutencao) {
      proximaManutencao = `${v.marca} ${v.modelo}`
    }
  })

  return {
    gastoTotal,
    veiculosAtivos,
    proximaManutencao: proximaManutencao || 'Tudo em dia',
  }
})

// Funções
function calcularTotalOS(os: Manutencao) {
  return os.itens?.reduce((sum: number, item: ManutencaoItem) => sum + (item.quantidade * item.valor), 0) || 0
}

function getTotalManutencao() {
  return formManutencaoData.value.itens.reduce((sum: number, item: ManutencaoItem) => sum + (item.quantidade * item.valor), 0)
}

function addManutencaoItem() {
  const maxId = Math.max(...formManutencaoData.value.itens.map((i: ManutencaoItem) => (typeof i.id === 'number' ? i.id : 0)), 0)
  formManutencaoData.value.itens.push({
    id: maxId + 1,
    nome: '',
    descricao: '',
    quantidade: 1,
    valor: 0
  })
}

function removeManutencaoItem(index: number) {
  formManutencaoData.value.itens.splice(index, 1)
}

function openOSDetailsDialog(manutencao: Manutencao) {
  osDetalhes.value = JSON.parse(JSON.stringify(manutencao))
  dialogOSDetailsOpen.value = true
}

function closeOSDetailsDialog() {
  dialogOSDetailsOpen.value = false
  osDetalhes.value = null
}

function editarOSDetalhes() {
  if (osDetalhes.value) {
    editingManutencao.value = osDetalhes.value.id
    formManutencaoData.value = JSON.parse(JSON.stringify(osDetalhes.value))
    closeOSDetailsDialog()
    dialogManutencaoOpen.value = true
  }
}

function formatCurrency(value: number | undefined) {
  if (!value && value !== 0) return 'R$ 0,00'
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

function formatDate(date: string | undefined) {
  if (!date) return '-'
  return new Intl.DateTimeFormat('pt-BR').format(new Date(date))
}

function formatNumber(value: number | undefined) {
  if (!value && value !== 0) return '0'
  return new Intl.NumberFormat('pt-BR').format(value)
}

function getStatusColor(status: string) {
  const colors: Record<string, string> = {
    ativo: 'success',
    inativo: 'secondary',
    'manutenção': 'error',
  }
  return colors[status] || 'default'
}

function getStatusLabel(status: string) {
  const labels: Record<string, string> = {
    ativo: 'Ativo',
    inativo: 'Inativo',
    'manutenção': 'Em Manutenção',
  }
  return labels[status] || status
}

function getTipoManutencaoColor(tipo: string) {
  const colors: Record<string, string> = {
    'Troca de Óleo': 'info',
    'Revisão': 'primary',
    'Pneu': 'warning',
    'Freios': 'error',
    'Filtros': 'secondary',
    'Bateria': 'orange',
    'Velas': 'cyan',
  }
  return colors[tipo] || 'default'
}

function calculateProxManutencaoProgress(veiculo: Veiculo) {
  const proximaManutencao = veiculo.proximaManutencao
  const quilometragem = veiculo.quilometragem
  const baseKm = proximaManutencao - 10000
  
  const progress = Math.round(((quilometragem - baseKm) / 10000) * 100)
  return Math.max(0, Math.min(100, progress))
}

function getVeiculo(id: number | undefined): Veiculo {
  return veiculos.value.find((v: Veiculo) => v.id === id) || { marca: '', modelo: '', placa: '', ano: 0, quilometragem: 0, combustivel: 'Gasolina', proximaManutencao: 0, status: '' }
}

function openAddVeiculoDialog() {
  editingVeiculo.value = null
  formVeiculoData.value = {
    marca: '',
    modelo: '',
    placa: '',
    ano: new Date().getFullYear(),
    quilometragem: 0,
    combustivel: 'Gasolina',
    proximaManutencao: 40000,
    status: 'ativo',
    color: '#163dc0',
  }
  dialogVeiculoOpen.value = true
}

function closeDialogVeiculo() {
  dialogVeiculoOpen.value = false
  editingVeiculo.value = null
}

function editVeiculo(veiculo: Veiculo) {
  editingVeiculo.value = veiculo.id || null
  formVeiculoData.value = { ...veiculo }
  dialogVeiculoOpen.value = true
}

function saveVeiculo() {
  loading.value = true
  try {
    if (editingVeiculo.value) {
      // Atualizar veículo existente
      veiculoService.updateVeiculo(editingVeiculo.value, {
        marca: formVeiculoData.value.marca,
        modelo: formVeiculoData.value.modelo,
        placa: formVeiculoData.value.placa,
        ano: formVeiculoData.value.ano,
        quilometragem: formVeiculoData.value.quilometragem,
        combustivel: formVeiculoData.value.combustivel,
        proximaManutencao: formVeiculoData.value.proximaManutencao,
        status: formVeiculoData.value.status,
      }).then(() => {
        showToast('Veículo atualizado com sucesso!', 'success')
        loadVeiculos()
        closeDialogVeiculo()
      }).catch((error) => {
        console.error('Erro ao atualizar veículo:', error)
        showToast('Erro ao atualizar veículo. Por favor, tente novamente.', 'error')
      }).finally(() => {
        loading.value = false
      })
    } else {
      // Criar novo veículo
      veiculoService.createVeiculo({
        marca: formVeiculoData.value.marca,
        modelo: formVeiculoData.value.modelo,
        placa: formVeiculoData.value.placa,
        ano: formVeiculoData.value.ano,
        quilometragem: formVeiculoData.value.quilometragem,
        combustivel: formVeiculoData.value.combustivel,
        proximaManutencao: formVeiculoData.value.proximaManutencao,
        status: formVeiculoData.value.status,
      }).then(() => {
        showToast('Veículo criado com sucesso!', 'success')
        loadVeiculos()
        closeDialogVeiculo()
      }).catch((error) => {
        console.error('Erro ao criar veículo:', error)
        showToast('Erro ao criar veículo. Por favor, tente novamente.', 'error')
      }).finally(() => {
        loading.value = false
      })
    }
  } catch (error) {
    console.error('Erro:', error)
    loading.value = false
  }
}

function deleteVeiculo(id: number) {
  if (confirm('Tem certeza que deseja deletar este veículo?')) {
    loading.value = true
    veiculoService.deleteVeiculo(id)
      .then(() => {
        showToast('Veículo deletado com sucesso!', 'success')
        loadVeiculos()
      })
      .catch((error) => {
        console.error('Erro ao deletar veículo:', error)
        showToast('Erro ao deletar veículo. Por favor, tente novamente.', 'error')
      })
      .finally(() => {
        loading.value = false
      })
  }
}

function resetFormManutencao(veiculoId: number | null | undefined = null) {
  return {
    veiculoId: veiculoId || null,
    tipo: '',
    data: new Date().toISOString().split('T')[0],
    quilometragem: 0,
    oficina: {
      nome: '',
      telefone: '',
      email: '',
      endereco: ''
    },
    itens: [
      { id: 1, nome: '', descricao: '', quantidade: 1, valor: 0 }
    ]
  }
}

function openAddManutencaoDialog(veiculo: Veiculo | null = null) {
  editingManutencao.value = null
  formManutencaoData.value = resetFormManutencao(veiculo?.id)
  dialogManutencaoOpen.value = true
}

function closeDialogManutencao() {
  dialogManutencaoOpen.value = false
  editingManutencao.value = null
}

function editManutencao(manutencao: Manutencao) {
  editingManutencao.value = manutencao.id || null
  formManutencaoData.value = { ...manutencao }
  dialogManutencaoOpen.value = true
}

function saveManutencao() {
  loadingManutencao.value = true
  try {
    // Preparar dados dos itens
    const itens = formManutencaoData.value.itens.map((item: any) => ({
      nome: item.nome,
      descricao: item.descricao || '',
      quantidade: item.quantidade,
      valor_unitario: item.valor,
    }))

    if (editingManutencao.value) {
      // Atualizar manutenção
      manutencaoService.updateManutencao(editingManutencao.value, {
        veiculo_id: formManutencaoData.value.veiculoId,
        tipo: formManutencaoData.value.tipo,
        data: formManutencaoData.value.data,
        quilometragem: formManutencaoData.value.quilometragem,
        oficina_nome: formManutencaoData.value.oficina.nome,
        oficina_telefone: formManutencaoData.value.oficina.telefone,
        oficina_email: formManutencaoData.value.oficina.email,
        oficina_endereco: formManutencaoData.value.oficina.endereco,
        itens: itens,
      }).then(() => {
        showToast('Manutenção atualizada com sucesso!', 'success')
        loadManutencoes()
        closeDialogManutencao()
      }).catch((error) => {
        console.error('Erro ao atualizar manutenção:', error)
        showToast('Erro ao atualizar manutenção. Por favor, tente novamente.', 'error')
      }).finally(() => {
        loadingManutencao.value = false
      })
    } else {
      // Criar nova manutenção
      manutencaoService.createManutencao({
        veiculo_id: formManutencaoData.value.veiculoId,
        tipo: formManutencaoData.value.tipo,
        data: formManutencaoData.value.data,
        quilometragem: formManutencaoData.value.quilometragem,
        oficina_nome: formManutencaoData.value.oficina.nome,
        oficina_telefone: formManutencaoData.value.oficina.telefone,
        oficina_email: formManutencaoData.value.oficina.email,
        oficina_endereco: formManutencaoData.value.oficina.endereco,
        itens: itens,
      }).then(() => {
        showToast('Manutenção registrada com sucesso!', 'success')
        loadManutencoes()
        closeDialogManutencao()
      }).catch((error) => {
        console.error('Erro ao criar manutenção:', error)
        showToast('Erro ao registrar manutenção. Por favor, tente novamente.', 'error')
      }).finally(() => {
        loadingManutencao.value = false
      })
    }
  } catch (error) {
    console.error('Erro:', error)
    loadingManutencao.value = false
  }
}

function deleteManutencao(id: number) {
  if (confirm('Tem certeza que deseja deletar este registro de manutenção?')) {
    loadingManutencao.value = true
    manutencaoService.deleteManutencao(id)
      .then(() => {
        showToast('Manutenção deletada com sucesso!', 'success')
        loadManutencoes()
      })
      .catch((error) => {
        console.error('Erro ao deletar manutenção:', error)
        showToast('Erro ao deletar manutenção. Por favor, tente novamente.', 'error')
      })
      .finally(() => {
        loadingManutencao.value = false
      })
  }
}

function clearFiltersVeiculo() {
  searchVeiculo.value = ''
  statusFilterVeiculo.value = ''
}

function clearFiltersManutencao() {
  searchManutencao.value = ''
  tipoManutencaoFilter.value = ''
}

// Funções do Histórico
function openHistoricoDialog(veiculo: Veiculo) {
  veiculoSelecionado.value = veiculo
  dialogHistoricoOpen.value = true
}

function closeHistoricoDialog() {
  dialogHistoricoOpen.value = false
  veiculoSelecionado.value = null
}

function getManutencaoVeiculo(veiculoId: number | undefined) {
  return manutencoes.value
    .filter((m: Manutencao) => (m.veiculo_id || m.veiculoId) === veiculoId)
    .sort((a: Manutencao, b: Manutencao) => new Date(b.data).getTime() - new Date(a.data).getTime())
}

function getManutencaoCount(veiculoId: number | undefined) {
  return manutencoes.value.filter((m: Manutencao) => (m.veiculo_id || m.veiculoId) === veiculoId).length
}

function getTipoIcon(tipo: string) {
  const icons: Record<string, string> = {
    'Troca de Óleo': 'mdi-oil',
    'Revisão': 'mdi-wrench',
    'Pneu': 'mdi-tire',
    'Freios': 'mdi-car-brake-abs',
    'Filtros': 'mdi-air-filter',
    'Bateria': 'mdi-car-battery',
    'Velas': 'mdi-lightning-bolt',
    'Corrente': 'mdi-link-variant',
    'Suspensão': 'mdi-car-suspension',
    'Ar Condicionado': 'mdi-air-conditioner',
    'Limpeza de Injetor': 'mdi-water-percent',
    'Alinhamento': 'mdi-roughly-equal',
  }
  return icons[tipo] || 'mdi-wrench'
}

function openAddManutencaoForHistorico() {
  editingManutencao.value = null
  if (veiculoSelecionado.value) {
    formManutencaoData.value = resetFormManutencao(veiculoSelecionado.value.id)
  }
  closeHistoricoDialog()
  dialogManutencaoOpen.value = true
}
</script>

<style scoped lang="scss">
.veiculo-view {
  .view-header {
    @media (max-width: 600px) {
      h1 {
        font-size: 1.25rem !important;
      }
    }
  }

  .kpi-row {
    @media (max-width: 600px) {
      .kpi-card {
        h3 {
          font-size: 1rem !important;
        }
      }
    }
  }

  .veiculo-card {
    overflow: hidden;
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.12);

    &:hover {
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15) !important;
      transform: translateY(-4px);
    }

    .card-header {
      position: relative;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.2) 100%),
        var(--color);
    }

    .text-white-50 {
      color: rgba(255, 255, 255, 0.7);
    }
  }

  .alert-card {
    background-color: rgba(244, 67, 54, 0.05);
  }

  .table-wrapper {
    overflow-x: auto;
  }

  .filters-card {
    background: rgb(var(--v-theme-surface));
    margin-bottom: 1.5rem;

    @media (max-width: 600px) {
      margin-bottom: 1rem;

      .filter-select {
        min-width: 140px;
      }
    }
  }

  .hover-elevation {
    transition: all 0.3s ease;
  }

  .cursor-pointer {
    cursor: pointer;
  }

  .manutencao-item {
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 4px;
    transition: all 0.2s ease;

    &:hover {
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
  }

  .info-item {
    padding: 0.75rem;
    background: rgb(var(--v-theme-surface));
    border-radius: 4px;
  }

  .text-white-50 {
    color: rgba(255, 255, 255, 0.7);
  }
}
</style>
