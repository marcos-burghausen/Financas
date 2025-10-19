// src/services/despesas.service.ts
import http from './http'

export interface Despesa {
  id?: number | null
  user_id?: number | null
  invoice_id?: number | null
  descricao: string
  valor: string // Valor em formato string "10,00" ou número
  categoria: string
  subcategoria?: string
  conta_id?: number | null
  data_vencimento: string
  data_lancamento?: string | Date
  data_efetivacao?: string | Date | null
  status?: 'pendente' | 'paga' | 'cancelada'
  status_lancamento?: 'EFETIVADA' | 'PENDENTE'
  observacao?: string
  observacoes?: string | null
  recorrencia?: "Não recorrente" | "Fixa" | "Parcelado"
  forma_pagamento?: string
  tipo?: 'despesa' // Tipo de lancamento (frontend)
  tipo_lancamento?: string // Tipo de lancamento (API - RECEITA, DESPESA, etc)
  mesAno?: string // Mês/ano no formato YYYY-MM
  qtd_parcelas?: number | null
  num_parcela?: number | null
  tipo_parcela?: "total" | "parcela" | null
  periodicidade?: "Mensal" | "Diario" | "Semanal" | "Quinzenal" | "Bimestral" | "Trimenstral" | "Anual" | null
  is_estorno?: boolean
  original_lancamento_id?: number | null
  fatura?: string | null // "YYYY-MM"
  cartao_id?: number | null
  conta_model?: { id: number; nome: string }
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
        // Garantir que tipo_lancamento seja 'DESPESA' (MAIÚSCULA)
        tipo_lancamento: data.tipo_lancamento || 'DESPESA'
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
    console.error('DespesasService Error:', error);
    
    // Se temos resposta com erro
    if (error.response?.data) {
      const data = error.response.data;
      
      // Se tem mensagem de erro
      if (data.message) {
        return new Error(data.message);
      }
      
      // Se tem erros de validação (Laravel)
      if (data.errors) {
        const errors = data.errors;
        const firstError = Object.values(errors)[0];
        if (Array.isArray(firstError)) {
          return new Error(firstError[0]);
        }
        return new Error(JSON.stringify(errors));
      }
      
      // Se tem erro geral
      if (data.error) {
        return new Error(data.error);
      }
    }
    
    // Se temos mensagem de erro da resposta
    if (error.response?.statusText) {
      return new Error(`${error.response.status} - ${error.response.statusText}`);
    }
    
    // Erro genérico
    return error || new Error('Erro desconhecido ao salvar despesa');
  }
}

export default new DespesasService()
