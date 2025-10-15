import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
    // Sanctum: Token é uma string simples
    const token = ref<string>("");

    function setToken(tokenValue: string) {
        // Limpa QUALQUER token antigo antes de salvar o novo
        localStorage.removeItem("token"); // Remove token JWT antigo
        localStorage.removeItem("sanctum_token"); // Remove token Sanctum antigo
        
        // Salva o novo token
        localStorage.setItem("sanctum_token", tokenValue);
        token.value = tokenValue;
    }

    function loadFromSession() {
        const sanctumToken = localStorage.getItem("sanctum_token");
        
        if (sanctumToken) {
            token.value = sanctumToken;
        }
    }

    const isAuthenticated = computed(() => {
        return !!token.value;
    });

    function clear() {
        localStorage.removeItem("revenuesData");
        localStorage.removeItem("expensesData");
        localStorage.removeItem("sanctum_token");
        localStorage.removeItem("token"); // Remove token JWT antigo também
        localStorage.removeItem("userData");
        localStorage.removeItem("walletsData");
        localStorage.removeItem("mesAno");
        localStorage.removeItem("dashboardSummary");
        token.value = "";
    }

    function expiredToken() {
        clear();
        setTimeout(() => {
            window.location.href = "/";
        }, 100);
    }

    return {
        token,
        setToken,
        loadFromSession,
        isAuthenticated,
        clear,
        expiredToken,
    };
});
