import type { Category } from "./";
export interface Lancamento {
    id?: number;
    descricao?: string;
    valor?: string; // Changed to number for consistency
    tipo?: "Não recorrente" | "Parcelada" | "Fixa mensal";
    num_parcelas?: number;
    periodicidade?: "Mensal" | "Diario" | "Semanal" | "Quinzenal" | "Trimenstral" | "Anual";
    data_vencimento?: string;
    status?: "Efetivada" | "Pendente";
    categoria?: string;
    subcategoria?: string;
    conta?: string;
    data_lancamento?: string;
    data_efetivacao?: string;
  }

  export interface TransactionsData {
    by_category: [];
    value_pay: number;
    value_pending: number;
    value_total_month: number;
    by_month: [];
    categories: Category[];
    total_days: number;
  }
  
  // Represents transactions grouped by month
  export interface LancamentosPorMes {
    [mes_ano: string]: Lancamento[];
  }
  
  // Data for monthly financial summaries
  export interface MonthData {
    month: string; // e.g., "2025-04"
    total: number;
  }
  
  // Data for financial summaries by category
  export interface CategoryData {
    category_id: number;
    name: string;
    total: number;
  }