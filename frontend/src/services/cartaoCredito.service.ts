import http from './http';

interface CartaoCredito {
  id: number;
  name: string;
  icon?: string;
  color?: string;
  tipo_conta: string;
  limite?: number;
  saldo?: number;
  descricao?: string;
  dia_fechamento?: number;
  dia_vencimento?: number;
  conta_pai_id?: number | null;
  conta_pai_name?: string | null;
  total_fatura_vigente?: number;
  valor_em_aberto?: number;
  data_fechamento?: string;
  data_vencimento?: string;
  status_fatura?: string;
  lancamentos_fatura_vigente?: any[];
}

class CartaoCreditoService {
  /**
   * Listar todos os cartões de crédito do usuário
   */
  async list(mesAno?: string): Promise<CartaoCredito[]> {
    try {
      const response = await http.get<any>('/wallet', { params: { mesAno } });
      console.log('Resposta da API /wallet (cartões):', response);
      
      // Extrair dados dos cartões - a estrutura é response.data.wallets.cartoes
      const cartoesData = response.data?.wallets?.cartoes || [];
      
      if (!Array.isArray(cartoesData)) {
        console.warn('Cartões não é um array:', cartoesData);
        return [];
      }
      
      return cartoesData.map((c: any) => ({
        id: c.id,
        name: c.name,
        icon: c.icon || 'mdi-credit-card',
        color: c.color || '#e53935',
        tipo_conta: c.tipo_conta || 'Cartão de Crédito',
        limite: c.limite || 0,
        saldo: c.saldo || 0,
        descricao: c.descricao || '',
        dia_fechamento: c.dia_fechamento || 0,
        dia_vencimento: c.dia_vencimento || 0,
        conta_pai_id: c.conta_pai_id || null,
        conta_pai_name: c.conta_pai_name || null,
        total_fatura_vigente: c.total_fatura_vigente || 0,
        valor_em_aberto: c.valor_em_aberto || 0,
        data_fechamento: c.data_fechamento || null,
        data_vencimento: c.data_vencimento || null,
        status_fatura: c.status_fatura || 'INEXISTENTE',
        lancamentos_fatura_vigente: c.lancamentos_fatura_vigente || [],
      }));
    } catch (error) {
      console.error('Erro ao listar cartões:', error);
      return [];
    }
  }

  /**
   * Criar novo cartão
   */
  async create(data: Omit<CartaoCredito, 'id'>): Promise<CartaoCredito> {
    try {
      const payload = {
        name: data.name,
        icon: data.icon,
        tipo_conta: 'Cartão de Crédito',
        limite: data.limite,
        descricao: data.descricao,
        dia_fechamento: data.dia_fechamento,
        dia_vencimento: data.dia_vencimento,
        color: data.color,
      };
      
      const response = await http.post<any>('/wallet', payload);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Atualizar cartão
   */
  async update(id: number, data: Partial<CartaoCredito>): Promise<CartaoCredito> {
    try {
      const payload = {
        id,
        name: data.name,
        icon: data.icon,
        tipo_conta: 'Cartão de Crédito',
        limite: data.limite,
        descricao: data.descricao,
        dia_fechamento: data.dia_fechamento,
        dia_vencimento: data.dia_vencimento,
        color: data.color,
      };
      
      const response = await http.post<any>('/wallet', payload);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Deletar cartão
   */
  async delete(id: number): Promise<void> {
    try {
      await http.delete(`/wallet/${id}`);
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Tratamento de erros
   */
  private handleError(error: any): Error {
    const message = error.response?.data?.message || error.message || 'Erro desconhecido';
    return new Error(message);
  }
}

export default new CartaoCreditoService();
