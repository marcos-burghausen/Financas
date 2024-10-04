import { defineStore } from "pinia";
import { ref } from "vue";

import type { Wallets } from "@/types/wallets";

export const useWalletsStore = defineStore("wallets", () => {

    const walletsData = ref(
        localStorage.getItem("walletsData")
            ? JSON.parse(localStorage.getItem("walletsData") as string)
            : "");


    function setWalletsData(wallets: Wallets): void {
            walletsData.value = wallets;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
    }

    function setWallets(wallets: Wallets) {
        walletsData.value.wallets = wallets;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
    }
    
    function setSaldoInicial(saldoInicial: number) {
        walletsData.value.saldoInicial = saldoInicial;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
    }

    function setMesReferencia(mesReferencia: string) {
        walletsData.value.mes_ano_referencia = mesReferencia;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
    }

    return {
        walletsData,
        setWalletsData,
        setWallets,
        setSaldoInicial,
        setMesReferencia
    };
});