import http from "@/services/http";
import { Token } from "@/types";
import { defineStore } from "pinia";
import { computed, onMounted, ref, watchEffect } from "vue";


export const useAuthStore = defineStore("auth", () => {
    const token = ref<Token>({
        token: "",
        token_type: "",
        expires_in: 0,
        iat: 0,
        expires: 0,
    });
            

    onMounted(() => {
        const storedToken = localStorage.getItem("token");
        if (storedToken) {
            token.value = JSON.parse(storedToken);
        }

    });

    function setToken(tokenValue: Token) {
        localStorage.setItem("token", JSON.stringify(tokenValue));
        token.value = tokenValue;
    }

    const isAuthenticated = computed(() => {
        if (!token.value.token || !token.value.expires) {
            return false;
        }
        // Convertendo o timestamp de expiração para milissegundos
        // pois o Date.now() retorna o tempo em milessegundos
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
        token.value = {
            token: "",
            token_type: "",
            expires_in: 0,
            iat: 0,
            expires: 0,
        };
    }

    function expiredTokem() {
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
    function monitorTokenExpiration() {
        setInterval(() => {
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
        }
    });

    return {
        token,
        setToken,
        isAuthenticated,
        clear,
        expiredTokem,
        refreshToken,
    };

});
