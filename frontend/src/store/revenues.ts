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
            byCategory: revenues?.byCategory ?? revenuesData.value.byCategory,
            valuePay: revenues?.valuePay ?? revenuesData.value.valuePay,
            valuePending: revenues?.valuePending ?? revenuesData.value.valuePending,
            valueTotalMonth: revenues?.valueTotalMonth ?? revenuesData.value.valueTotalMonth,
            byMonth: revenues?.byMonth ?? revenuesData.value.byMonth,
            categories: revenues?.categories ?? revenuesData.value.categories,
            totalDays: revenues?.totalDays ?? revenuesData.value.totalDays,
        };
        localStorage.setItem("revenuesData", JSON.stringify(revenuesData.value));
    }

    return {
        revenuesData,
        setRevenuesData,
    };
});