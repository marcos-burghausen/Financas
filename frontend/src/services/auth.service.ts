// src/services/auth.service.ts
import http from './http'

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  name: string
  email: string
  password: string
  password_confirmation: string
  type: string
}

export interface AuthResponse {
  success: string
  token: string
  user: {
    id: number
    name: string
    email: string
    type: string
  }
  summary?: {
    saldoAtual: number
    saldoInicial: number
    totalReceitas: number
    totalDespesas: number
  }
  mesAno?: string
}

export interface ErrorResponse {
  error: string
  message?: string
  validation_errors?: Record<string, string[]>
  error_code?: string
}

class AuthService {
  /**
   * Registrar novo usuário
   */
  async register(data: RegisterRequest): Promise<AuthResponse> {
    try {
      const response = await http.post<any>('/create', {
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation,
        type: data.type
      })
      
      // A resposta pode vir em diferentes formatos
      // Normalizar para o formato esperado
      const responseData = response.data || response
      
      // Se não houver token, usar um token vazio (será preenchido posteriormentepor Sanctum)
      // ou tentar extrair do header
      const token = responseData.token || ''
      
      // Montar resposta no formato esperado
      const normalizedResponse: AuthResponse = {
        success: responseData.success || 'Usuário criado com sucesso',
        token: token,
        user: responseData.user || {
          id: responseData.id,
          name: data.name,
          email: data.email,
          type: data.type
        },
        summary: responseData.summary,
        mesAno: responseData.mesAno
      }
      
      return normalizedResponse
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Login com email e senha (Sanctum)
   */
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    try {
      const response = await http.post<any>('/login', {
        email: credentials.email,
        password: credentials.password
      })
      
      // A resposta pode vir em diferentes formatos
      // Normalizar para o formato esperado
      const responseData = response.data || response
      
      // Se não houver token, usar um token vazio
      const token = responseData.token || ''
      
      // Montar resposta no formato esperado
      const normalizedResponse: AuthResponse = {
        success: responseData.success || 'Login realizado com sucesso',
        token: token,
        user: responseData.user || {
          id: responseData.id,
          name: responseData.name || credentials.email.split('@')[0],
          email: credentials.email,
          type: responseData.type || 'USER'
        },
        summary: responseData.summary,
        mesAno: responseData.mesAno
      }
      
      return normalizedResponse
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Logout
   */
  async logout(): Promise<void> {
    try {
      await http.post('/sanctum/logout')
    } catch (error) {
      console.error('Erro ao fazer logout:', error)
      throw error
    }
  }

  /**
   * Obter dados do usuário autenticado
   */
  async getMe(): Promise<any> {
    try {
      const response = await http.post('/sanctum/me')
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  }

  /**
   * Tratamento de erros padronizado
   */
  private handleError(error: any): ErrorResponse {
    if (error.response?.data) {
      const data = error.response.data
      return {
        error: data.error || data.message || 'Erro na requisição',
        message: data.message,
        validation_errors: data.validation_errors,
        error_code: data.error_code // Incluir error_code se presente
      } as any
    }

    return {
      error: error.message || 'Erro na conexão com o servidor'
    }
  }
}

export default new AuthService()
