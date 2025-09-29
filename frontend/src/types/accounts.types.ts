// src/types/accounts.types.ts
// Represents a user account (e.g., bank account, wallet)
export interface Account {
  descricao?: string;
  color: string;
  icon: string;
  id?: number;
  incluirEmSomaInicial: boolean;
  name: string;
  saldo?: string;
  limite: string;
  saldoInicial?: string;
  tipoConta: string;
  conta: string;
  bandeira: string;
  dia_fechamento: number | null,
  dia_vencimento: number | null,
  updatedAt?: string;
}

// Represents wallet-related data (replacing Wallets)
export interface WalletData {
  categories: CategoryAccount[];
  contas: Account[];
  cartoes: Account[];
  contasNames: string[];
  saldoAtual: number;
  saldoInicial: number;
}

export interface CategoryAccount {
  color: string;
  editable: boolean;
  icon: string;
  id: number;
  name: string;
  type: string
}