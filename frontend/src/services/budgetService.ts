// File: src/services/budgetService.ts
import type { Budget, BudgetFormData } from "@/types/budget.types";
import axiosInstance from "./http";

export interface ApiBudgetResponse {
  success: boolean;
  data?: {
    budgets: Budget[];
    resumo: {
      total_orcado: number;
      total_gasto: number;
      saldo_restante: number;
      meta_economia: number;
      percentual_gasto_geral: number;
      total_categorias: number;
    };
    mesAno: string;
  };
  message?: string;
  errors?: Record<string, string[]>;
}

export interface ApiBudgetItemResponse {
  success: boolean;
  data?: Budget;
  message?: string;
  errors?: Record<string, string[]>;
}

export interface ApiCategoriasResponse {
  success: boolean;
  data?: string[];
  message?: string;
}

class BudgetService {
  private readonly baseUrl = "/budgets";

  /**
   * Buscar orçamentos do mês
   */
  async getBudgets(mesAno?: string, categoria?: string): Promise<ApiBudgetResponse> {
    const params = new URLSearchParams();
    if (mesAno) params.append("mesAno", mesAno);
    if (categoria) params.append("categoria", categoria);

    const response = await axiosInstance.get(`${this.baseUrl}?${params.toString()}`);
    return response.data;
  }

  /**
   * Criar novo orçamento
   */
  async createBudget(budgetData: BudgetFormData): Promise<ApiBudgetItemResponse> {
    const response = await axiosInstance.post(this.baseUrl, {
      categoria: budgetData.categoria,
      valor_orcado: Math.round(budgetData.valor_orcado * 100), // Converter para centavos
      mes_ano: budgetData.mes_ano,
      observacao: budgetData.observacao || null,
    });
    return response.data;
  }

  /**
   * Buscar orçamento específico
   */
  async getBudget(id: number): Promise<ApiBudgetItemResponse> {
    const response = await axiosInstance.get(`${this.baseUrl}/${id}`);
    return response.data;
  }

  /**
   * Atualizar orçamento
   */
  async updateBudget(id: number, budgetData: Partial<BudgetFormData>): Promise<ApiBudgetItemResponse> {
    const payload: any = {};

    if (budgetData.categoria) payload.categoria = budgetData.categoria;
    if (budgetData.valor_orcado !== undefined) {
      payload.valor_orcado = Math.round(budgetData.valor_orcado * 100); // Converter para centavos
    }
    if (budgetData.mes_ano) payload.mes_ano = budgetData.mes_ano;
    if (budgetData.observacao !== undefined) payload.observacao = budgetData.observacao || null;

    const response = await axiosInstance.put(`${this.baseUrl}/${id}`, payload);
    return response.data;
  }

  /**
   * Excluir orçamento
   */
  async deleteBudget(id: number): Promise<{ success: boolean; message?: string }> {
    const response = await axiosInstance.delete(`${this.baseUrl}/${id}`);
    return response.data;
  }

  /**
   * Buscar categorias disponíveis
   */
  async getCategorias(): Promise<ApiCategoriasResponse> {
    const response = await axiosInstance.get("/budgets-categorias");
    return response.data;
  }
}

export const budgetService = new BudgetService();