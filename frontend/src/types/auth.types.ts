// src/types/auth.types.ts
// Defines the structure of the login API response
import type { CategoryData, Lancamento, WalletData } from "./index";

export interface Token {
  token: string;
  tokenType: string;
  expiresIn: number;
  iat: number;
  expires: number;
}

export interface User {
  id: number;
  name: string;
  email: string;
  type: string;
}

export interface LoginResponse {
  token: Token;
  userData: User;

  data: {
    expenses: {
      valuePay: number;
      valuePending: number;
      valueTotalMonth: number;
      byMonth: Lancamento[];
      totalDay: number;
      byCategory: Lancamento[];
      categories: CategoryData[];
    };
    revenues: {
      valuePay: number;
      valuePending: number;
      valueTotalMonth: number;
      byMonth: Lancamento[];
      totalDay: number;
      categories: CategoryData[];
      byCategory: Lancamento[];
    };
    wallets: WalletData;
    mesAno: string;
  };
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