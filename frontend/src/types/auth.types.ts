// src/types/auth.types.ts
// Defines the structure of the login API response
import type { CategoryData, Lancamento, WalletData } from "./index";

export interface Token {
  expires: number;
  expiresIn: number;
  iat: number;
  token: string;
  tokenType: string;
}

export interface User {
  email: string;
  id: number;
  name: string;
  type: string;
}

export interface LoginResponse {
  expenses: {
    byCategory: Lancamento[];
    byMonth: Lancamento[];
    categories: CategoryData[];
    totalDay: number;
    valuePay: number;
    valuePending: number;
    valueTotalMonth: number;
  };
  mesAno: string;
  revenues: {
    byCategory: Lancamento[];
    byMonth: Lancamento[];
    categories: CategoryData[];
    totalDay: number;
    valuePay: number;
    valuePending: number;
    valueTotalMonth: number;
  };
  token: Token;
  userData: User;
  wallets: WalletData;
}


// Form for login submission
export interface FormLogin {
  email: string;
  password: string;
}

// Form for user registration
export interface FormCadastro {
  name?: string;
  email?: string;
  password?: string;
  confirmPassword?: string;
}

// User data (replacing User and UserDAta)
// export interface UserDAta {
//     id: number;
//     name: string;
//     email: string;
//     userType: string | null; // Renamed from user_tipe
//     createdAt: string;
//     updatedAt: string;
//     carteiras?: Account[]; // Specific type instead of []
//     categoriasDespesas?: Category[];
//     categoriasReceitas?: Category[];
//   }