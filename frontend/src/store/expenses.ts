import { defineStore } from "pinia";
import { ref } from "vue";

import type { TransactionsData } from "../types";


export const useExpensesStore = defineStore("expenses", () => {

    const expensesData = ref<TransactionsData>(
        localStorage.getItem("expensesData")
            ? JSON.parse(localStorage.getItem("expensesData") as string)
            : "");


    function setExpensesData(expenses: TransactionsData): void {
        expensesData.value = {
            byCategory: expenses.byCategory ?? expensesData.value.byCategory,
            valuePay: expenses.valuePay ?? expensesData.value.valuePay,
            valuePending: expenses.valuePending ?? expensesData.value.valuePending,
            valueTotalMonth: expenses.valueTotalMonth ?? expensesData.value.valueTotalMonth,
            byMonth: expenses.byMonth ?? expensesData.value.byMonth,
            categories: expenses.categories ?? expensesData.value.categories,
            totalDays: expenses.totalDays ?? expensesData.value.totalDays,
        };
        localStorage.setItem("expensesData", JSON.stringify(expensesData.value));
    }
    

    return {
        expensesData,
        setExpensesData,
    };
});