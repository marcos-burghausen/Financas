import http from './http';

interface Conta {
  id: number;
  name: string;
  icon?: string;
  color?: string;
  number?: string;
  agency?: string;
  bank?: string;
  type?: 'corrente' | 'poupanca' | 'investimento';
  balance?: number;
  limit?: number;
  status?: 'ativa' | 'inativa';
  description?: string | null;
  opening_date?: string;
  saldo_inicial?: string;
  incluir_em_soma_inicial?: boolean;
  tipo_conta?: string;
  conta_pai_id?: number | null;
  dia_fechamento?: number | null;
  dia_vencimento?: number | null;
}

class ContasService {
  /**
   * Listar todas as contas do usuário
   */
  async list(mesAno?: string): Promise<Conta[]> {
    try {
      const response = await http.get<any>('/wallet', { params: { mesAno } });
      console.log('Resposta da API /wallet:', response);
      
      // Extrair dados das contas - a estrutura é response.data.wallets.contas
      const contasData = response.data?.wallets?.contas || [];
      
      if (!Array.isArray(contasData)) {
        console.warn('Contas não é um array:', contasData);
        return [];
      }
      
      return contasData.map((c: any) => ({
        id: c.id,
        color: c.color || '#163dc0',
        name: c.name,
        number: c.number || '',
        agency: c.agency || '',
        bank: c.bank || 'Banco',
        icon: c.icon || '',
        tipo_conta: c.tipo_conta || '', // Incluindo tipo_conta para filtro
        type: c.tipo_conta?.toLowerCase().includes('poupança') ? 'poupanca' 
              : c.tipo_conta?.toLowerCase().includes('investimento') ? 'investimento'
              : 'corrente',
        balance: c.saldo || 0,
        limit: c.limite || 0,
        status: c.ativo === false || c.status === 'inativa' ? 'inativa' : 'ativa',
        description: c.descricao || '',
        opening_date: c.data_abertura || '',
      }));
    } catch (error) {
      console.error('Erro ao listar contas:', error);
      return [];
    }
  }

  /**
   * Criar nova conta
   */
  async create(data: Omit<Conta, 'id'>): Promise<Conta> {
    try {
      const payload = {
        name: data.name,
        icon: data.icon,
        saldo_inicial: data.saldo_inicial,
        incluir_em_soma_inicial: data.incluir_em_soma_inicial,
        tipo_conta: data.tipo_conta,
        limit: data.limit,
        status_conta: data.status,
        description: data.description,
      };
      
      const response = await http.post<any>('/wallet', payload);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Atualizar conta
   */
  async update(id: number, data: Partial<Conta>): Promise<Conta> {
    try {
      const payload = {
        id,
        name: data.name,
        icon: data.icon,
        saldo_inicial: data.saldo_inicial,
        incluir_em_soma_inicial: data.incluir_em_soma_inicial,
        tipo_conta: data.tipo_conta,
        limit: data.limit,
        status_conta: data.status,
        description: data.description,
        color: data.color,
      };
      
      const response = await http.post<any>('/wallet', payload);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Deletar conta
   */
  async delete(id: number): Promise<void> {
    try {
      await http.post('/delete-wallets', { id });
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Tratamento de erros
   */
  private handleError(error: any): Error {
    console.error('ContasService Error:', error);
    const message = error?.response?.data?.message || error?.message || 'Erro ao processar conta';
    return new Error(message);
  }
}

export default new ContasService();
