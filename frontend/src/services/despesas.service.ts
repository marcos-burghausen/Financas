// src/services/despesas.service.ts
import http from './http'

export interface Despesa {
  id?: number
  descricao: string
  valor: number
  categoria: string
  subcategoria?: string
  conta_id: number
  data_vencimento: string
  data_lancamento?: string
  data_efetivacao?: string
  status: 'pendente' | 'paga' | 'cancelada'
  status_lancamento?: string
  observacao?: string
  recorrencia?: string
  forma_pagamento?: string
}

export interface DespesaResponse {
  success: string
  data?: Despesa | Despesa[]
  despesas?: Despesa[]
}

class DespesasService {
  /**
   * Listar despesas do mês
   */
  async list(mesAno?: string): Promise<Despesa[]> {
    try {
      const params = mesAno ? { mesAno } : {}
      const response = await http.get<any>('/despesas', { params })
      
      // Suportar diferentes formatos de resposta
      if (Array.isArray(response.data)) return response.data
      if (response.data?.despesas) return response.data.despesas
      if (response.data?.data) return response.data.data
      
      return []
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Criar nova despesa
   */
  async create(data: Despesa): Promise<Despesa> {
    try {
      const response = await http.post<any>('/despesas', data)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Atualizar despesa
   */
  async update(id: number, data: Despesa): Promise<Despesa> {
    try {
      const response = await http.put<any>(`/despesas/${id}`, data)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Deletar despesa
   */
  async delete(id: number): Promise<void> {
    try {
      await http.delete(`/despesas/${id}`)
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Pagar despesa
   */
  async pay(id: number): Promise<Despesa> {
    try {
      const response = await http.post<any>(`/despesas/${id}/pay`)
      return response.data?.data || response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Obter resumo de despesas
   */
  async summary(mesAno?: string): Promise<any> {
    try {
      const params = mesAno ? { mesAno } : {}
      const response = await http.get<any>('/despesas/summary', { params })
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

export default new DespesasService()
