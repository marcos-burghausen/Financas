import { defineStore } from "pinia";
import { ref } from "vue";

import type { Wallets } from "@/types/wallets";

export const useWalletsStore = defineStore("wallets", () => {

    const walletsData = ref(
        localStorage.getItem("walletsData")
            ? JSON.parse(localStorage.getItem("walletsData") as string)
            : "");


    function setWalletsData(wallets: Array<Wallets>): void {
        walletsData.value = {
            wallets
        };
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
    }

    function setWallets(wallets) {
        walletsData.value.wallets = wallets;
    }

    return {
        walletsData,
        setWalletsData,
        setWallets
    };
});