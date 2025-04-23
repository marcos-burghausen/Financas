import { defineStore } from "pinia";
import { ref } from "vue";

import type { TransactionsData } from "../types";

export const useRevenuesStore = defineStore("revenues", () => {

    const revenuesData = ref<TransactionsData>(
        localStorage.getItem("revenuesData")
            ? JSON.parse(localStorage.getItem("revenuesData") as string)
            : "");

    function setRevenuesData(revenues: TransactionsData): void {
        revenuesData.value = {
            byCategory: revenues.byCategory || [],
            valuePay: revenues.valuePay || 0,
            valuePending: revenues.valuePending || 0,
            valueTotalMonth: revenues.valueTotalMonth || 0,
            byMonth: revenues.byMonth || [],
            categories: revenues.categories || [],
            totalDays: revenues.totalDays || 0,
        };
        localStorage.setItem("revenuesData", JSON.stringify(revenuesData.value));
    }

    return {
        revenuesData,
        setRevenuesData,
    };
});