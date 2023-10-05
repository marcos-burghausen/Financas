import { defineStore } from "pinia";
import { computed, reactive, ref } from "vue";

import type { Lancamentos } from "@/types/lancamentos";
import type { ComputedRef, Ref } from "vue";

// export const useExpensesStore = defineStore('expenses', {
//     // state -> propriedades reativas
//     state: () => ({
//         valueTotalExpensesMonth: 0 as number,
//         valuePayExpenses: 0 as number,
//         valuePendingExpenses: 0 as number,
//         expenses: {} as object,
//         expensesMonth: {} as object,
//     }),

//     // actions -> methods
//     actions: {
//         setValueTotalExpensesMonth(value: number) {
//             this.valueTotalExpensesMonth = value;
//         },

//         setValuePayExpenses(value: number) {
//             this.valuePayExpenses = value;
//         },
//         /////////////////////////////////////
//         setValuePendingExpenses(value: number) {
//             this.valuePendingExpenses = value;
//         },
//         /////////////////////////////////////
//         setExpenses(data: object) {
//             this.expenses = data
//         },
//         /////////////////////////////////////
//         setExpensesMonth(data: object) {
//             this.expensesMonth = data;
//         },
// persist: true,
//     },


export const useExpensesStore = defineStore("expenses", () => {
    // state
    let valueTotalExpensesMonth: Ref<number> = ref(0);
    let valuePayExpenses: Ref<number> = ref(0);
    let valuePendingExpenses: Ref<number> = ref(0);
    let expenses = reactive({});
    let expensesMonth = reactive({});

    // getters


    // actions
    function setValueTotalExpensesMonth(value: number) {
        valueTotalExpensesMonth.value = value;
    }
    function setValuePayExpenses(value: number) {
        valuePayExpenses.value = value;
    }
    function setValuePendingExpenses(value: number) {
        valuePendingExpenses.value = value;
    }
    function setExpenses(value: Lancamentos) {
        expenses = value;
    }
    function setExpensesMonth(value: object) {
        expensesMonth = value;
    }

    return {
        valueTotalExpensesMonth,
        valuePayExpenses,
        valuePendingExpenses,
        expenses,
        expensesMonth,
        setValueTotalExpensesMonth,
        setValuePayExpenses,
        setValuePendingExpenses,
        setExpenses,
        setExpensesMonth,
    };
});