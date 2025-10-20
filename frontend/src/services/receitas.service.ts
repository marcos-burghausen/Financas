// src/services/receitas.service.ts
import http from "./http";

export interface Receita {
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
  status?: "pendente" | "recebida" | "cancelada"
  status_lancamento?: "EFETIVADA" | "PENDENTE"
  observacao?: string
  observacoes?: string | null
  recorrencia?: "Não recorrente" | "Fixa" | "Parcelado"
  tipo?: "receita" // Tipo de lancamento (frontend)
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

class ReceitasService {
  /**
   * Listar receitas (lancamentos do tipo receita)
   */
  async list(mesAno?: string): Promise<Receita[]> {
    try {
      const params = mesAno ? { mesAno, tipo: "receita" } : { tipo: "receita" };
      const response = await http.get<any>("/lancamentos", { params });
      
      // Filtrar apenas receitas
      const data = Array.isArray(response.data) ? response.data : response.data?.data || [];
      return data.filter((item: any) => item.tipo === "receita" || item.tipo_lancamento === "RECEITA");
    } catch (error) {
      console.error("Erro ao listar receitas:", error);
      return [];
    }
  }

  /**
   * Criar nova receita
   */
  async create(data: Receita): Promise<Receita> {
    try {
      const payload = {
        ...data,
        // Garantir que tipo_lancamento seja 'RECEITA' (MAIÚSCULA)
        tipo_lancamento: data.tipo_lancamento || "RECEITA"
      };
      const response = await http.post<any>("/lancamentos", payload);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Atualizar receita
   */
  async update(id: number, data: Receita): Promise<Receita> {
    try {
      const response = await http.put<any>(`/lancamentos/${id}`, data);
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Deletar receita
   */
  async delete(id: number): Promise<void> {
    try {
      await http.delete(`/lancamentos/${id}`);
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Receber receita (marcar como recebida)
   */
  async receive(id: number): Promise<Receita> {
    try {
      const response = await http.patch<any>(`/lancamentos/${id}`, { status: "recebida" });
      return response.data?.data || response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Tratamento de erros padronizado
   */
  private handleError(error: any): Error {
    console.error("ReceitasService Error:", error);
    
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
    return error || new Error("Erro desconhecido ao salvar receita");
  }
}

export default new ReceitasService();
