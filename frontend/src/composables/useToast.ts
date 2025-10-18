import { useToastStore } from '@/store/toast'

/**
 * Composable para usar o sistema de Toast
 */
export const useToast = () => {
  const toastStore = useToastStore()

  /**
   * Mostrar toast de sucesso
   */
  const success = (message: string, timeout?: number) => {
    return toastStore.success(message, timeout)
  }

  /**
   * Mostrar toast de erro
   */
  const error = (message: string, timeout?: number) => {
    return toastStore.error(message, timeout)
  }

  /**
   * Mostrar toast de aviso
   */
  const warning = (message: string, timeout?: number) => {
    return toastStore.warning(message, timeout)
  }

  /**
   * Mostrar toast de informação
   */
  const info = (message: string, timeout?: number) => {
    return toastStore.info(message, timeout)
  }

  /**
   * Remover toast específico
   */
  const remove = (id: string) => {
    toastStore.removeToast(id)
  }

  /**
   * Limpar todos os toasts
   */
  const clear = () => {
    toastStore.clearAll()
  }

  return {
    success,
    error,
    warning,
    info,
    remove,
    clear
  }
}
