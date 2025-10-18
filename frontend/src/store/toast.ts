import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export interface Toast {
  id: string
  message: string
  color: 'success' | 'error' | 'warning' | 'info'
  icon?: string
  timeout?: number
  position?: 'top' | 'bottom'
  show?: boolean
}

// Generate unique ID
const generateId = (): string => {
  return `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref<Toast[]>([])
  const maxToasts = ref(5)

  // Add toast
  const addToast = (toast: Omit<Toast, 'id' | 'show'>) => {
    const id = generateId()
    const timeout = toast.timeout ?? 4000
    const position = toast.position ?? 'top'
    
    const newToast: Toast = {
      ...toast,
      id,
      timeout,
      position,
      show: true
    }

    // Remove oldest if exceeds max
    if (toasts.value.length >= maxToasts.value) {
      toasts.value.shift()
    }

    toasts.value.push(newToast)

    // Auto-remove after timeout
    if (timeout > 0) {
      setTimeout(() => {
        removeToast(id)
      }, timeout)
    }

    return id
  }

  // Remove toast
  const removeToast = (id: string) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  // Clear all
  const clearAll = () => {
    toasts.value = []
  }

  // Success toast
  const success = (message: string, timeout?: number) => {
    return addToast({
      message,
      color: 'success',
      icon: 'mdi-check-circle',
      timeout
    })
  }

  // Error toast
  const error = (message: string, timeout?: number) => {
    return addToast({
      message,
      color: 'error',
      icon: 'mdi-alert-circle',
      timeout
    })
  }

  // Warning toast
  const warning = (message: string, timeout?: number) => {
    return addToast({
      message,
      color: 'warning',
      icon: 'mdi-alert',
      timeout
    })
  }

  // Info toast
  const info = (message: string, timeout?: number) => {
    return addToast({
      message,
      color: 'info',
      icon: 'mdi-information',
      timeout
    })
  }

  // Top toasts
  const topToasts = computed(() => 
    toasts.value.filter(t => t.position === 'top')
  )

  // Bottom toasts
  const bottomToasts = computed(() => 
    toasts.value.filter(t => t.position === 'bottom')
  )

  return {
    toasts,
    topToasts,
    bottomToasts,
    maxToasts,
    addToast,
    removeToast,
    clearAll,
    success,
    error,
    warning,
    info
  }
})
