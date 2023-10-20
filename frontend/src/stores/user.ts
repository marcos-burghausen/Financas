import { defineStore } from "pinia";
import { ref } from "vue";

import type { UserDAta } from "@/types/userData";

export const useUserStore = defineStore("user", () => {
    // state
    const user = ref(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user") as string)
            : "");

    // getters
    const getCarteias = () => {
        return user.carteiras;
    }

    // actions
    function setUserData(data: UserDAta): void {
        user.value = {
            data
        };
        localStorage.setItem('user', JSON.stringify(user.value));
    }


    return {
        user,
        getCarteias,
        setUserData,
    };
});