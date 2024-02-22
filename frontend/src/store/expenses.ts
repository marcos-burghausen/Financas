import { defineStore } from "pinia";
import { ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

export const useExpensesStore = defineStore("expenses", () => {

    const expensesData = ref(
        localStorage.getItem("expensesData")
            ? JSON.parse(localStorage.getItem("expensesData") as string)
            : "");


    function setExpensesData(expenses: Array<Lancamentos>): void {
        expensesData.value = {
            expenses
        };
        localStorage.setItem("expensesData", JSON.stringify(expensesData.value));
    }

    return {
        expensesData,
        setExpensesData,
    };
});