// import { computed, reactive } from 'vue';
import { defineStore } from 'pinia';

export const userData = defineStore('data', {
    // state -> propriedades reativas
    // const dataUser = reactive({ data:{}});
    state: () => ({
        user: '',
        totalExpenses: '',
        totalReveues: '',
        totalCreditCard: '',
        totalBalance: '',
        expenses: '',

    }),

    // actions -> methods
    // function defineData(data) {
    //         dataUser['data'] = data
    //     }
    actions: {
        setUser(data) {
            this.user = data;
        },
        setTotalExpenses(data) {
            this.totalExpenses = data;
        },
        setTotalReveues(data) {
            this.totalReveues = data;
        },
        setTotalCreditCard(data) {
            this.totalCreditCard = data;
        },
        setTotalBalance(data) {
            this.totalBalance = data;
        },
        setExpenses(data) {
            this.expenses = data
        },
        addExpense(release) {
            this.expenses.push(release);
        },
        addValor(valor) {
            this.totalExpenses += valor;
        },
        decrementValor(valor) {
            this.totalExpenses -= valor;
        }

    },

    // getters -> propriedades computadas
    // const getData = computed(() => {
    //         return dataUser
    //     })
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

    // return {
    //     dataUser,
    //     defineData,
    //     getData
    // }


})