import { defineStore } from "pinia";

export const useDataStore = defineStore("data", {
    state: () => ({
        totalCreditCard: 0 as number,
        totalBalance: 0 as number,

    }),

    actions: {
        setTotalCreditCard(data: number) {
            this.totalCreditCard = data;
        },

        setTotalBalance(data: number) {
            this.totalBalance = data;
        },
    },
    // persist: true,

});