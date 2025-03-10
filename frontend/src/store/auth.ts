import http from "@/services/http";
import type { Token } from "@/types/token";
import type { User } from "@/types/user";
import { defineStore } from "pinia";
import type { Ref } from "vue";
import { computed, onMounted, ref, watchEffect } from "vue";


export const useAuthStore = defineStore("auth", () => {
    const token = ref({ token: "", expires: 0 });
            
    const user: Ref<User | null> = ref(null);

    onMounted(() => {
        const storedToken = localStorage.getItem("token");
        if (storedToken) {
            token.value = JSON.parse(storedToken);
        }

        const storedUser = localStorage.getItem("user");
        if (storedUser) {
            user.value = JSON.parse(storedUser);
        }
    });

    function setToken(tokenValue: Token) {
        localStorage.setItem("token", JSON.stringify(tokenValue));
        token.value = tokenValue;
    }


    function setUser(userValue: User) {
        localStorage.setItem("user", JSON.stringify(userValue));
        user.value = userValue;
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
        localStorage.removeItem("user");
        localStorage.removeItem("walletsData");
        token.value = { token: "", expires: 0 };
        user.value = null;
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
        console.log("verificando");
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
        user,
        setToken,
        setUser,
        isAuthenticated,
        clear,
        expiredTokem,
        refreshToken,
    };

});
