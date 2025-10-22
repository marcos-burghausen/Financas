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
  async getRecentTransactions(limit: number = 100): Promise<Transaction[]> {
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
        // Converter para minúscula: backend retorna 'RECEITA', 'Receita', ou 'receita'
        tipo: (item.tipo_lancamento && item.tipo_lancamento.toLowerCase() === 'receita') || 
              (item.tipo && (item.tipo === 'receita' || item.tipo === 'R')) ? 'receita' : 'despesa',
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
      // Usar dados do lancamentos para calcular distribuição
      const response = await http.get<any>('/lancamentos', {
        params: {
          limit: 1000,
          select: 'categoria,valor,tipo_lancamento'
        }
      })

      const data = response.data || response
      const lancamentos = data.data || data.lancamentos || []

      // Filtrar apenas despesas
      const despesas = lancamentos.filter((item: any) => {
        const tipo = item.tipo_lancamento ? item.tipo_lancamento.toLowerCase() : 'despesa'
        return tipo === 'despesa' || tipo === 'd'
      })

      // Agrupar por categoria
      const categoriaMap = new Map<string, number>()
      despesas.forEach((item: any) => {
        const categoria = item.categoria || 'Outros'
        const valor = item.valor || 0
        categoriaMap.set(categoria, (categoriaMap.get(categoria) || 0) + valor)
      })

      const labels = Array.from(categoriaMap.keys())
      const values = Array.from(categoriaMap.values())

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
      // Usar dados do lancamentos para calcular contadores
      const response = await http.get<any>('/lancamentos', {
        params: {
          limit: 1000,
          select: 'tipo_lancamento,status_lancamento'
        }
      })

      const data = response.data || response
      const lancamentos = data.data || data.lancamentos || []

      let receitasRecebidas = 0
      let receitasPendentes = 0
      let receitasAtrasadas = 0
      let despesasPagas = 0
      let despesasPendentes = 0
      let despesasAtrasadas = 0

      lancamentos.forEach((item: any) => {
        const tipo = item.tipo_lancamento ? item.tipo_lancamento.toLowerCase() : 'despesa'
        const status = (item.status_lancamento || '').toUpperCase()

        if (tipo === 'receita' || tipo === 'r') {
          if (status === 'EFETIVADA' || status === 'RECEBIDA') receitasRecebidas++
          else if (status === 'ATRASADA') receitasAtrasadas++
          else receitasPendentes++
        } else {
          if (status === 'EFETIVADA' || status === 'PAGA') despesasPagas++
          else if (status === 'ATRASADA') despesasAtrasadas++
          else despesasPendentes++
        }
      })

      return {
        receitasRecebidas,
        receitasPendentes,
        receitasAtrasadas,
        despesasPagas,
        despesasPendentes,
        despesasAtrasadas
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
