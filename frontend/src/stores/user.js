import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';

export const useUserStore = defineStore("user", {
    //state
    state: () => ({
        user: null

    }),


    //getters


    //actions
    actions: {
        setUser(data) {
            this.user = data;
        }
    }
})