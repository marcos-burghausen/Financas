// src/types/accounts.types.ts
// Represents a user account (e.g., bank account, wallet)
export interface Account {
    id: number;
    name: string;
    icon: string;
    saldo: number;
    saldoInicial: number;
    descricao: string;
    tipo: string;
    incluirEmSomaInicial: boolean;
    updatedAt?: string;
  }
  
  // Represents wallet-related data (replacing Wallets)
  export interface WalletData {
    contas: Account[];
    contasNames: string[];
    saldoInicial: number;
    categories: CategoryAccount[];
  }

  export interface CategoryAccount {
    id: number;
    name: string;
    color: string;
    icon: string;
    edit: boolean;
    type: string
  }