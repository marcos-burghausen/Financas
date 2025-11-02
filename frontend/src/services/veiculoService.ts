import axiosInstance from './http'

const API_BASE = '/veiculos'
const MANUTENCAO_BASE = '/manutencoes'

export interface Veiculo {
  id?: number
  user_id?: number
  placa: string
  marca: string
  modelo: string
  ano: number
  cor?: string
  quilometragem: number
  combustivel: 'Gasolina' | 'Diesel' | 'Etanol' | 'Híbrido' | 'Elétrico'
  proximaManutencao: number
  status: 'ativo' | 'inativo' | 'manutenção'
  created_at?: string
  updated_at?: string
  manutencoes?: Manutencao[]
}

export interface ManutencaoItem {
  id?: number
  manutencao_id?: number
  nome: string
  descricao?: string
  quantidade: number
  valor_unitario: number
  valor_total?: number
  created_at?: string
  updated_at?: string
}

export interface Manutencao {
  id?: number
  veiculo_id: number
  tipo: string
  data: string
  quilometragem: number
  valor_total?: number
  oficina_nome?: string
  oficina_telefone?: string
  oficina_email?: string
  oficina_endereco?: string
  observacoes?: string
  itens?: ManutencaoItem[]
  created_at?: string
  updated_at?: string
  veiculo?: Veiculo
}

// Veículos
export const veiculoService = {
  // Listar todos os veículos
  async getVeiculos() {
    try {
      const response = await axiosInstance.get(API_BASE)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar veículos:', error)
      throw error
    }
  },

  // Obter um veículo específico
  async getVeiculo(id: number) {
    try {
      const response = await axiosInstance.get(`${API_BASE}/${id}`)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar veículo:', error)
      throw error
    }
  },

  // Criar novo veículo
  async createVeiculo(data: Veiculo) {
    try {
      const response = await axiosInstance.post(API_BASE, data)
      return response.data
    } catch (error) {
      console.error('Erro ao criar veículo:', error)
      throw error
    }
  },

  // Atualizar veículo
  async updateVeiculo(id: number, data: Partial<Veiculo>) {
    try {
      const response = await axiosInstance.put(`${API_BASE}/${id}`, data)
      return response.data
    } catch (error) {
      console.error('Erro ao atualizar veículo:', error)
      throw error
    }
  },

  // Deletar veículo
  async deleteVeiculo(id: number) {
    try {
      const response = await axiosInstance.delete(`${API_BASE}/${id}`)
      return response.data
    } catch (error) {
      console.error('Erro ao deletar veículo:', error)
      throw error
    }
  },
}

// Manutenções
export const manutencaoService = {
  // Listar todas as manutenções
  async getManutencoes() {
    try {
      const response = await axiosInstance.get(MANUTENCAO_BASE)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar manutenções:', error)
      throw error
    }
  },

  // Obter uma manutenção específica
  async getManutencao(id: number) {
    try {
      const response = await axiosInstance.get(`${MANUTENCAO_BASE}/${id}`)
      return response.data
    } catch (error) {
      console.error('Erro ao buscar manutenção:', error)
      throw error
    }
  },

  // Criar nova manutenção
  async createManutencao(data: Manutencao) {
    try {
      const response = await axiosInstance.post(MANUTENCAO_BASE, data)
      return response.data
    } catch (error) {
      console.error('Erro ao criar manutenção:', error)
      throw error
    }
  },

  // Atualizar manutenção
  async updateManutencao(id: number, data: Partial<Manutencao>) {
    try {
      const response = await axiosInstance.put(`${MANUTENCAO_BASE}/${id}`, data)
      return response.data
    } catch (error) {
      console.error('Erro ao atualizar manutenção:', error)
      throw error
    }
  },

  // Deletar manutenção
  async deleteManutencao(id: number) {
    try {
      const response = await axiosInstance.delete(`${MANUTENCAO_BASE}/${id}`)
      return response.data
    } catch (error) {
      console.error('Erro ao deletar manutenção:', error)
      throw error
    }
  },
}
