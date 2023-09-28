import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';

export const useExpensesStore = defineStore("expenses", () => {
    //state
    let valueTotalExpensesMonth = ref();
    const valuePayExpenses = ref();
    const valuePendingExpenses = ref();
    const expensesMonth = reactive();
    const expenses = reactive();


    //getters


    //actions
    function setValueTotalExpensesMonth(value) {
        valueTotalExpensesMonth.value = value;
    }

    function setValuePayExpenses(value) {
        valuePayExpenses.value = value;
    }
    /////////////////////////////////////
    function setValuePendingExpenses(value) {
        valuePendingExpenses.value = value;
    }
    /////////////////////////////////////
    function setExpenses(data) {
        expenses = data
    }
    /////////////////////////////////////
    function setExpensesMonth(data) {
        expensesMonth = data;
    }


    return {
        setValueTotalExpensesMonth,
        setValuePayExpenses,
        setValuePendingExpenses,
        setExpenses,
        setExpensesMonth,
    }
})