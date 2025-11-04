// Tipos para o sistema de orçamentos
export interface BudgetTransaction {
  id?: number;
  descricao: string;
  valor: number; // em centavos
  data: string; // YYYY-MM-DD
  categoria: string;
}

export interface Budget {
  id?: number;
  categoria: string;
  orcado: number; // em centavos
  gasto: number; // em centavos
  restante: number; // em centavos
  percentual: number;
  icon: string;
  color: string;
  observacao?: string;
  mes_ano: string; // YYYY-MM
  transacoes?: BudgetTransaction[];
}

export interface BudgetData {
  budgets: Budget[];
  totalOrcamento: number;
  totalGasto: number;
  saldoRestante: number;
  percentualGasto: number;
  metaEconomia: number;
}

export interface BudgetFormData {
  categoria: string;
  valor: string; // string formatada para input (ex: "100,00")
  observacao: string;
}

export interface BudgetSummary {
  totalOrcamento: number;
  totalGasto: number;
  saldoRestante: number;
  percentualGasto: number;
  metaEconomia: number;
}

// Enums úteis
export enum BudgetStatus {
  NORMAL = 'normal',
  WARNING = 'warning',
  EXCEEDED = 'exceeded'
}

export enum BudgetColor {
  SUCCESS = 'success',
  WARNING = 'warning',
  ERROR = 'error',
  INFO = 'info',
  PRIMARY = 'primary'
}

// Utilitários de tipo
export type BudgetCategory = 
  | 'Alimentação'
  | 'Transporte' 
  | 'Saúde'
  | 'Educação'
  | 'Lazer'
  | 'Moradia'
  | 'Vestuário'
  | 'Utilidades'
  | 'Investimentos'
  | 'Outros';

export interface CategoryIcon {
  [key: string]: string;
}