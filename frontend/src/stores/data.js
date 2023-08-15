// import { computed, reactive } from 'vue';
import { defineStore } from 'pinia';

export const userData = defineStore('data', {
    // state -> propriedades reativas
    // const dataUser = reactive({ data:{}});
    state: () => ({
        userName: '',
        totalExpenses: '',
        totalReveues: '',
        totalCreditCard: '',
        totalBalance: '',

    }),
    
    // actions -> methods
    // function defineData(data) {
    //         dataUser['data'] = data
    //     }
    actions: {
        setUserName(data) {
            this.userName = data;
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
    },
    persist: true,

    // return {
    //     dataUser,
    //     defineData,
    //     getData
    // }
        

})