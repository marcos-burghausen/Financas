import { ref, computed } from "vue";
import { defineStore } from "pinia";
import { useRouter } from "vue-router";


export const useAuth = defineStore("auth", () => {
    const router = useRouter();
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
        localStorage.removeItem("userName");
        token.value = "";
    }

    function expiredTokem() {
        router.push({ name: "home" });
        clear();
        console.log("object");
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
