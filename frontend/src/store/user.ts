import { defineStore } from "pinia";
import { ref } from "vue";

import type { DashboardSummary, User } from "@/types";

export const useUserStore = defineStore("user", () => {
    const userData = ref<(User & { summary?: DashboardSummary }) | null>(null);
    const mesAno = ref<string>(new Date().toISOString().slice(0, 7));
    const summary = ref<DashboardSummary | null>(null);

    // actions
    function setUserData(data: (User & { summary?: DashboardSummary }) | null): void {
        userData.value = data;
        if (data?.summary) {
            summary.value = data.summary;
        }
        if (data) {
            localStorage.setItem("userData", JSON.stringify(data));
        } else {
            localStorage.removeItem("userData");
        }
    }

    function setSummary(summaryData: DashboardSummary): void {
        summary.value = summaryData;
    }

    function setMesAno(mes_ano: string) {
        mesAno.value = mes_ano;
        localStorage.setItem("mesAno", mes_ano);
    }

    function getMesAno(): string {
        return mesAno.value;
    }

    function loadFromSession(): void {
        const storedUser = localStorage.getItem("userData");
        const storedMes = localStorage.getItem("mesAno");

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
        localStorage.removeItem("userData");
        localStorage.removeItem("mesAno");
    }

    return {
        userData,
        mesAno,
        summary,
        getMesAno,
        setUserData,
        setSummary,
        setMesAno,
        loadFromSession,
        clear,
    };
});