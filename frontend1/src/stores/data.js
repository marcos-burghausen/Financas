import { defineStore } from 'pinia';

export const userData = defineStore('data', {
    // state -> propriedades reativas
    state: () => ({
        user: null,
        valueTotalExpensesMonth: null,
        valuePayExpenses: null,
        valuePendingExpenses: null,
        expenses: null,
        expensesMonth: null,
        valueTotalRevenuesMonth: null,
        valueReceivedRevenues: null,
        valuePendingRevenues: null,
        totalCreditCard: null,
        totalBalance: null,
        revenues: null,
        revenuesMonth: null,
        routerCurrent: null,

    }),

    // actions -> methods
    actions: {
        setUser(data) {
            this.user = data;
        },
        ////////////////////////////////////
        setValueTotalExpensesMonth(value) {
            this.valueTotalExpensesMonth = value;
        },

        setValuePayExpenses(value) {
            this.valuePayExpenses = value;
        },
        /////////////////////////////////////
        setValuePendingExpenses(value) {
            this.valuePendingExpenses = value;
        },
        /////////////////////////////////////
        setExpenses(data) {
            this.expenses = data
        },
        /////////////////////////////////////
        setExpensesMonth(data) {
            this.expensesMonth = data;
        },





        /////////////////////////////////////
        setValueTotalRevenuesMonth(value) {
            this.valueTotalRevenuesMonth = value;
        },
        /////////////////////////////////////
        setValueReceivedRevenues(value) {
            this.valueReceivedRevenues = value;
        },
        /////////////////////////////////////
        setValuePendingRevenues(value) {
            this.valuePendingRevenues = value;
        },
        setRevenues(data) {
            this.revenues = data
        },
        setRevenuesMonth(valor) {
            this.revenuesMonth = valor;
        },




        /////////////////////////////////////
        setTotalCreditCard(data) {
            this.totalCreditCard = data;
        },
        /////////////////////////////////////
        setTotalBalance(data) {
            this.totalBalance = data;
        },

        /////////////////////////////////////


        setRouterCurrent(router) {
            this.routerCurrent = router;
        }


    },

    // getters -> propriedades computadas
    getters: {
        getUserName() {
            return this.user.name;
        },
        getTotalExpenses() {
            return this.totalExpenses;
        },
        getTotalRevenues() {
            return this.totalRevenues;
        },
        getTotalCreditCard() {
            return this.totalCreditCard;
        },
        getTotalBalance() {
            return this.totalBalance;
        },
        getExpenses() {
            return this.expenses;
        },
    },
    persist: true,

})