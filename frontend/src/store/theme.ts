// frontend/src/store/theme.ts
import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    // Tenta carregar o tema do localStorage, ou usa 'light' como padrão
    const theme = ref(localStorage.getItem('theme') || 'light');

    function setTheme(newTheme: string) {
        theme.value = newTheme;
    }

    function toggleTheme() {
        setTheme(theme.value === 'light' ? 'dark' : 'light');
    }

    // Assiste a mudanças na ref 'theme' e salva no localStorage
    watch(theme, (newTheme) => {
        localStorage.setItem('theme', newTheme);
    });

    return {
        theme,
        setTheme,
        toggleTheme,
    };
});