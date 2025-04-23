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
    incluirEmSomaInicial: boolean; // Renamed for camelCase
    createdAt?: string;
    updatedAt?: string;
  }
  
  // Represents wallet-related data (replacing Wallets)
  export interface WalletData {
    saldoInicial: number;
    contas: Account[];
    contasNames: string[];
    mesAnoReferencia: string;
  }