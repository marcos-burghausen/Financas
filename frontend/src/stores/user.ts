import { defineStore } from "pinia";
import { ref } from "vue";

import type { UserDAta } from "@/types/userData";
import type { Category } from "@/types/category";

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
        user.value = data;
        localStorage.setItem('user', JSON.stringify(user.value));
    }

    function setCategoriasDespesas(categoriasDespesas: Category) {
        user.value.categoriasDespesas = categoriasDespesas;
        localStorage.setItem('user', JSON.stringify(user.value));
    }

    function setCategoriasReceitas(categoriasReceitas: Category) {
        user.value.categoriasReceitas = categoriasReceitas;
        localStorage.setItem('user', JSON.stringify(user.value));
    }


    return {
        user,
        getCarteias,
        setUserData,
        setCategoriasDespesas,
        setCategoriasReceitas,
    };
});