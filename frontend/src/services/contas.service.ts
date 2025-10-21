import http from './http';

interface Conta {
  id: number;
  name: string;
  number: string;
  agency: string;
  bank: string;
  type: 'corrente' | 'poupanca' | 'investimento';
  balance: number;
  limit?: number;
  status: 'ativa' | 'inativa';
  description?: string;
  opening_date?: string;
}

class ContasService {
  /**
   * Listar todas as contas do usuário
   */
  async list(): Promise<Conta[]> {
    try {
      const response = await http.get<any>('/user-data/wallets');
      
      // Extrair dados das contas
      const contas = Array.isArray(response.data) ? response.data : response.data?.contas || [];
      
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
        number: data.number,
        agency: data.agency,
        bank: data.bank,
        type: data.type?.toUpperCase(),
        balance: data.balance,
        limit: data.limit,
        status: data.status,
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
