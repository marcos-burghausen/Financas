import { defineStore } from "pinia";
import { ref } from "vue";

import type { User } from "@/types";

export const useUserStore = defineStore("user", () => {
    const userData = ref<User | null>(null);
    const mesAno = ref<string>(new Date().toISOString().slice(0, 7));

    // actions
    function setUserData(data: User | null): void {
        userData.value = data;
        if (data) {
            sessionStorage.setItem("userData", JSON.stringify(data));
        } else {
            sessionStorage.removeItem("userData");
        }
    }

    function setMesAno(mes_ano: string) {
        mesAno.value = mes_ano;
        sessionStorage.setItem("mesAno", mes_ano);
    }

    function getMesAno(): string {
        return mesAno.value;
    }

    function loadFromSession(): void {
        const storedUser = sessionStorage.getItem("userData");
        const storedMes = sessionStorage.getItem("mesAno");

        if (storedUser) {
            try {
                userData.value = JSON.parse(storedUser);
            } catch {
                console.warn("Erro ao carregar dados do usuário");
            }
        }

        if (storedMes) {
            mesAno.value = storedMes;
        }
    }

    function clear(): void {
        userData.value = null;
        mesAno.value = new Date().toISOString().slice(0, 7);
        sessionStorage.removeItem("userData");
        sessionStorage.removeItem("mesAno");
    }

    return {
        userData,
        mesAno,
        getMesAno,
        setUserData,
        setMesAno,
        loadFromSession,
        clear,
    };
});