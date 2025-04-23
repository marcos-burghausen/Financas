// src/types/transactions.types.ts
// Represents a financial transaction
export interface Lancamento {
    id?: number;
    descricao?: string;
    valor?: number; // Changed to number for consistency
    tipo?: "despesa" | "receita";
    numParcelas?: number;
    periodicidade?: string;
    data?: string;
    status?: string;
    categoria?: string;
    carteira?: string;
    subCategoria?: string;
    conta?: string;
    mesReferencia?: string;
    dateLancamento?: string;
    dateEfetivacao?: string;
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