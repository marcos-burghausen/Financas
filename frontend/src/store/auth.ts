import { ref, computed } from "vue";
import { defineStore } from "pinia";
import { useRouter } from "vue-router";


export const useAuthStore = defineStore("auth", () => {
    const router = useRouter();
    const token = ref(
        localStorage.getItem("token")
            ? JSON.parse(localStorage.getItem("token") as string)
            : "");
            
    const userName = ref(
        localStorage.getItem("userName")
            ? JSON.parse(localStorage.getItem("user") as string)
            : "");

    function setToken(tokenValue: string) {
        localStorage.setItem("token", JSON.stringify(tokenValue));
        token.value = tokenValue;
    }


    function setUser(userValue: string) {
        localStorage.setItem("userName", JSON.stringify(userValue));
        userName.value = userValue;
    }

    const isAuthenticated = computed(() => {
        if (!token.value.token || !token.value.expires) {
            return false;
        }
        // Convertendo o timestamp de expiração para milissegundos
        // pois o Date.now() retorna o tempo em milessegundos
        const expirationTime = token.value.expires * 1000;
        const currentTime = Date.now();

        return currentTime < expirationTime;
    });

    function clear() {
        localStorage.removeItem("revenuesData");
        localStorage.removeItem("expensesData");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        localStorage.removeItem("userName");
        localStorage.removeItem("walletsData");
        token.value = "";
    }

    function expiredTokem() {
        router.push({ name: "home" });
        clear();
    }

    return {
        token,
        setToken,
        setUser,
        isAuthenticated,
        clear,
        expiredTokem,
    };

});
