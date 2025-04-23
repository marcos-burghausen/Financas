import type { Account, WalletData } from "@/types";
import { defineStore } from "pinia";
import { ref, type Ref } from "vue";

export const useWalletsStore = defineStore("wallets", () => {
    const walletsData: Ref<WalletData> = ref(
        localStorage.getItem("walletsData")
            ? JSON.parse(localStorage.getItem("walletsData") as string)
            : ""
    );


    function setWalletsData(wallets: WalletData): void {
        walletsData.value = wallets;
        localStorage.setItem("walletsData", JSON.stringify(wallets));
    }

    function setContas(wallets: Account[]): void {
        if (walletsData.value) {
        walletsData.value.contas = wallets;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
        }
    }
    
    function setSaldoInicial(saldoInicial: number): void {
        if (walletsData.value) {
            walletsData.value.saldoInicial = saldoInicial;
            localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
          }
    }

    function setMesReferencia(mesAnoReferencia: string) {
        if (walletsData.value) {
            walletsData.value.mesAnoReferencia = mesAnoReferencia;
            localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
          }
    }

    return {
        walletsData,
        setWalletsData,
        setContas,
        setSaldoInicial,
        setMesReferencia
    };
});