// src/composables/useNotification.ts
import { ref } from 'vue'

/**
 * Composable para notificações Toast usando Vuetify Snackbar
 */
export function useNotification() {
  const snackbar = ref(false)
  const message = ref('')
  const type = ref<'success' | 'error' | 'warning' | 'info'>('info')
  const timeout = ref(3000)

  const notification = {
    success: (options: { title?: string; message: string; duration?: number }) => {
      message.value = options.message
      type.value = 'success'
      timeout.value = options.duration || 3000
      snackbar.value = true
    },
    error: (options: { title?: string; message: string; duration?: number }) => {
      message.value = options.message
      type.value = 'error'
      timeout.value = options.duration || 3000
      snackbar.value = true
    },
    warning: (options: { title?: string; message: string; duration?: number }) => {
      message.value = options.message
      type.value = 'warning'
      timeout.value = options.duration || 3000
      snackbar.value = true
    },
    info: (options: { title?: string; message: string; duration?: number }) => {
      message.value = options.message
      type.value = 'info'
      timeout.value = options.duration || 3000
      snackbar.value = true
    }
  }

  return {
    notification,
    snackbar,
    message,
    type,
    timeout
  }
}
