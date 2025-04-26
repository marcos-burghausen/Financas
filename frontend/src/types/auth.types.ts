// src/types/auth.types.ts
// Defines the structure of the login API response
import type { Account, Category, CategoryData, MonthData, WalletData } from "./index";

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
    user: User;
    
    data: {
      expenses: {
        valuePay: number;
        valuePending: number;
        valueTotalMonth: number;
        byMonth: MonthData[];
        totalDay: number;
        byCategory: CategoryData[];
      };
      revenues: {
        valueReceived: number;
        valuePending: number;
        valueTotalMonth: number;
        byMonth: MonthData[];
      };
      categories: {
        expenses: Category[];
        revenues: Category[];
        wallets: Category[];
      };
      wallets: WalletData
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
export interface UserDAta {
    id: number;
    name: string;
    email: string;
    userType: string | null; // Renamed from user_tipe
    createdAt: string;
    updatedAt: string;
    carteiras?: Account[]; // Specific type instead of []
    categoriasDespesas?: Category[];
    categoriasReceitas?: Category[];
  }