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
}

export interface ReceitaResponse {
  success: string
  data?: Receita | Receita[]
  receitas?: Receita[]
}

class ReceitasService {
  /**
   * Listar receitas do mês
   */
  async list(mesAno?: string): Promise<Receita[]> {
    try {
      const params = mesAno ? { mesAno } : {}
      const response = await http.get<any>('/receitas', { params })
      
      // Suportar diferentes formatos de resposta
      if (Array.isArray(response.data)) return response.data
      if (response.data?.receitas) return response.data.receitas
      if (response.data?.data) return response.data.data
      
      return []
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Criar nova receita
   */
  async create(data: Receita): Promise<Receita> {
    try {
      const response = await http.post<any>('/receitas', data)
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
      const response = await http.put<any>(`/receitas/${id}`, data)
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
      await http.delete(`/receitas/${id}`)
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Receber receita (marcar como recebida)
   */
  async receive(id: number): Promise<Receita> {
    try {
      const response = await http.post<any>(`/receitas/${id}/receive`)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Obter resumo de receitas
   */
  async summary(mesAno?: string): Promise<any> {
    try {
      const params = mesAno ? { mesAno } : {}
      const response = await http.get<any>('/receitas/summary', { params })
      return response.data
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
