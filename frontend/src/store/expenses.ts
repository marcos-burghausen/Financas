import { defineStore } from "pinia";
import { ref } from "vue";
import { TransactionsData } from "../types";


export const useExpensesStore = defineStore("expenses", () => {

    const expensesData = ref<TransactionsData>(
        localStorage.getItem("expensesData")
            ? JSON.parse(localStorage.getItem("expensesData") as string)
            : "");


    function setExpensesData(expenses: TransactionsData): void {
        expensesData.value = {
            byCategory: expenses.byCategory || [],
            valuePay: expenses.valuePay || 0,
            valuePending: expenses.valuePending || 0,
            valueTotalMonth: expenses.valueTotalMonth || 0,
            byMonth: expenses.byMonth || [],
            categories: expenses.categories || [],
            totalDays: expenses.totalDays || 0,
        };
        localStorage.setItem("expensesData", JSON.stringify(expensesData.value));
    }

    return {
        expensesData,
        setExpensesData,
    };
});