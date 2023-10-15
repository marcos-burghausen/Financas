import { defineStore } from "pinia";
import { ref, type Ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";

export const useExpensesStore = defineStore("expenses", () => {

    const expensesData = ref(
        localStorage.getItem("expensesData")
            ? JSON.parse(localStorage.getItem("expensesData") as string)
            : "");

    // function setExpensesData(expenses: Array<Lancamentos>, valueTotalExpensesMonth: number, valuePayExpenses: number, valuePendingExpenses: number, expensesMonth: Array<Lancamentos>): void {
    //     expensesData.value = {
    //         valueTotalExpensesMonth,
    //         valuePendingExpenses,
    //         valuePayExpenses,
    //         expensesMonth,
    //         expenses
    //     };
    //     localStorage.setItem('expensesData', JSON.stringify(expensesData.value));
    // }

    function setExpensesData(expenses: Array<Lancamentos>): void {
        expensesData.value = {
            expenses
        };
        localStorage.setItem('expensesData', JSON.stringify(expensesData.value));
    }

    return {
        expensesData,
        setExpensesData,
    };
});