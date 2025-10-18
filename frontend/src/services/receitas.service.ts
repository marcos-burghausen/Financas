// src/services/receitas.service.ts
import http from './http'

export interface Receita {
  id?: number
  descricao: string
  valor: number
  categoria: string
  subcategoria?: string
  conta_id: number
  data_vencimento: string
  data_lancamento?: string
  data_efetivacao?: string
  status: 'pendente' | 'recebida' | 'cancelada'
  status_lancamento?: string
  observacao?: string
  recorrencia?: string
  tipo: 'receita' // Tipo de lancamento
}

class ReceitasService {
  /**
   * Listar receitas (lancamentos do tipo receita)
   */
  async list(mesAno?: string): Promise<Receita[]> {
    try {
      const params = mesAno ? { mesAno, tipo: 'receita' } : { tipo: 'receita' }
      const response = await http.get<any>('/lancamentos', { params })
      
      // Filtrar apenas receitas
      const data = Array.isArray(response.data) ? response.data : response.data?.data || []
      return data.filter((item: any) => item.tipo === 'receita' || item.tipo_lancamento === 'RECEITA')
    } catch (error) {
      console.error('Erro ao listar receitas:', error)
      return []
    }
  }

  /**
   * Criar nova receita
   */
  async create(data: Receita): Promise<Receita> {
    try {
      const payload = {
        ...data,
        tipo: 'receita',
        tipo_lancamento: 'receita'
      }
      const response = await http.post<any>('/lancamentos', payload)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Atualizar receita
   */
  async update(id: number, data: Receita): Promise<Receita> {
    try {
      const response = await http.put<any>(`/lancamentos/${id}`, data)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Deletar receita
   */
  async delete(id: number): Promise<void> {
    try {
      await http.delete(`/lancamentos/${id}`)
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Receber receita (marcar como recebida)
   */
  async receive(id: number): Promise<Receita> {
    try {
      const response = await http.patch<any>(`/lancamentos/${id}`, { status: 'recebida' })
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Tratamento de erros padronizado
   */
  private handleError(error: any): Error {
    if (error.response?.data?.message) {
      return new Error(error.response.data.message)
    }
    if (error.response?.data?.error) {
      return new Error(error.response.data.error)
    }
    return error
  }
}

export default new ReceitasService()
