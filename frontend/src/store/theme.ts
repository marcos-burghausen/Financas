// frontend/src/store/theme.ts
import { defineStore } from "pinia";
import { computed, ref, watch } from "vue";

export const useThemeStore = defineStore("theme", () => {
    // Tenta carregar o tema do sessionStorage, ou usa 'light' como padrão
    const theme = ref(sessionStorage.getItem("theme") || "light");

    // Computed para verificar se está no modo escuro
    const isDark = computed(() => theme.value === "dark");
    
    // Alias para manter compatibilidade
    const themeName = computed(() => theme.value);

    function setTheme(newTheme: string) {
        theme.value = newTheme;
        sessionStorage.setItem("theme", newTheme);
    }

    function toggleTheme() {
        setTheme(theme.value === "light" ? "dark" : "light");
    }

    // Assiste a mudanças na ref 'theme' e salva no sessionStorage
    watch(theme, (newTheme) => {
        sessionStorage.setItem("theme", newTheme);
    });

    return {
        theme,
        themeName,
        isDark,
        setTheme,
        toggleTheme,
    };
});