import http from './http';

interface Conta {
  id: number;
  name: string
  icon: string
  saldo_inicial?: string
  incluir_em_soma_inicial?: boolean
  tipo_conta: string
  limit?: string
  conta_pai_id?: number | null
  status: String
  description: string | null
  dia_fechamento: number | null
  dia_vencimento?: number | null
}

class ContasService {
  /**
   * Listar todas as contas do usuário
   */
  async list(mesAno?: string): Promise<Conta[]> {
    try {
      const response = await http.get<any>('/wallet', { params: { mesAno } });
      console.log(response);
      
      // Extrair dados das contas
      const contas = Array.isArray(response.data.wallets.contas) ? response.data.wallets.contas : response.data?.contas || [];
      
      return contas.map((c: any) => ({
        id: c.id,
        name: c.name,
        number: c.number,
        agency: c.agency,
        bank: c.bank,
        type: c.type?.toLowerCase() || 'corrente',
        balance: c.balance || 0,
        limit: c.limit,
        status: c.status === 'ativa' ? 'ativa' : 'inativa',
        description: c.description,
        opening_date: c.opening_date,
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
        number: data.number,
        agency: data.agency,
        bank: data.bank,
        type: data.type?.toUpperCase(),
        balance: data.balance,
        limit: data.limit,
        status: data.status,
        description: data.description,
      };
      
      const response = await http.post<any>('/edit-wallets', payload);
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
