// src/services/dashboard.service.ts
import http from './http'

export interface Transaction {
  id: number
  descricao: string
  valor: number
  data: string
  status: string
  tipo: 'receita' | 'despesa'
  categoria?: string
  tipo_lancamento?: string
}

export interface DashboardData {
  transacoes: Transaction[]
  categorias: Record<string, number>
}

class DashboardService {
  /**
   * Obter transações recentes para a dashboard
   */
  async getRecentTransactions(limit: number = 10): Promise<Transaction[]> {
    try {
      const response = await http.get<any>('/lancamentos', {
        params: {
          limit: limit,
          sort: '-data',
          select: 'id,descricao,valor,data,status,tipo,categoria,tipo_lancamento'
        }
      })

      const data = response.data || response
      const lancamentos = data.data || data.lancamentos || []

      // Normalizar para formato esperado
      return lancamentos.map((item: any) => ({
        id: item.id,
        descricao: item.descricao || item.tipo_lancamento || 'Transação',
        valor: item.valor || 0,
        data: this.formatDate(item.data || item.created_at),
        status: item.status || 'confirmado',
        tipo: item.tipo === 'receita' || item.tipo === 'R' ? 'receita' : 'despesa',
        categoria: item.categoria || 'Sem categoria'
      }))
    } catch (error) {
      console.error('Erro ao carregar transações recentes:', error)
      return []
    }
  }

  /**
   * Obter distribuição de despesas por categoria
   */
  async getExpensesByCategory(): Promise<{ labels: string[]; values: number[] }> {
    try {
      const response = await http.get<any>('/lancamentos/analise/categorias')

      const data = response.data || response
      const categorias = data.data || data.categorias || {}

      const labels = Object.keys(categorias)
      const values = Object.values(categorias) as number[]

      // Calcular percentuais
      const total = values.reduce((a, b) => a + b, 0)
      const percentuais = values.map(v => (total > 0 ? (v / total) * 100 : 0))

      return {
        labels,
        values: percentuais
      }
    } catch (error) {
      console.error('Erro ao carregar categorias:', error)
      // Retornar dados padrão
      return {
        labels: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Outros'],
        values: [25.2, 18.5, 30.1, 15.3, 10.9]
      }
    }
  }

  /**
   * Obter contadores de transações (recebidas, pagas, pendentes, atrasadas)
   */
  async getTransactionCounters(): Promise<any> {
    try {
      const response = await http.get<any>('/lancamentos/analise/contadores')

      const data = response.data || response

      return {
        receitasRecebidas: data.receitasRecebidas || 0,
        receitasPendentes: data.receitasPendentes || 0,
        receitasAtrasadas: data.receitasAtrasadas || 0,
        despesasPagas: data.despesasPagas || 0,
        despesasPendentes: data.despesasPendentes || 0,
        despesasAtrasadas: data.despesasAtrasadas || 0
      }
    } catch (error) {
      console.error('Erro ao carregar contadores:', error)
      return {
        receitasRecebidas: 0,
        receitasPendentes: 0,
        receitasAtrasadas: 0,
        despesasPagas: 0,
        despesasPendentes: 0,
        despesasAtrasadas: 0
      }
    }
  }

  /**
   * Formatar data
   */
  private formatDate(dateString: string): string {
    try {
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR')
    } catch {
      return dateString
    }
  }
}

export default new DashboardService()
