import { defineStore } from "pinia";
import { ref } from "vue";

import type { Category } from "@/types/auth.types";
import type { UserDAta } from "@/types/userData";

export const useUserStore = defineStore("user", () => {
    // state
    const user = ref(
        localStorage.getItem("user")
            ? JSON.parse(localStorage.getItem("user") as string)
            : "");
    const mesAno = ref(
        localStorage.getItem("mesAno")
    );

    // getters
    const getCarteias = () => {
        return user.value.carteiras;
    };

    // actions
    function setUserData(data: UserDAta): void {
        user.value = data;
        localStorage.setItem("user", JSON.stringify(user.value));
    }

    function setCategoriasDespesas(categoriasDespesas: Category) {
        user.value.categoriasDespesas = categoriasDespesas;
        localStorage.setItem("user", JSON.stringify(user.value));
    }

    function setCategoriasReceitas(categoriasReceitas: Category) {
        user.value.categoriasReceitas = categoriasReceitas;
        localStorage.setItem("user", JSON.stringify(user.value));
    }

    function setMesAno(mes_ano: string) {
        console.log(mes_ano);
        mesAno.value = mes_ano;
        localStorage.setItem("mesAno", JSON.stringify(mesAno.value));
        
    }


    return {
        user,
        getCarteias,
        setUserData,
        setMesAno,
        setCategoriasDespesas,
        setCategoriasReceitas,
    };
});