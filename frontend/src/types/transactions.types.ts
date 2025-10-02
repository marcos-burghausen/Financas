import type { CategoryData } from "./";
export interface Lancamento {
  id: number | null;
  user_id?: number | null;
  invoice_id: number | null;
  descricao?: string;
  valor: string;
  tipo_lancamento: 'Receita' | 'Despesa' | 'Cartão de Crédito';
  is_estorno: boolean;
  original_lancamento_id: number | null;
  recorrencia?: "Não recorrente" | "Fixa" | "Parcelado";
  num_parcelas: number | null;
  parcela_atual: number | null;
  data_vencimento?: string | Date;
  status_lancamento: 'Efetivada' | 'Pendente'; 
  tipo_parcela: "total" | "parcela" | null;
  categoria?: string;
  subcategoria?: string;
  data_lancamento: string; // "YYYY-MM-DD"
  data_efetivacao: string | null;
  periodicidade: "Mensal" | "Diario" | "Semanal" | "Quinzenal" | "Trimenstral" | "Anual" | null;
  conta_id?: number | null;
  fatura?: string; // "YYYY-MM"
}

export interface CreditCardInvoice {
  id: number;
  conta_id: number;
  competencia: string; // "YYYY-MM"
  data_fechamento: string;
  data_vencimento: string;
  status: 'Aberta' | 'Fechada' | 'Paga';
  paid_at: string | null;
  payment_lancamento_id: number | null;
  lancamentos: Lancamento[]; // Array de lançamentos da fatura
}

export interface TransactionsData {
  byCategory: Lancamento[];
  valuePay: number;
  valuePending: number;
  valueTotalMonth: number;
  byMonth: Lancamento[] | [];
  categories: CategoryData[];
  totalDays?: number;
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
export interface Category {
  categoryId: number;
  name: string;
  total: number;
}