import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore(
  'theme',
  () => {
    // State
    const isDark = ref<boolean>(true) // Default dark theme
    const themeName = ref<'light' | 'dark'>('dark')

    // Actions
    const toggleTheme = () => {
      isDark.value = !isDark.value
      themeName.value = isDark.value ? 'dark' : 'light'
    }

    const setTheme = (theme: 'light' | 'dark') => {
      themeName.value = theme
      isDark.value = theme === 'dark'
    }

    const setDarkTheme = () => {
      setTheme('dark')
    }

    const setLightTheme = () => {
      setTheme('light')
    }

    return {
      // State
      isDark,
      themeName,
      
      // Actions
      toggleTheme,
      setTheme,
      setDarkTheme,
      setLightTheme,
    }
  },
  {
    persist: {
      key: 'financas-theme',
      storage: localStorage,
    }
  }
)
