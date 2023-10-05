import { defineStore } from 'pinia';

export const userData = defineStore('data', {
    // state -> propriedades reativas
    state: () => ({
        valueTotalExpensesMonth: 0 as number,
        valuePayExpenses: 0 as number,
        valuePendingExpenses: 0 as number,
        expenses: {} as object,
        expensesMonth: {} as object,
        valueTotalRevenuesMonth: 0 as number,
        valueReceivedRevenues: 0 as number,
        valuePendingRevenues: 0 as number,
        totalCreditCard: 0 as number,
        totalBalance: 0 as number,
        revenues: {} as object,
        revenuesMonth: {} as object,
        routerCurrent: null,

    }),

    // actions -> methods
    actions: {
        setValueTotalExpensesMonth(value: number) {
            this.valueTotalExpensesMonth = value;
        },

        setValuePayExpenses(value: number) {
            this.valuePayExpenses = value;
        },
        /////////////////////////////////////
        setValuePendingExpenses(value: number) {
            this.valuePendingExpenses = value;
        },
        /////////////////////////////////////
        setExpenses(data: object) {
            this.expenses = data
        },
        /////////////////////////////////////
        setExpensesMonth(data: object) {
            this.expensesMonth = data;
        },





        /////////////////////////////////////
        setValueTotalRevenuesMonth(value: number) {
            this.valueTotalRevenuesMonth = value;
        },
        /////////////////////////////////////
        setValueReceivedRevenues(value: number) {
            this.valueReceivedRevenues = value;
        },
        /////////////////////////////////////
        setValuePendingRevenues(value: number) {
            this.valuePendingRevenues = value;
        },
        setRevenues(data: object) {
            this.revenues = data
        },
        setRevenuesMonth(valor: object) {
            this.revenuesMonth = valor;
        },




        /////////////////////////////////////
        setTotalCreditCard(data: number) {
            this.totalCreditCard = data;
        },
        /////////////////////////////////////
        setTotalBalance(data: number) {
            this.totalBalance = data;
        },

        /////////////////////////////////////


        // setRouterCurrent(router) {
        //     this.routerCurrent = router;
        // }


    },

    // getters -> propriedades computadas
    persist: true,

})