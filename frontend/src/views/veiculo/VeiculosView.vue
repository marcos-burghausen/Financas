<template>
  <div class="px-4 py-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Veículos</h1>
      <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        @click="openCadastroDialog"
      >
        Novo Veículo
      </v-btn>
    </div>

    <!-- Grid de Veículos -->
    <div v-if="veiculos.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <v-card
        v-for="veiculo in veiculos"
        :key="veiculo.id"
        class="cursor-pointer hover:shadow-lg transition-shadow"
        @click="selecionarVeiculo(veiculo)"
      >
        <v-card-item>
          <div class="flex justify-between items-start">
            <div>
              <v-card-title class="text-xl font-bold">
                {{ veiculo.placa }}
              </v-card-title>
              <v-card-subtitle>
                {{ veiculo.marca }} {{ veiculo.modelo }} ({{ veiculo.ano }})
              </v-card-subtitle>
            </div>
            <v-chip
              :color="veiculo.ativo ? 'success' : 'error'"
              text-color="white"
              size="small"
            >
              {{ veiculo.ativo ? 'Ativo' : 'Inativo' }}
            </v-chip>
          </div>
        </v-card-item>

        <v-card-text>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Cor:</span>
              <span class="font-semibold">{{ veiculo.cor }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Quilometragem:</span>
              <span class="font-semibold">{{ formatarQuilometragem(veiculo.quilometragem) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Combustível:</span>
              <span class="font-semibold">{{ veiculo.combustivel }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Última manutenção:</span>
              <span class="font-semibold">{{ formatarData(veiculo.ultimaManutencao) }}</span>
            </div>
          </div>
        </v-card-text>

        <v-card-actions class="pt-0">
          <v-spacer></v-spacer>
          <v-btn
            size="small"
            variant="text"
            color="primary"
            @click.stop="editarVeiculo(veiculo)"
          >
            Editar
          </v-btn>
          <v-btn
            size="small"
            variant="text"
            color="error"
            @click.stop="deletarVeiculo(veiculo.id)"
          >
            Deletar
          </v-btn>
        </v-card-actions>
      </v-card>
    </div>

    <!-- Sem dados -->
    <div v-else class="text-center py-12">
      <v-icon size="64" class="text-gray-400 mb-4">mdi-car-off</v-icon>
      <p class="text-gray-500 text-lg">Nenhum veículo cadastrado</p>
      <v-btn
        color="primary"
        class="mt-4"
        @click="openCadastroDialog"
      >
        Cadastrar primeiro veículo
      </v-btn>
    </div>

    <!-- Dialog Histórico -->
    <v-dialog v-model="mostrarHistorico" max-width="900px">
      <v-card v-if="veiculoSelecionado">
        <v-card-title class="bg-primary text-white">
          <div class="flex justify-between items-center">
            <span>Histórico de Manutenções - {{ veiculoSelecionado.placa }}</span>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="mostrarHistorico = false"
            ></v-btn>
          </div>
        </v-card-title>

        <v-card-text class="py-6">
          <!-- Informações do veículo -->
          <div class="bg-light p-4 rounded-lg mb-6">
            <h3 class="font-bold text-lg mb-3">Informações do Veículo</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Marca:</span>
                <p class="font-semibold">{{ veiculoSelecionado.marca }}</p>
              </div>
              <div>
                <span class="text-gray-600">Modelo:</span>
                <p class="font-semibold">{{ veiculoSelecionado.modelo }}</p>
              </div>
              <div>
                <span class="text-gray-600">Ano:</span>
                <p class="font-semibold">{{ veiculoSelecionado.ano }}</p>
              </div>
              <div>
                <span class="text-gray-600">Quilometragem:</span>
                <p class="font-semibold">{{ formatarQuilometragem(veiculoSelecionado.quilometragem) }}</p>
              </div>
            </div>
          </div>

          <!-- Manutenções -->
          <div>
            <div class="flex justify-between items-center mb-4">
              <h3 class="font-bold text-lg">Manutenções</h3>
              <v-btn
                size="small"
                color="primary"
                prepend-icon="mdi-plus"
                @click="openNovaManutencaoDialog"
              >
                Nova Manutenção
              </v-btn>
            </div>

            <div v-if="veiculoSelecionado.manutencoes.length > 0" class="space-y-4">
              <v-card
                v-for="manutencao in veiculoSelecionado.manutencoes"
                :key="manutencao.id"
                class="border-l-4 border-l-primary"
              >
                <v-card-item>
                  <div class="flex justify-between items-start">
                    <div>
                      <v-card-title class="text-base">
                        {{ manutencao.tipo }}
                      </v-card-title>
                      <v-card-subtitle>
                        {{ formatarData(manutencao.data) }} • {{ formatarQuilometragem(manutencao.quilometragem) }}
                      </v-card-subtitle>
                    </div>
                    <span class="font-bold text-lg text-primary">
                      R$ {{ formatarValor(manutencao.valor) }}
                    </span>
                  </div>
                </v-card-item>

                <v-card-text>
                  <p v-if="manutencao.descricao" class="text-gray-700 mb-3">
                    {{ manutencao.descricao }}
                  </p>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div>
                      <span class="text-gray-600">Oficina:</span>
                      <p class="font-semibold">{{ manutencao.oficina }}</p>
                    </div>
                    <div>
                      <span class="text-gray-600">Telefone:</span>
                      <p class="font-semibold">{{ manutencao.telefone }}</p>
                    </div>
                    <div>
                      <span class="text-gray-600">Próxima revisão:</span>
                      <p class="font-semibold">{{ formatarData(manutencao.proximaRevisao) }}</p>
                    </div>
                  </div>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    size="small"
                    variant="text"
                    color="primary"
                    @click="editarManutencao(manutencao)"
                  >
                    Editar
                  </v-btn>
                  <v-btn
                    size="small"
                    variant="text"
                    color="error"
                    @click="deletarManutencao(manutencao.id)"
                  >
                    Deletar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <v-icon size="40" class="text-gray-400 mb-2">mdi-history</v-icon>
              <p>Nenhuma manutenção registrada</p>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Dialog Cadastro/Edição de Veículo -->
    <v-dialog v-model="mostrarCadastroVeiculo" max-width="600px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ veiculoEmEdicao?.id ? 'Editar Veículo' : 'Novo Veículo' }}
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form ref="formVeiculoRef" @submit.prevent="salvarVeiculo">
            <v-text-field
              v-model="formVeiculo.placa"
              label="Placa"
              placeholder="ABC-1234"
              required
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="formVeiculo.marca"
              label="Marca"
              required
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="formVeiculo.modelo"
              label="Modelo"
              required
              class="mb-4"
            ></v-text-field>

            <v-row>
              <v-col cols="6">
                <v-text-field
                  v-model.number="formVeiculo.ano"
                  label="Ano"
                  type="number"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="formVeiculo.cor"
                  label="Cor"
                  required
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="6">
                <v-text-field
                  v-model.number="formVeiculo.quilometragem"
                  label="Quilometragem (km)"
                  type="number"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-select
                  v-model="formVeiculo.combustivel"
                  label="Combustível"
                  :items="['Gasolina', 'Diesel', 'Etanol', 'Elétrico', 'Híbrido']"
                  required
                ></v-select>
              </v-col>
            </v-row>

            <v-checkbox
              v-model="formVeiculo.ativo"
              label="Veículo ativo"
            ></v-checkbox>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="gray" variant="text" @click="mostrarCadastroVeiculo = false">
            Cancelar
          </v-btn>
          <v-btn color="primary" @click="salvarVeiculo">
            {{ veiculoEmEdicao?.id ? 'Atualizar' : 'Cadastrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog Nova Manutenção -->
    <v-dialog v-model="mostrarNovaManutencao" max-width="600px">
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ manutencaoEmEdicao?.id ? 'Editar Manutenção' : 'Nova Manutenção' }}
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form ref="formManutencaoRef" @submit.prevent="salvarManutencao">
            <v-select
              v-model="formManutencao.tipo"
              label="Tipo de Manutenção"
              :items="[
                'Troca de Óleo',
                'Revisão Completa',
                'Troca de Pneus',
                'Alinhamento',
                'Balanceamento',
                'Troca de Pastilhas',
                'Troca de Correia',
                'Limpeza de Bico',
                'Outro'
              ]"
              required
              class="mb-4"
            ></v-select>

            <v-text-field
              v-model="formManutencao.descricao"
              label="Descrição"
              placeholder="Detalhes da manutenção..."
              multiline
              rows="3"
              class="mb-4"
            ></v-text-field>

            <v-row>
              <v-col cols="6">
                <v-text-field
                  v-model="formManutencao.data"
                  label="Data"
                  type="date"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model.number="formManutencao.quilometragem"
                  label="Quilometragem (km)"
                  type="number"
                  required
                ></v-text-field>
              </v-col>
            </v-row>

            <v-text-field
              v-model.number="formManutencao.valor"
              label="Valor (R$)"
              type="number"
              step="0.01"
              required
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="formManutencao.oficina"
              label="Oficina"
              required
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="formManutencao.telefone"
              label="Telefone"
              placeholder="(11) 99999-9999"
              class="mb-4"
            ></v-text-field>

            <v-text-field
              v-model="formManutencao.proximaRevisao"
              label="Próxima Revisão"
              type="date"
              class="mb-4"
            ></v-text-field>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="gray" variant="text" @click="mostrarNovaManutencao = false">
            Cancelar
          </v-btn>
          <v-btn color="primary" @click="salvarManutencao">
            {{ manutencaoEmEdicao?.id ? 'Atualizar' : 'Registrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

// States
const mostrarHistorico = ref(false)
const mostrarCadastroVeiculo = ref(false)
const mostrarNovaManutencao = ref(false)
const veiculoSelecionado = ref(null)
const veiculoEmEdicao = ref(null)
const manutencaoEmEdicao = ref(null)

// Form references
const formVeiculoRef = ref(null)
const formManutencaoRef = ref(null)

// Form models
const formVeiculo = reactive({
  placa: '',
  marca: '',
  modelo: '',
  ano: null,
  cor: '',
  quilometragem: 0,
  combustivel: 'Gasolina',
  ativo: true
})

const formManutencao = reactive({
  tipo: '',
  descricao: '',
  data: new Date().toISOString().split('T')[0],
  quilometragem: 0,
  valor: 0,
  oficina: '',
  telefone: '',
  proximaRevisao: ''
})

// Dados hardcoded
const veiculos = ref([
  {
    id: 1,
    placa: 'ABC-1234',
    marca: 'Toyota',
    modelo: 'Corolla',
    ano: 2022,
    cor: 'Prata',
    quilometragem: 45000,
    combustivel: 'Gasolina',
    ativo: true,
    ultimaManutencao: '2025-10-15',
    manutencoes: [
      {
        id: 1,
        tipo: 'Troca de Óleo',
        descricao: 'Troca de óleo e filtro 5W-30',
        data: '2025-10-15',
        quilometragem: 45000,
        valor: 150.00,
        oficina: 'Oficina do João',
        telefone: '(11) 98765-4321',
        proximaRevisao: '2025-12-15'
      },
      {
        id: 2,
        tipo: 'Revisão Completa',
        descricao: 'Revisão dos 40 mil km: pneus, bateria, filtros',
        data: '2025-09-20',
        quilometragem: 40000,
        valor: 450.00,
        oficina: 'Toyota Autorizada',
        telefone: '(11) 3333-3333',
        proximaRevisao: '2025-11-20'
      },
      {
        id: 3,
        tipo: 'Alinhamento',
        descricao: 'Alinhamento e balanceamento',
        data: '2025-08-10',
        quilometragem: 38000,
        valor: 120.00,
        oficina: 'Rodão do Brasil',
        telefone: '(11) 2222-2222',
        proximaRevisao: null
      }
    ]
  },
  {
    id: 2,
    placa: 'XYZ-5678',
    marca: 'Honda',
    modelo: 'Civic',
    ano: 2021,
    cor: 'Preto',
    quilometragem: 62000,
    combustivel: 'Gasolina',
    ativo: true,
    ultimaManutencao: '2025-09-10',
    manutencoes: [
      {
        id: 4,
        tipo: 'Troca de Óleo',
        descricao: 'Troca de óleo e filtro 5W-30',
        data: '2025-09-10',
        quilometragem: 62000,
        valor: 180.00,
        oficina: 'Honda Autorizada',
        telefone: '(11) 4444-4444',
        proximaRevisao: '2025-11-10'
      },
      {
        id: 5,
        tipo: 'Troca de Pastilhas',
        descricao: 'Troca de pastilhas de freio dianteiras',
        data: '2025-07-05',
        quilometragem: 58000,
        valor: 350.00,
        oficina: 'Oficina do João',
        telefone: '(11) 98765-4321',
        proximaRevisao: null
      }
    ]
  },
  {
    id: 3,
    placa: 'DEF-9012',
    marca: 'Volkswagen',
    modelo: 'Polo',
    ano: 2020,
    cor: 'Branco',
    quilometragem: 78000,
    combustivel: 'Diesel',
    ativo: false,
    ultimaManutencao: '2025-06-20',
    manutencoes: [
      {
        id: 6,
        tipo: 'Revisão Completa',
        descricao: 'Revisão dos 75 mil km',
        data: '2025-06-20',
        quilometragem: 75000,
        valor: 520.00,
        oficina: 'VW Autorizada',
        telefone: '(11) 5555-5555',
        proximaRevisao: '2026-06-20'
      }
    ]
  }
])

// Methods
function selecionarVeiculo(veiculo) {
  veiculoSelecionado.value = veiculo
  mostrarHistorico.value = true
}

function openCadastroDialog() {
  veiculoEmEdicao.value = null
  Object.assign(formVeiculo, {
    placa: '',
    marca: '',
    modelo: '',
    ano: null,
    cor: '',
    quilometragem: 0,
    combustivel: 'Gasolina',
    ativo: true
  })
  mostrarCadastroVeiculo.value = true
}

function editarVeiculo(veiculo) {
  veiculoEmEdicao.value = veiculo
  Object.assign(formVeiculo, veiculo)
  mostrarCadastroVeiculo.value = true
}

function salvarVeiculo() {
  if (veiculoEmEdicao.value?.id) {
    const index = veiculos.value.findIndex(v => v.id === veiculoEmEdicao.value.id)
    if (index !== -1) {
      veiculos.value[index] = {
        ...veiculos.value[index],
        ...formVeiculo,
        manutencoes: veiculos.value[index].manutencoes
      }
    }
  } else {
    veiculos.value.push({
      id: Math.max(...veiculos.value.map(v => v.id), 0) + 1,
      ...formVeiculo,
      manutencoes: []
    })
  }
  mostrarCadastroVeiculo.value = false
}

function deletarVeiculo(id) {
  if (confirm('Tem certeza que deseja deletar este veículo?')) {
    const index = veiculos.value.findIndex(v => v.id === id)
    if (index !== -1) {
      veiculos.value.splice(index, 1)
    }
  }
}

function openNovaManutencaoDialog() {
  manutencaoEmEdicao.value = null
  Object.assign(formManutencao, {
    tipo: '',
    descricao: '',
    data: new Date().toISOString().split('T')[0],
    quilometragem: 0,
    valor: 0,
    oficina: '',
    telefone: '',
    proximaRevisao: ''
  })
  mostrarNovaManutencao.value = true
}

function editarManutencao(manutencao) {
  manutencaoEmEdicao.value = manutencao
  Object.assign(formManutencao, manutencao)
  mostrarNovaManutencao.value = true
}

function salvarManutencao() {
  if (!veiculoSelecionado.value) return

  if (manutencaoEmEdicao.value?.id) {
    const index = veiculoSelecionado.value.manutencoes.findIndex(
      m => m.id === manutencaoEmEdicao.value.id
    )
    if (index !== -1) {
      veiculoSelecionado.value.manutencoes[index] = {
        ...manutencaoEmEdicao.value,
        ...formManutencao
      }
    }
  } else {
    veiculoSelecionado.value.manutencoes.push({
      id: Math.max(...veiculoSelecionado.value.manutencoes.map(m => m.id), 0) + 1,
      ...formManutencao
    })
  }
  mostrarNovaManutencao.value = false
}

function deletarManutencao(id) {
  if (!veiculoSelecionado.value) return

  if (confirm('Tem certeza que deseja deletar esta manutenção?')) {
    const index = veiculoSelecionado.value.manutencoes.findIndex(m => m.id === id)
    if (index !== -1) {
      veiculoSelecionado.value.manutencoes.splice(index, 1)
    }
  }
}

// Formatters
function formatarData(data) {
  if (!data) return '-'
  return new Date(data).toLocaleDateString('pt-BR')
}

function formatarValor(valor) {
  return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function formatarQuilometragem(km) {
  return km.toLocaleString('pt-BR') + ' km'
}
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}

.hover\:shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.transition-shadow {
  transition: box-shadow 0.3s ease;
}

.bg-light {
  background-color: rgba(0, 0, 0, 0.02);
}
</style>
