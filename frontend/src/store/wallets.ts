import http from "@/services/http";
import type { Wallet, WalletData } from "@/types";
import { defineStore } from "pinia";
import { ref, type Ref } from "vue";

export const useWalletsStore = defineStore("wallets", () => {
    const getDefaultWalletData = (): WalletData => ({
        contas: [],
        cartoes: [],
        contas_names: [],
        saldo_inicial: 0,
        saldo_atual: 0,
        categories: [],
    });

    const walletsData: Ref<WalletData> = ref(getDefaultWalletData());

    function setWalletsData(wallets: WalletData): void {
        walletsData.value = {
            contas: wallets?.contas ?? [],
            cartoes: wallets?.cartoes ?? [],
            contas_names: wallets?.contas_names ?? [],
            saldo_inicial: wallets?.saldo_inicial ?? 0,
            saldo_atual: wallets?.saldo_atual ?? 0,
            categories: wallets?.categories ?? [],
        };
        localStorage.setItem("walletsData", JSON.stringify(walletsData.value));
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

    function loadFromSession(): void {
        const stored = localStorage.getItem("walletsData");
        if (stored) {
            try {
                walletsData.value = JSON.parse(stored);
            } catch {
                console.warn("Erro ao carregar carteiras");
            }
        }
    }

    function clear(): void {
        walletsData.value = getDefaultWalletData();
        localStorage.removeItem("walletsData");
    }

    async function saveWallet(walletData: Wallet): Promise<any> {
        try {
            const response = await http.post("/wallet", walletData);
            setWalletsData(response.data.wallets);
            return response.data;
        } catch (error) {
            console.error("Erro ao salvar a carteira:", error);
            throw error;
        }
    }

    return {
        walletsData,
        setWalletsData,
        setContas,
        setSaldoInicial,
        loadFromSession,
        clear,
        saveWallet,
    };
});