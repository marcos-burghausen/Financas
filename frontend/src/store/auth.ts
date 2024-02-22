import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useAuth = defineStore("auth", () => {
    const token = ref(localStorage.getItem("token"));
    const userName = ref(
        localStorage.getItem("userName")
            ? JSON.parse(localStorage.getItem("user") as string)
            : "");

    function setToken(tokenValue: string) {
        localStorage.setItem("token", tokenValue);
        token.value = tokenValue;
    }


    function setUser(userValue: string) {
        localStorage.setItem("userName", JSON.stringify(userValue));
        userName.value = userValue;
    }

    const isAuthenticated = computed(() => {
        return token.value && userName.value;
    });

    function clear() {
        localStorage.removeItem("revenuesData");
        localStorage.removeItem("expensesData");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        localStorage.removeItem("data");
        token.value = "";
        // user.value = '';
    }

    return {
        token,
        // user,
        setToken,
        setUser,
        isAuthenticated,
        clear,
    };

});
