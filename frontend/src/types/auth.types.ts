// src/types/auth.types.ts
// Defines the structure of the login API response

// REMOVIDO: Interface Token não é mais necessária com Sanctum
// JWT Token structure (deprecated - migrado para Sanctum)
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

export interface DashboardSummary {
  saldoAtual: number;
  saldoInicial: number;
  totalReceitas: number;
  totalDespesas: number;
}

// Sanctum: token é string simples, não objeto
export interface LoginResponse {
  token: string; // Sanctum retorna string, não objeto Token
  user: User;
  mesAno: string;
  summary: DashboardSummary;
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