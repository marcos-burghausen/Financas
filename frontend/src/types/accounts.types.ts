
export interface Wallet {
  id?: number | null;
  user_id: number;
  name: string;
  icon: string;
  saldo?: string;
  saldo_inicial?: string;
  incluir_em_soma_inicial: boolean;
  descricao?: string;
  tipo_conta: "Carteira" | "Conta Corrente" | "Poupança" | "Investimento" | "Outro" | "Cartão de Crédito";
  status_conta: "Ativo" | "Inativo";
  color: string;
  limite: string;
  dia_fechamento?: number | null;
  dia_vencimento?: number | null;
  conta_id: number | null;
  conta_pai_id?: number | null;
  bandeira?: string;
  updatedAt?: string;
}

// Represents wallet-related data (replacing Wallets)
export interface WalletData {
  categories: CategoryAccount[];
  contas: Wallet[];
  cartoes: Wallet[];
  contas_names: string[];
  saldo_atual: number;
  saldo_inicial: number;
}

export interface CategoryAccount {
  color: string;
  editable: boolean;
  icon: string;
  id: number;
  name: string;
  type: string
}