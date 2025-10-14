import { defineStore } from "pinia";
import { ref } from "vue";

import type { TransactionsData } from "../types";

export const useRevenuesStore = defineStore("revenues", () => {
    const getDefaultTransactionsData = (): TransactionsData => ({
        byCategory: [],
        valuePay: 0,
        valuePending: 0,
        valueTotalMonth: 0,
        byMonth: [],
        categories: [],
        totalDays: 0,
    });

    const revenuesData = ref<TransactionsData>(getDefaultTransactionsData());

    function setRevenuesData(revenues: TransactionsData): void {
        revenuesData.value = {
            byCategory: revenues?.byCategory ?? [],
            valuePay: revenues?.valuePay ?? 0,
            valuePending: revenues?.valuePending ?? 0,
            valueTotalMonth: revenues?.valueTotalMonth ?? 0,
            byMonth: revenues?.byMonth ?? [],
            categories: revenues?.categories ?? [],
            totalDays: revenues?.totalDays ?? 0,
        };
        sessionStorage.setItem("revenuesData", JSON.stringify(revenuesData.value));
    }

    function loadFromSession(): void {
        const stored = sessionStorage.getItem("revenuesData");
        if (stored) {
            try {
                revenuesData.value = JSON.parse(stored);
            } catch {
                console.warn("Erro ao carregar receitas");
            }
        }
    }

    function clear(): void {
        revenuesData.value = getDefaultTransactionsData();
        sessionStorage.removeItem("revenuesData");
    }

    return {
        revenuesData,
        setRevenuesData,
        loadFromSession,
        clear,
    };
});