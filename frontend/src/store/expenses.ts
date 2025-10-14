import { defineStore } from "pinia";
import { ref } from "vue";

import type { TransactionsData } from "../types";

export const useExpensesStore = defineStore("expenses", () => {
    const getDefaultTransactionsData = (): TransactionsData => ({
        byCategory: [],
        valuePay: 0,
        valuePending: 0,
        valueTotalMonth: 0,
        byMonth: [],
        categories: [],
        totalDays: 0,
    });

    const expensesData = ref<TransactionsData>(getDefaultTransactionsData());

    function setExpensesData(expenses: TransactionsData): void {
        expensesData.value = {
            byCategory: expenses?.byCategory ?? [],
            valuePay: expenses?.valuePay ?? 0,
            valuePending: expenses?.valuePending ?? 0,
            valueTotalMonth: expenses?.valueTotalMonth ?? 0,
            byMonth: expenses?.byMonth ?? [],
            categories: expenses?.categories ?? [],
            totalDays: expenses?.totalDays ?? 0,
        };
        sessionStorage.setItem("expensesData", JSON.stringify(expensesData.value));
    }

    function loadFromSession(): void {
        const stored = sessionStorage.getItem("expensesData");
        if (stored) {
            try {
                expensesData.value = JSON.parse(stored);
            } catch {
                console.warn("Erro ao carregar despesas");
            }
        }
    }

    function clear(): void {
        expensesData.value = getDefaultTransactionsData();
        sessionStorage.removeItem("expensesData");
    }

    return {
        expensesData,
        setExpensesData,
        loadFromSession,
        clear,
    };
});