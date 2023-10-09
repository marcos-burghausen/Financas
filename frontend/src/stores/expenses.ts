import { defineStore } from "pinia";
import { ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

export const useExpensesStore = defineStore("expenses", () => {

    const expensesData = ref(JSON.parse(localStorage.getItem("expensesData")));

    function setExpensesData(expenses: Lancamentos, valueTotalExpensesMonth: number, valuePayExpenses: number, valuePendingExpenses: number, expensesMonth: Lancamentos): void {
        expensesData.value = {
            valueTotalExpensesMonth,
            valuePendingExpenses,
            valuePayExpenses,
            expensesMonth,
            expenses
        };
        localStorage.setItem('expensesData', JSON.stringify(expensesData));
    }

    return {
        expensesData,
        setExpensesData,
    };
});