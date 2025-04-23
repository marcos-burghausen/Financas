// src/types/auth.types.ts
// Defines the structure of the login API response
import type { Account, Category, CategoryData, MonthData } from "./index";
export interface LoginResponse {
    token: {
      token: string;
      token_type: string;
      expires_in: number;
      iat: number;
      expires: number;
    };
    user: {
      id: number;
      name: string;
      email: string;
      type: string;
    };
    data: {
      expenses: {
        ValuePay: number;
        ValuePending: number;
        ValueTotalMonth: number;
        byMonth: MonthData[];
        totalDay: number;
        ByCategory: CategoryData[];
      };
      revenues: {
        ValueReceived: number;
        ValuePending: number;
        ValueTotalMonth: number;
        byMonth: MonthData[];
      };
      categories: {
        expenses: Category[];
        revenues: Category[];
        wallets: Category[];
      };
      wallets: {
        mes_ano_referencia: string;
        contas: Account[];
        contasNames: string[];
        saldoInicial: number;
      };
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
  export interface User {
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