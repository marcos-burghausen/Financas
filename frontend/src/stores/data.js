// import { computed, reactive } from 'vue';
import { defineStore } from 'pinia';

export const userData = defineStore('data', {
    // state -> propriedades reativas
    state: () => ({
        user: null,
        totalExpenses: null,
        totalPayExpenses: null,
        totalPendingExpenses: null,
        totalReveues: null,
        totalCreditCard: null,
        totalBalance: null,
        expenses: null,

    }),

    // actions -> methods
    actions: {
        setUser(data) {
            this.user = data;
        },
        //////////////////////////////////////
        setTotalExpenses(data) {
            this.totalExpenses = data;
        },
        addValorTotalExpense(valor) {
            this.totalExpenses += valor;
        },
        decrementValorTotalExpense(valor) {
            this.totalExpenses -= valor;
        },
        /////////////////////////////////////
        setTotalPayExpenses(expenses) {
            for (let i = 0; i < expenses.length; i++) {
                if (expenses[i].status === 'PAGA') {
                    this.totalPayExpenses += expenses[i].valor;
                }
            }
        },
        addTotalPayExpenses(valor) {
            this.totalPayExpenses += valor;
        },
        decrementTotalPayExpenses(valor) {
            this.totalPayExpenses -= valor;
        },
        /////////////////////////////////////
        setTotalPendingExpenses(expenses) {
            for (let i = 0; i < expenses.length; i++) {
                if (expenses[i].status === 'AGUARDANDO') {
                    this.totalPendingExpenses += expenses[i].valor;
                }
            }
        },
        addTotalPendingExpenses(valor) {
            this.totalPendingExpenses += valor;
        },
        decrementTotalPendingExpenses(valor) {
            this.totalPendingExpenses -= valor;
        },
        /////////////////////////////////////
        setTotalReveues(data) {
            this.totalReveues = data;
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
        setExpenses(data) {
            this.expenses = data
        },
        addExpense(release) {
            this.expenses.push(release);
        },



    },

    // getters -> propriedades computadas
    getters: {
        getUserName() {
            return this.userName;
        },
        getTotalExpenses() {
            return this.totalExpenses;
        },
        getTotalReveues() {
            return this.totalReveues;
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