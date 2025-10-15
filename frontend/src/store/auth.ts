import http from "@/services/http";
import { Token } from "@/types";
import { defineStore } from "pinia";
import { computed, ref, watchEffect } from "vue";

export const useAuthStore = defineStore("auth", () => {
    const token = ref<Token>({
        token: "",
        tokenType: "",
        expiresIn: 0,
        iat: 0,
        expires: 0,
    });

    function setToken(tokenValue: Token) {
        console.log('=== SETTOKEN DEBUG ===');
        console.log('tokenValue recebido:', tokenValue);
        console.log('Tipo de tokenValue:', typeof tokenValue);
        console.log('tokenValue stringificado:', JSON.stringify(tokenValue));
        
        localStorage.setItem("token", JSON.stringify(tokenValue));
        token.value = tokenValue;
        
        console.log('Verificando o que foi salvo no localStorage:', localStorage.getItem('token'));
        console.log('token.value atualizado:', token.value);
        console.log('=== FIM SETTOKEN DEBUG ===');
    }

    function loadFromSession() {
        const storedToken = localStorage.getItem("token");
        if (storedToken) {
            try {
                token.value = JSON.parse(storedToken);
            } catch {
                console.warn("Erro ao carregar token do localStorage");
            }
        }
    }

    const isAuthenticated = computed(() => {
        if (!token.value.token || !token.value.expires) {
            return false;
        }
        // Convertendo o timestamp de expiração para milissegundos
        const expirationTime = token.value.expires * 1000;
        const currentTime = Date.now();

        if (currentTime >= expirationTime) {
            clear();
            return false;
        }
        return true;
    });

    function clear() {
        localStorage.removeItem("revenuesData");
        localStorage.removeItem("expensesData");
        localStorage.removeItem("token");
        localStorage.removeItem("userData");
        localStorage.removeItem("walletsData");
        localStorage.removeItem("mesAno");
        localStorage.removeItem("dashboardSummary");
        token.value = {
            token: "",
            tokenType: "",
            expiresIn: 0,
            iat: 0,
            expires: 0,
        };
    }

    function expiredToken() {
        clear();
        setTimeout(() => {
            window.location.href = "/";
        }, 100);
    }

    async function refreshToken() {
        try {
            const response = await http.post("/refresh-token");

            if (response.status === 200) {
                setToken(response.data);
            } else {
                console.error("Erro ao renovar o token", response);
                clear();
            }
        } catch (error) {
            console.error("Erro ao chamar a API de renovação de token", error);
            clear();
        }
    }

    // Função para iniciar o monitoramento da expiração do token
    let monitorInterval: NodeJS.Timeout | null = null;
    
    function monitorTokenExpiration() {
        // Limpa intervalo anterior se existir
        if (monitorInterval) {
            clearInterval(monitorInterval);
        }

        monitorInterval = setInterval(() => {
            if (!token.value.expires) return;

            const expirationTime = token.value.expires * 1000;
            const currentTime = Date.now();
            const timeRemaining = expirationTime - currentTime;

            // Renovar o token 1 minuto antes de expirar
            if (timeRemaining <= 60000 && timeRemaining > 0) {
                refreshToken();
            }
        }, 5000); // Verifica a cada 5 segundos
    }

    // Inicia o monitoramento quando o store for carregado
    watchEffect(() => {
        if (isAuthenticated.value) {
            monitorTokenExpiration();
        } else if (monitorInterval) {
            clearInterval(monitorInterval);
            monitorInterval = null;
        }
    });

    return {
        token,
        setToken,
        loadFromSession,
        isAuthenticated,
        clear,
        expiredToken,
        refreshToken,
    };
});
