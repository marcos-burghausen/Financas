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
  tipo: 'despesa' // Tipo de lancamento
}

class DespesasService {
  /**
   * Listar despesas (lancamentos do tipo despesa)
   */
  async list(mesAno?: string): Promise<Despesa[]> {
    try {
      const params = mesAno ? { mesAno, tipo: 'despesa' } : { tipo: 'despesa' }
      const response = await http.get<any>('/lancamentos', { params })
      
      // Filtrar apenas despesas
      const data = Array.isArray(response.data) ? response.data : response.data?.data || []
      return data.filter((item: any) => item.tipo === 'despesa' || item.tipo_lancamento === 'DESPESA')
    } catch (error) {
      console.error('Erro ao listar despesas:', error)
      return []
    }
  }

  /**
   * Criar nova despesa
   */
  async create(data: Despesa): Promise<Despesa> {
    try {
      const payload = {
        ...data,
        tipo: 'despesa',
        tipo_lancamento: 'despesa'
      }
      const response = await http.post<any>('/lancamentos', payload)
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
      const response = await http.put<any>(`/lancamentos/${id}`, data)
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
      await http.delete(`/lancamentos/${id}`)
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Pagar despesa
   */
  async pay(id: number): Promise<Despesa> {
    try {
      const response = await http.patch<any>(`/lancamentos/${id}`, { status: 'paga' })
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

export default new DespesasService()
