import { defineStore } from "pinia";
import { ref } from "vue";

import type { User } from "@/types";

export const useUserStore = defineStore("user", () => {
    // state
    const userData = ref<User>(
        localStorage.getItem("userData")
            ? JSON.parse(localStorage.getItem("userData") as string)
            : "");
    const mesAno = ref(
        localStorage.getItem("mesAno")
    );

    // getters
    // const getCarteias = () => {
    //     return userData.value.carteiras;
    // };

    // actions
    function setUserData(data: User): void {
        userData.value = data;
        localStorage.setItem("userData", JSON.stringify(userData.value));
    }

    // function setCategoriasDespesas(categoriasDespesas: Category) {
    //     userData.value.categoriasDespesas = categoriasDespesas;
    //     localStorage.setItem("userData", JSON.stringify(userData.value));
    // }

    // function setCategoriasReceitas(categoriasReceitas: Category) {
    //     userData.value.categoriasReceitas = categoriasReceitas;
    //     localStorage.setItem("userData", JSON.stringify(userData.value));
    // }

    function setMesAno(mes_ano: string) {
        console.log(mes_ano);
        mesAno.value = mes_ano;
        localStorage.setItem("mesAno", JSON.stringify(mesAno.value));
        
    }


    return {
        userData,
        // getCarteias,
        setUserData,
        setMesAno,
        // setCategoriasDespesas,
        // setCategoriasReceitas,
    };
});