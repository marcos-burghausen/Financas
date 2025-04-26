import type { Category } from "./";
export interface Lancamento {
    id?: number;
    descricao?: string;
    valor?: string;
    tipo?: "Não recorrente" | "Parcelada" | "Fixa mensal";
    numParcelas?: number;
    periodicidade?: "Mensal" | "Diario" | "Semanal" | "Quinzenal" | "Trimenstral" | "Anual";
    dataVencimento?: string;
    status?: "Efetivada" | "Pendente";
    categoria?: string;
    subcategoria?: string;
    conta?: string;
    dataLancamento?: string;
    dataEfetivacao?: string;
    mesReferencia?: string;
  }

  export interface TransactionsData {
    byCategory: [];
    valuePay: number;
    valuePending: number;
    valueTotalMonth: number;
    byMonth: [];
    categories: Category[];
    totalDays: number;
  }
  
  // Represents transactions grouped by month
  export interface LancamentosPorMes {
    [mesAno: string]: Lancamento[];
  }
  
  // Data for monthly financial summaries
  export interface MonthData {
    month: string; // e.g., "2025-04"
    total: number;
  }
  
  // Data for financial summaries by category
  export interface CategoryData {
    categoryId: number;
    name: string;
    total: number;
  }