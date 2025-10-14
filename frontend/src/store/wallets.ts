import http from '@/services/http';
import type { Wallet, WalletData } from "@/types";
import { defineStore } from "pinia";
import { ref, type Ref } from "vue";

export const useWalletsStore = defineStore("wallets", () => {
    const walletsData: Ref<WalletData> = ref(
        localStorage.getItem("walletsData")
            ? JSON.parse(localStorage.getItem("walletsData") as string)
            : ""
    );


    function setWalletsData(wallets: WalletData): void {
        walletsData.value = {
            contas: wallets?.contas ?? walletsData.value.contas,
            cartoes: wallets?.cartoes ?? walletsData.value.cartoes,
            contas_names: wallets?.contas_names ?? walletsData.value.contas_names,
            saldo_inicial: wallets?.saldo_inicial ?? walletsData.value.saldo_inicial,
            saldo_atual: wallets?.saldo_atual ?? walletsData.value.saldo_atual,
            categories: wallets?.categories ?? walletsData.value.categories,
        };
        localStorage.setItem("walletsData", JSON.stringify( walletsData.value));
    }

    function setContas(wallets: Wallet[]): void {
        if (walletsData.value) {
        walletsData.value.contas = wallets;
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
        }
    }
    
    function setSaldoInicial(saldoInicial: number): void {
        if (walletsData.value) {
            walletsData.value.saldo_inicial = saldoInicial;
            localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
          }
    }

    async function saveWallet(walletData: Wallet): Promise<any> {
      try {
        const response = await http.post('/wallet', walletData);
        console.log(response);
        setWalletsData(response.data.wallets);
        return response.data;
      } catch (error) {
        console.error('Erro ao salvar a carteira:', error);
      }
    }

    return {
        walletsData,
        setWalletsData,
        setContas,
        setSaldoInicial,
        saveWallet,
    };
});