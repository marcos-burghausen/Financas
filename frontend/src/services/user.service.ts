// src/services/user.service.ts
import http from './http'

export interface UserProfile {
  id: number
  name: string
  email: string
  type: string
  phone?: string
  cpf?: string
  date_of_birth?: string
  profession?: string
  bio?: string
}

export interface UpdateProfileRequest {
  name: string
  email: string
  phone?: string
  cpf?: string
  date_of_birth?: string
  profession?: string
  bio?: string
}

export interface UpdatePasswordRequest {
  current_password: string
  password: string
  password_confirmation: string
}

class UserService {
  /**
   * Obter perfil do usuário
   */
  async getProfile(): Promise<UserProfile> {
    try {
      const response = await http.get<UserProfile>('/user')
      return response.data
    } catch (error) {
      throw error
    }
  }

  /**
   * Atualizar perfil
   */
  async updateProfile(data: UpdateProfileRequest): Promise<UserProfile> {
    try {
      const response = await http.put<UserProfile>('/user/profile', data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  /**
   * Atualizar senha
   */
  async updatePassword(data: UpdatePasswordRequest): Promise<{ success: string }> {
    try {
      const response = await http.put<{ success: string }>('/user/password', data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  /**
   * Obter estatísticas do usuário
   */
  async getStats(): Promise<any> {
    try {
      const response = await http.get('/user/stats')
      return response.data
    } catch (error) {
      throw error
    }
  }
}

export default new UserService()
