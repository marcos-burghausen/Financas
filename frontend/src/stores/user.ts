import { defineStore } from "pinia";
import { reactive } from "vue";

import type { UserDAta } from "@/types/userData";

export const useUserStore = defineStore("user", () => {
    // state
    const user = reactive({
        id: 0 as number,
        name: "" as string,
        email: "" as string,
        carteiras: [],
        categoriasDespesas: [],
        categoriasReceitas: [],
    });

    // getters
    const getCarteias = () => {
        return user.carteiras;
    }

    // actions
    function setUserData(value: UserDAta) {
        user.id = value.id;
        user.name = value.name;
        user.email = value.email;
        user.carteiras = value.carteiras;
        user.categoriasDespesas = value.categoriasDespesas;
        user.categoriasReceitas = value.categoriasReceitas;
    }


    return {
        user,
        getCarteias,
        setUserData,
    };
});